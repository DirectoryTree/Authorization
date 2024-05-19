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
            $this->publishes([
                __DIR__.'/../database/migrations/create_authorization_tables.php' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_authorization_tables.php'),
            ], 'authorization-migrations');
        }

        // Register the permissions.
        if (Authorization::$registersInGate) {
            app(PermissionRegistrar::class)->register();
        }
    }
}
