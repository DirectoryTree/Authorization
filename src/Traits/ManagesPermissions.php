<?php

namespace Larapacks\Authorization\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

trait ManagesPermissions
{
    use HasPermissions, HasUsers, AssociatesPermissions;

    /**
     * Returns true / false if the current role has the specified permission.
     *
     * @param string|Model $permission
     *
     * @return bool
     */
    public function hasPermission($permission)
    {
        if (empty($permission)) {
            return false;
        }

        $this->loadMissing('permissions');

        return $permission instanceof Model
            ? $this->permissions->contains($permission)
            : $this->permissions->contains('name', $permission);
    }

    /**
     * Returns true / false if the current role has the specified permissions.
     *
     * @param string|array $permissions
     *
     * @return bool
     */
    public function hasPermissions($permissions)
    {
        return collect(Arr::wrap($permissions))->filter(function ($permission) {
            return $this->hasPermission($permission);
        })->count() === count($permissions);
    }

    /**
     * Returns true / false if the current role has any of the specified permissions.
     *
     * @param array $permissions
     *
     * @return bool
     */
    public function hasAnyPermissions($permissions)
    {
        return collect(Arr::wrap($permissions))->filter(function ($permission) {
            return $this->hasPermission($permission);
        })->count() > 0;
    }
}
