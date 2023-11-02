<?php

namespace Layer\Base\Commands;

use Layer\Base\Support\BaseCommand;

class OpcacheClearCommand extends BaseCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'opcache:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove the cached bootstrap files';

    /**
     * Execute the console command.
     *
     * @return bool
     */
    public function handle()
    {
        $this->components->info('Clearing optimize cached bootstrap files.');
        // 清除 opcache
        opcache_reset();

        collect([
            //'events' => fn() => $this->callSilent('event:clear') == 0,
            //'views' => fn() => $this->callSilent('view:clear') == 0,
            //'cache' => fn() => $this->callSilent('cache:clear') == 0,
            'route' => fn() => $this->callSilent('route:clear') == 0,
            'config' => fn() => $this->callSilent('config:clear') == 0,
            //'compiled' => fn() => $this->callSilent('clear-compiled') == 0,
        ])->each(fn($task, $description) => $this->components->task($description, $task));

        $this->newLine();
    }
}
