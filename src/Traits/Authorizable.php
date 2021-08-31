<?php

namespace DirectoryTree\Authorization\Traits;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use DirectoryTree\Authorization\Authorization;

trait Authorizable
{
    use HasRoles, HasPermissions, AssociatesPermissions;

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

        $this->loadMissing('roles');

        return $role instanceof Model
            ? $this->roles->contains($role)
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
        $roles = Collection::make($roles);

        if ($roles->isEmpty()) {
            return false;
        }

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
        return Collection::make($roles)->filter(function ($role) {
            return $this->hasRole($role);
        })->isNotEmpty();
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
            // If we've been given a string, then we can
            // assume its the permissions name. We will
            // attempt to fetch it from the database.
            $permission = Authorization::permission()->whereName($permission)->first();
        }

        if (! $permission instanceof Model) {
            return false;
        }

        // Here we will check if the user has been granted
        // explicit this permission explicitly. If so, we
        // can return here. No further check is needed.
        if ($this->permissions()->find($permission->getKey())) {
            return true;
        }

        // Otherwise, we'll determine their permission by gathering
        // the roles that the permission belongs to and checking
        // if the user is a member of any of the fetched roles.
        $roles = $permission->roles()->get()->map(function ($role) {
            return $role->getKey();
        });

        return $this->roles()
                ->whereIn($this->roles()->getRelatedPivotKeyName(), $roles)
                ->count() > 0;
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
        $permissions = Collection::make($permissions);

        if ($permissions->isEmpty()) {
            return false;
        }

        return $permissions->filter(function ($permission) {
            return $this->hasPermission($permission);
        })->count() === $permissions->count();
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
        return Collection::make($permissions)->filter(function ($permission) {
            return $this->hasPermission($permission);
        })->isNotEmpty();
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
     * Determine if the user does not have all of the given permissions.
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
