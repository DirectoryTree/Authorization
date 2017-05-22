<?php

namespace Larapacks\Authorization;

use Illuminate\Contracts\Auth\Access\Gate;
use Larapacks\Authorization\Commands\CreateRole;
use Larapacks\Authorization\Commands\CreatePermission;
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
        // The configuration path.
        $config = __DIR__.'/Config/config.php';

        // The migrations path.
        $migrations = __DIR__.'/Migrations/';

        // Set the configuration and migrations to publishable.
        $this->publishes([
            $migrations => database_path('migrations'),
            $config     => config_path('authorization.php'),
        ], 'authorization');

        // Merge the configuration.
        $this->mergeConfigFrom($config, 'authorization');

        // Register the permissions.
        (new PermissionRegistrar($gate))->register();

        // Register authorization commands.
        $this->commands([CreateRole::class, CreatePermission::class]);
    }
}
