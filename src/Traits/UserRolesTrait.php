<?php

namespace Larapacks\Authorization\Traits;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Larapacks\Authorization\Authorization;

trait UserRolesTrait
{
    use HasRolesTrait, HasPermissionsTrait;

    /**
     * Returns true / false if the current user is an administrator.
     *
     * @return bool
     */
    public function isAdministrator()
    {
        $role = $this->roles()->getRelated();

        return $this->hasRole($role::getAdministratorName());
    }

    /**
     * Assign the given role to the user.
     *
     * @param string|Model $role
     *
     * @return Model
     */
    public function assignRole($role)
    {
        if (!$role instanceof Model) {
            $role = $this->roles()->getRelated()->whereName($role)->firstOrFail();
        }

        return $this->roles()->save($role);
    }

    /**
     * Removes the specified role from the user.
     *
     * @param string|Model $role
     *
     * @return Model
     */
    public function removeRole($role)
    {
        if (!$role instanceof Model) {
            $role = $this->roles()->getRelated()->whereName($role)->firstOrFail();
        }

        return $this->roles()->detach($role);
    }

    /**
     * Determine if the user has the given role.
     *
     * @param string|Model $role
     *
     * @return bool
     */
    public function hasRole($role)
    {
        if (is_string($role)) {
            $role = $this->roles()->whereName($role)->first();
        }

        if ($role instanceof Model) {
            return $this->roles->contains($role);
        }

        return false;
    }

    /**
     * Returns true / false if the current user has the specified roles.
     *
     * @param array $roles
     *
     * @return bool
     */
    public function hasRoles($roles)
    {
        if (!$roles instanceof Collection) {
            $roles = collect($roles);
        }

        $roles = collect($roles);

        $count = $roles->count();

        return $roles->filter(function ($role) {
            return $this->hasRole($role);
        })->count() === $count;
    }

    /**
     * Returns true / false if the current user has any of the specified roles.
     *
     * @param array $roles
     *
     * @return bool
     */
    public function hasAnyRoles($roles)
    {
        if (!$roles instanceof Collection) {
            $roles = collect($roles);
        }

        $roles = collect($roles);

        return $roles->filter(function ($role) {
            return $this->hasRole($role);
        })->count() > 0;
    }

    /**
     * Determine if the user may perform the given permission.
     *
     * @param string|Model $permission
     *
     * @return bool
     */
    public function hasPermission($permission)
    {
        if (is_string($permission)) {
            // If we weren't given a permission model, we'll try to find it by name.
            $permission = Authorization::permission()->whereName($permission)->first();
        }

        if ($this->permissions->contains($permission)) {
            return true;
        }

        if ($permission instanceof Model) {
            $roles = $permission->roles;

            foreach ($roles as $role) {
                if ($this->hasRole($role)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Returns true / false if the current user
     * has the specified permissions.
     *
     * @param array|Collection $permissions
     *
     * @return bool
     */
    public function hasPermissions($permissions)
    {
        if (!$permissions instanceof Collection) {
            $permissions = collect($permissions);
        }

        $count = $permissions->count();

        return $permissions->filter(function ($permission) {
            return $this->hasPermission($permission);
        })->count() === $count;
    }

    /**
     * Returns true / false if the current user has
     * any of the specified permissions.
     *
     * @param array|Collection $permissions
     *
     * @return bool
     */
    public function hasAnyPermissions($permissions)
    {
        if (!$permissions instanceof Collection) {
            $permissions = collect($permissions);
        }

        return $permissions->filter(function ($permission) {
            return $this->hasPermission($permission);
        })->count() > 0;
    }

    /**
     * Returns true / false if the user does not have the specified permission.
     *
     * @param string|Model $permission
     *
     * @return bool
     */
    public function doesNotHavePermission($permission)
    {
        return !$this->hasPermission($permission);
    }

    /**
     * Returns true / false if all of the specified
     * permissions do not exist on the current user.
     *
     * @param array|Collection $permissions
     *
     * @return bool
     */
    public function doesNotHavePermissions($permissions)
    {
        return !$this->hasPermissions($permissions);
    }

    /**
     * Returns true / false if any of the specified
     * permissions do not exist on the current user.
     *
     * @param array|Collection $permissions
     *
     * @return bool
     */
    public function doesNotHaveAnyPermissions($permissions)
    {
        return !$this->hasAnyPermissions($permissions);
    }
}
