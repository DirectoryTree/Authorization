<?php

namespace DirectoryTree\Authorization;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthorizationServiceProvider extends ServiceProvider
{
    /**
     * Register authorization permissions.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->registerMigrations();

            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'authorization-migrations');
        }

        // Register the permissions.
        if (Authorization::$registersInGate) {
            app(PermissionRegistrar::class)->register();
        }
    }

    /**
     * Register Authorization migration files.
     *
     * @return void
     */
    protected function registerMigrations()
    {
        if (Authorization::$runsMigrations) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }
    }
}
