<?php

namespace Larapacks\Authorization;

use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Database\Eloquent\Model;
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

        // The authorization tag.
        $tag = 'authorization';

        // Set the configuration and migrations to publishable.
        $this->publishes([
            $migrations => database_path('migrations'),
            $config     => config_path('authorization.php'),
        ], $tag);

        // Merge the configuration.
        $this->mergeConfigFrom($config, 'authorization');

        // Dynamically register permissions with Laravel's Gate.
        foreach ($this->getPermissions() as $permission) {
            $gate->define($permission->name, function ($user) use ($permission) {
                return $user->hasPermission($permission);
            });
        }
    }

    /**
     * Fetch the collection of site permissions.
     *
     * @return \Illuminate\Database\Eloquent\Collection|array
     */
    protected function getPermissions()
    {
        try {
            $model = $this->getPermissionsModel();

            if ($model instanceof Model) {
                return $model->with('roles')->get();
            }
        } catch (\PDOException $e) {
            // We catch PDOExceptions here in case the developer
            // hasn't migrated authorization tables yet.
        }

        return [];
    }

    /**
     * Returns a new permission model instance.
     *
     * @return \Illuminate\Database\Eloquent\Model|false
     */
    protected function getPermissionsModel()
    {
        $model = config('authorization.permission');

        if (class_exists($model)) {
            return new $model();
        }

        return false;
    }
}
