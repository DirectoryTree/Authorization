<?php

namespace Larapacks\Authorization\Traits;

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
        if (empty($role)) {
            return false;
        }

        return $role instanceof Model
            ? $this->roles->find($role->getKey()) instanceof Model
            : $this->roles->contains('name', $role);
    }

    /**
     * Determine if the user has all of the given roles.
     *
     * @param array $roles
     *
     * @return bool
     */
    public function hasRoles($roles)
    {
        $this->load('roles');

        $roles = collect($roles);

        return $roles->filter(function ($role) {
            return $this->hasRole($role);
        })->count() === $roles->count();
    }

    /**
     * Determine if the user has any of the given roles.
     *
     * @param array $roles
     *
     * @return bool
     */
    public function hasAnyRoles($roles)
    {
        $this->load('roles');

        return collect($roles)->filter(function ($role) {
            return $this->hasRole($role);
        })->count() > 0;
    }

    /**
     * Determine if the user has the given permission.
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
     * Determine if the user has all of the given permissions.
     *
     * @param array|\Illuminate\Support\Collection $permissions
     *
     * @return bool
     */
    public function hasPermissions($permissions)
    {
        $permissions = collect($permissions);

        return $permissions->filter(function ($permission) {
            return $this->hasPermission($permission);
        })->count() ===  $permissions->count();
    }

    /**
     * Determine if the user has any of the permissions.
     *
     * @param array|\Illuminate\Support\Collection $permissions
     *
     * @return bool
     */
    public function hasAnyPermissions($permissions)
    {
        $permissions = collect($permissions);

        return $permissions->filter(function ($permission) {
            return $this->hasPermission($permission);
        })->count() > 0;
    }

    /**
     * Determine if the user does not have the given permission.
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
     * Determine if the user does not have all of the given permissions
     *
     * @param array|\Illuminate\Support\Collection $permissions
     *
     * @return bool
     */
    public function doesNotHavePermissions($permissions)
    {
        return ! $this->hasPermissions($permissions);
    }

    /**
     * Determine if the user does not have any of the given permissions.
     *
     * @param array|\Illuminate\Support\Collection $permissions
     *
     * @return bool
     */
    public function doesNotHaveAnyPermissions($permissions)
    {
        return ! $this->hasAnyPermissions($permissions);
    }
}
