<?php

declare(strict_types=1);

namespace TPG\Deadbolt\Console;

use Illuminate\Console\Command;
use TPG\Deadbolt\DeadboltServiceProvider;

class InstallCommand extends Command
{
    protected $signature = 'deadbolt:install';

    protected $description = 'Install a few Deadbolt requirements';

    public function handle(): int
    {
        $this->call('vendor:publish', [
            '--provider' => DeadboltServiceProvider::class,
        ]);

        return 0;
    }
}
