<?php

namespace Larapacks\Authorization;

use PDOException;
use Illuminate\Contracts\Auth\Access\Gate;

class PermissionRegistrar
{
    /**
     * The auth gate.
     *
     * @var Gate
     */
    protected $gate;

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
            $this->gate->define($permission->name, function ($user) use ($permission) {
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
            return Authorization::permission()->get();
        } catch (PDOException $e) {
            // We catch PDOExceptions here in case the developer
            // hasn't migrated authorization tables yet.
        }

        return [];
    }
}
