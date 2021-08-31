<?php

namespace DirectoryTree\Authorization\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

trait ManagesPermissions
{
    use HasUsers, HasPermissions, AssociatesPermissions;

    /**
     * Determine if the role has the given permission.
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
     * Determine if the current role has the given permissions.
     *
     * @param string|array $permissions
     *
     * @return bool
     */
    public function hasPermissions($permissions)
    {
        return Collection::make(Arr::wrap($permissions))->filter(function ($permission) {
            return $this->hasPermission($permission);
        })->count() === count($permissions);
    }

    /**
     * Determine if the current role has any of the given permissions.
     *
     * @param array $permissions
     *
     * @return bool
     */
    public function hasAnyPermissions($permissions)
    {
        return Collection::make(Arr::wrap($permissions))->filter(function ($permission) {
            return $this->hasPermission($permission);
        })->count() > 0;
    }
}
