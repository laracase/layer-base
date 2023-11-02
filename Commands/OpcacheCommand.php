<?php

namespace Layer\Base\Commands;

class OpcacheCommand extends BaseCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'opcache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache the framework bootstrap files';

    /**
     * Execute the console command.
     *
     * @return bool
     */
    public function handle()
    {
        $this->components->info('Caching the framework bootstrap files');

        collect([
            'config' => fn() => $this->callSilent('config:cache') == 0,
            'routes' => fn() => $this->callSilent('route:cache') == 0,
        ])->each(fn($task, $description) => $this->components->task($description, $task));

        $this->newLine();
    }
}
