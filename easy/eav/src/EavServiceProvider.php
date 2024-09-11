<?php

namespace Easy\Eav;

use Illuminate\Support\ServiceProvider;
use Easy\Eav\Console\Commands\MakeEntity;
// use Easy\Eav\Contracts\EavMigrateInterface;
// use Easy\Eav\Models\Services\EavMigrate;

class EavServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // $this->app->singleton(EavMigrateInterface::class, EavMigrate::class);
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
                MakeEntity::class
            ]);
        }
    }
}
