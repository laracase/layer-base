<?php

namespace Layer\Base\Kernel;

use Illuminate\Foundation\Console\Kernel;

class Console extends Kernel
{
    protected function commands(): void
    {
        $this->app->terminate();
    }
}
