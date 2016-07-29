<?php

namespace Larapacks\Authorization\Traits;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Larapacks\Authorization\Authorization;

trait RolePermissionsTrait
{
    /**
     * Returns the administrators name.
     *
     * @return string
     */
    public static function getAdministratorName()
    {
        return 'administrator';
    }

    /**
     * A role may have many users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        $model = get_class(Authorization::user());

        return $this->belongsToMany($model);
    }

    /**
     * A role may be given various permissions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        $model = get_class(Authorization::permission());

        return $this->belongsToMany($model);
    }

    /**
     * Returns true / false if the current role is an administrator.
     *
     * @return bool
     */
    public function isAdministrator()
    {
        return $this->name === self::getAdministratorName();
    }

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
            return $this->permissions->contains($permission);
        }

        return false;
    }

    /**
     * Returns true / false if the current role has the specified permissions.
     *
     * @param array $permissions
     *
     * @return bool
     */
    public function hasPermissions($permissions)
    {
        if (!is_array($permissions)) {
            $permissions = [$permissions];
        }

        $permissions = collect($permissions);

        $count = $permissions->count();

        return $permissions->filter(function ($permission) {
            return $this->hasPermission($permission);
        })->count() === $count;
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
        if (!is_array($permissions)) {
            $permissions = [$permissions];
        }

        $permissions = collect($permissions);

        return $permissions->filter(function ($permission) {
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
            // Verify if the role already has the permission.
            if ($this->hasPermission($permissions->name)) {
                return $permissions;
            }

            if ($this->permissions()->save($permissions) instanceof Model) {
                return $permissions;
            }
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
            if (!$this->hasPermission($permissions->name)) {
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
