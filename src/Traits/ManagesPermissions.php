<?php

namespace Larapacks\Authorization\Traits;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;

trait ManagesPermissions
{
    use HasPermissions, HasUsers;

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

        $this->load('permissions');

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

    /**
     * Grant the given permission to a role.
     *
     * Returns the granted permission(s).
     *
     * @param Model|array $permissions
     *
     * @return Model|\Illuminate\Support\Collection
     */
    public function grant($permissions)
    {
        if ($permissions instanceof Model) {
            return $this->hasPermission($permissions)
                ? $permissions
                : $this->permissions()->save($permissions);
        } elseif (is_array($permissions)) {
            $permissions = collect($permissions);
        }

        return $permissions->filter(function ($permission) {
            return $permission instanceof Model
                ? $this->grant($permission)
                : false;
        });
    }

    /**
     * Revoke the given permission to a role.
     *
     * Returns a collection of revoked permissions.
     *
     * @param Model|array $permissions
     *
     * @return Model|\Illuminate\Support\Collection
     */
    public function revoke($permissions)
    {
        if ($permissions instanceof Model) {
            if (! $this->hasPermission($permissions)) {
                return $permissions;
            }

            if ($this->permissions()->detach($permissions) === 1) {
                return $permissions;
            }
        } elseif (is_array($permissions)) {
            $permissions = collect($permissions);
        }

        return $permissions->filter(function ($permission) {
            return $permission instanceof Model
                ? $this->revoke($permission)
                : false;
        });
    }

    /**
     * Revokes all permissions on the current role.
     *
     * @return int
     */
    public function revokeAll()
    {
        return $this->permissions()->detach();
    }
}
