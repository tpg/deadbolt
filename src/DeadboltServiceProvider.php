<?php

namespace TPG\Deadbolt;

use Illuminate\Support\ServiceProvider;

class DeadboltServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publish();
    }

    protected function publish(): void
    {
        $this->publishes([
            __DIR__.'/../config/deadbolt.php' => config_path('deadbolt.php'),
        ], 'deadbolt');

        $this->app->bind('deadbolt.facade', function () {
            return new Deadbolt($this->app['config']->get('deadbolt'));
        });
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/deadbolt.php', 'deadbolt');
    }
}
