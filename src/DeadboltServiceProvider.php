<?php

namespace TPG\Deadbolt;

use Illuminate\Support\ServiceProvider;

class DeadboltServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publish();
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/deadbolt.php', 'deadbolt');

        $this->app->bind('deadbolt.facade', function () {
            return new Deadbolt(app('config')['deadbolt']);
        });
    }

    protected function publish()
    {
        $this->publishes([
            __DIR__.'/../config/deadbolt.php' => config_path('deadbolt.php'),
        ], 'deadbolt');
    }
}
