<?php

namespace Layer\Base\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Env;
use Layer\Base\Kernel\Http;
use Layer\Base\Traits\UtilTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BaseCommand extends Command
{
    use UtilTrait;

    protected bool $xray = false;
    protected function getApp($appPath): Application
    {
        $this->clearEnv();
        /** @var Application $app */
        $app = require $appPath . "/bootstrap/app.php";
        /** @var Http $kernel */
        $kernel = $app->make(Kernel::class);
        $kernel->bootstrap();
        return $app;
    }

    protected function clearEnv(): void
    {
        $repository = Env::getRepository();
        $repository->clear('APP_ENV');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($this->xray) {
            app('xray')->boot($this);
        }
        $result = parent::execute($input, $output);
        if ($this->xray) {
            dd(app('xray')->collect());
        }
        return $result;
    }
}
