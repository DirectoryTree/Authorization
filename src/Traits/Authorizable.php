<?php

namespace Larapacks\Authorization\Traits;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Larapacks\Authorization\Authorization;

trait Authorizable
{
    use HasRoles, HasPermissions;

    /**
     * Assign the given role to the user.
     *
     * @param string|Model $role
     *
     * @return Model
     */
    public function assignRole($role)
    {
        if (! $role instanceof Model) {
            $role = Authorization::role()->whereName($role)->firstOrFail();
        }

        return $this->roles()->save($role);
    }

    /**
     * Removes the specified role from the user.
     *
     * @param string|Model $role
     *
     * @return int
     */
    public function removeRole($role)
    {
        if (! $role instanceof Model) {
            $role = Authorization::role()->whereName($role)->firstOrFail();
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
            return $this->roles->contains('name', $role);
        }

        if ($role instanceof Model) {
            return $this->roles->find($role->getKey()) instanceof Model;
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
        if (! $roles instanceof Collection) {
            $roles = collect($roles);
        }

        $this->load('roles');

        return $roles->filter(function ($role) {
            return $this->hasRole($role);
        })->count() === $roles->count();
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
        if (! $roles instanceof Collection) {
            $roles = collect($roles);
        }

        $this->load('roles');

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

        if ($permission instanceof Model) {
            // We'll first check to see if the user was given this explicit permission.
            if ($this->permissions()->find($permission->getKey())) {
                return true;
            }

            // Otherwise, we'll determine their permission by gathering
            // the roles that the permission belongs to and checking
            // if the user is a member of any of the roles.
            $roles = $permission->roles()->get()->map(function ($role) {
                return $role->getKey();
            });

            // Determine if the user is a member of any of the permissions roles.
            return $this->roles()
                    ->whereIn($this->roles()->getRelatedPivotKeyName(), $roles)
                    ->count() > 0;
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
        if (! $permissions instanceof Collection) {
            $permissions = collect($permissions);
        }

        return $permissions->filter(function ($permission) {
            return $this->hasPermission($permission);
        })->count() ===  $permissions->count();
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
        if (! $permissions instanceof Collection) {
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
        return ! $this->hasPermission($permission);
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
        return ! $this->hasPermissions($permissions);
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
        return ! $this->hasAnyPermissions($permissions);
    }
}
