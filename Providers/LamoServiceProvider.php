<?php

namespace Layer\Base\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;
use Layer\Base\Kernel\Http;

/*
 * Lamo 模块的基类
 */
abstract class LamoServiceProvider extends ServiceProvider
{
    protected Http $kernel;

    protected Schedule $schedule;

    // @todo 可以根据 env 加载
    protected array $routes = ['*'];

    public function __construct($app)
    {
        parent::__construct($app);
        /** @var Http $kernel */
        $this->kernel = $app->make(Kernel::class);
        $this->loadAssets();

        $this->booted(function () {
            if (method_exists($this, 'loadCommands')) {
                $this->loadCommands();
            }
        });

        // schedule是singleton，并且在booted才instance
        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
            $this->schedule = $schedule;
            if (method_exists($this, 'loadSchedules')) {
                $this->loadSchedules();
            }
        });
    }

    /**
     * 加载 routes 和 migrations
     * @return void
     */
    public function loadAssets(): void
    {
        if (method_exists($this, 'loadModule') && $modulePath = $this->loadModule()) {
            // var_dump($modulePath);
            // register module route
            // 使用mergeConfigFrom方法，可以使用缓存
            if (!$this->app->routesAreCached()) {
                $routesPath = $modulePath . "/routes/";
                if (is_dir($routesPath)) {
                    $routes = scandir($routesPath);
                    foreach ($routes as $route) {
                        if ($this->shouldLoadRoute($route)) {
                            $this->loadRoutesFrom($routesPath . $route);
                        }
                    }
                }
            }

            // register module database
            if ($this->app->runningInConsole()) {
                // migrations
                $migrationPath = $modulePath . "/database/migrations";
                if (is_dir($migrationPath)) {
                    $this->loadMigrationsFrom($migrationPath);
                }
            }

            // load module config
            if (!$this->app->configurationIsCached()) {
                $configPath = $modulePath . "/config/";
                if (is_dir($configPath)) {
                    $configs = scandir($configPath);
                    foreach ($configs as $config) {
                        if (!in_array($config, ['.', '..'])) {
                            $this->mergeConfigFrom($configPath . $config, pathinfo($config)['filename']);
                        }
                    }
                }
            }
        }
    }

    /**
     * 分模块加载，比如只加载open，mine的，不加载admin的
     * @param string $route
     * @return bool
     */
    protected function shouldLoadRoute(string $route): bool
    {
        if (!in_array($route, ['.', '..']) && ($this->routes[0] === '*' || in_array(substr($route, 0, -4), $this->routes))) {
            return true;
        }
        return false;
    }
}
