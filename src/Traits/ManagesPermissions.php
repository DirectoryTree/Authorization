<?php

namespace Larapacks\Authorization\Traits;

use Illuminate\Support\Collection;
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
        if (is_string($permission)) {
            $permission = $this->permissions()->whereName($permission)->first();
        }

        if ($permission instanceof Model) {
            return $this->permissions()->get()->contains($permission);
        }

        return false;
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
        if (! is_array($permissions)) {
            $permissions = [$permissions];
        }

        return collect($permissions)->filter(function ($permission) {
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
        if (! is_array($permissions)) {
            $permissions = [$permissions];
        }

        return collect($permissions)->filter(function ($permission) {
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
     * @return Model|Collection
     */
    public function grant($permissions)
    {
        if ($permissions instanceof Model) {
            if ($this->hasPermission($permissions)) {
                return $permissions;
            }

            return $this->permissions()->save($permissions);
        } elseif (is_array($permissions)) {
            $permissions = collect($permissions);
        }

        return $permissions->filter(function ($permission) {
            if ($permission instanceof Model) {
                return $this->grant($permission);
            }

            return false;
        });
    }

    /**
     * Revoke the given permission to a role.
     *
     * Returns a collection of revoked permissions.
     *
     * @param Model|array $permissions
     *
     * @return Model|Collection
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
            if ($permission instanceof Model) {
                return $this->revoke($permission);
            }

            return false;
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
