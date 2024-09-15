<?php

namespace Easy\Eav;

use Illuminate\Support\ServiceProvider;
use Easy\Eav\Console\Commands\MakeEav;

class EavServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../database/migrations/' => database_path('migrations')
        ], 'eav-migration');


        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeEav::class
            ]);
        }
    }
}
