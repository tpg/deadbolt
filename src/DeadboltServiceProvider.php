<?php

namespace TPG\Deadbolt;

use Illuminate\Support\ServiceProvider;

class DeadboltServiceProvider extends ServiceProvider
{
    /**
     * Boot the package.
     */
    public function boot()
    {
        $this->publish();
    }

    /**
     * Publish the packages assets.
     */
    protected function publish(): void
    {
        $this->publishes([
            __DIR__.'/../config/deadbolt.php' => config_path('deadbolt.php'),
        ], 'deadbolt');
    }

    /**
     * Register anything needed into the container.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/deadbolt.php', 'deadbolt');

        $this->app->bind('deadbolt.facade', function () {
            return new DeadboltService($this->app['config']->get('deadbolt'));
        });
    }
}
