<?php

declare(strict_types=1);

namespace TPG\Deadbolt;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use TPG\Deadbolt\Console\InstallCommand;

class DeadboltServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishConfig();
        $this->publishMigrations();

        if ($this->app->runningInConsole() && ! file_exists(config_path('permissions.php'))) {
            $this->commands([
                InstallCommand::class,
            ]);
        }
    }

    protected function publishConfig(): void
    {
        $this->publishes([
            __DIR__.'/../config/deadbolt.php' => config_path('deadbolt.php'),
        ], 'config');
    }

    protected function publishMigrations(): void
    {
        $this->publishes([
            __DIR__.'/../database/add_deadbolt_permissions_column.php' => $this->getMigrationFilename('add_deadbolt_permissions_column'),
        ], 'migrations');
    }

    protected function getMigrationFilename(string $migrationName): string
    {
        $timestamp = date('Y_m_d_His');

        return collect(
            glob($this->app->databasePath().'/migrations/*'.$migrationName.'.php'))
            ->push($this->app->databasePath().'/migrations/'.$timestamp.'_'.$migrationName.'.php')
            ->first();
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/deadbolt.php', 'deadbolt');

        $this->app->bind('deadbolt.facade', function () {
            return new DeadboltService($this->app['config']->get('deadbolt'));
        });

        $this->app->singleton('permissions', function () {
            return $this->buildPermissionCollection();
        });
    }

    protected function buildPermissionCollection(): Collection
    {
        return collect(config('deadbolt.permissions'));
    }
}
