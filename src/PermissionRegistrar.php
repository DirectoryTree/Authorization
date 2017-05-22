<?php

namespace Larapacks\Authorization;

use PDOException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Access\Gate;

class PermissionRegistrar
{
    /**
     * PermissionRegistrar constructor.
     *
     * @param Gate $gate
     */
    public function __construct(Gate $gate)
    {
        $this->gate = $gate;
    }

    /**
     * Registers permissions into the gate.
     *
     * @return void
     */
    public function register()
    {
        // Dynamically register permissions with Laravel's Gate.
        foreach ($this->getPermissions() as $permission) {
            $closure = ($permission->hasClosure() ? $permission->closure : function ($user) use ($permission) {
                return $user->hasPermission($permission);
            });

            $this->gate->define($permission->name, $closure);
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
        } catch (PDOException $e) {
            // We catch PDOExceptions here in case the developer
            // hasn't migrated authorization tables yet.
        }

        return [];
    }

    /**
     * Returns a new permission model instance.
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    protected function getPermissionsModel()
    {
        return Authorization::permission();
    }
}
