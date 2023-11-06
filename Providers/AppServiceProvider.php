<?php

namespace Layer\Base\Providers;

use Dotenv\Dotenv;
use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Layer\Base\Commands\OpcacheClearCommand;
use Layer\Base\Commands\OpcacheCommand;
use Layer\Base\Kernel\Console;
use Layer\Base\Kernel\Exception;
use Layer\Base\Kernel\Http;

/*
 * 应用的入口
 */
class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // 不要依赖其他库
//        $this->app->register(DatabaseServiceProvider::class);

        if ($this->app->runningInConsole()) {
            $this->commands([
                OpcacheCommand::class,
                OpcacheClearCommand::class,
            ]);
        }

        if (!$this->app->routesAreCached()) {
            $basePath = config('app.base_path');
            if ($this->app->environment('local') && $basePath) {
                Router::macro('setBasePath', function ($path) {
                    $this->updateGroupStack(['prefix' => $path]);
                });
                Route::setBasePath($basePath);
            }
        }

        // @todo 不要这一段，通过 laravel discover service 进行注入
//        $lamoRootPath = $this->app->basePath('lamo');
//        $modules = scandir($lamoRootPath);
//        foreach ($modules as $module) {
//            if (!in_array($module, ['.', '..', '.git', 'README.md'])) {
//                // register module service
//                $module = ucfirst($module);
//                $moduleServiceProvider = "\\Lamo\\$module\\Providers\\ServiceProvider";
//                if (class_exists($moduleServiceProvider)) {
//                    $this->app->register($moduleServiceProvider);
//                }
//
//                // register module event
//                $eventServiceProvider = "\\Lamo\\$module\\Providers\\EventServiceProvider";
//                if (class_exists($eventServiceProvider)) {
//                    // var_dump($eventServiceProvider);
//                    $this->app->register($eventServiceProvider);
//                }
//            }
//        }
    }

    public function register(): void
    {
        if (!$this->app->configurationIsCached()) {
            $this->app->afterLoadingEnvironment(function () {
                // load local env
                $localEnvFile = $this->app->environmentPath() . DIRECTORY_SEPARATOR . $this->app->environmentFile() . '.local';
                if (file_exists($localEnvFile)) {
                    Dotenv::create(Env::getRepository(), $this->app->environmentPath(), $this->app->environmentFile() . '.local')->safeLoad();
                }

                // load app_env 对应的env文件，如果有--env或APP_ENV，则不能重复加载
                $appEnvConfig = $this->app->environmentFile() . '.' . Env::get('APP_ENV');
                if ($this->app->environmentFile() !== $appEnvConfig && file_exists($this->app->environmentPath() . DIRECTORY_SEPARATOR . $appEnvConfig)) {
                    Dotenv::create(Env::getRepository(), $this->app->environmentPath(), $appEnvConfig)->safeLoad();
                }
            });
        };
        $this->app->singleton(HttpKernel::class, Http::class);

        $this->app->singleton(ConsoleKernel::class, Console::class);

        $this->app->singleton(ExceptionHandler::class, Exception::class);

        $this->app->register(RouteServiceProvider::class);
        // @notice 目前有问题
        $this->app->register(ConsoleServiceProvider::class);
    }
}
