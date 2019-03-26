<?php

namespace Larapacks\Authorization;

use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthorizationServiceProvider extends ServiceProvider
{
    /**
     * Register authorization permissions.
     *
     * @param Gate $gate
     */
    public function boot(Gate $gate)
    {
        if ($this->app->runningInConsole()) {
            $this->registerMigrations();

            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'authorization-migrations');
        }

        // Register the permissions.
        if (Authorization::$registersInGate) {
            (new PermissionRegistrar($gate))->register();
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
