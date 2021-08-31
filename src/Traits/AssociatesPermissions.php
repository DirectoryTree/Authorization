<?php

namespace DirectoryTree\Authorization\Traits;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use DirectoryTree\Authorization\Authorization;

trait AssociatesPermissions
{
    /**
     * Grant the given permission(s).
     *
     * Returns a collection of granted permission(s).
     *
     * @param string|string[]|Model|Model[] $permissions
     *
     * @return Model|Model[]|Collection
     */
    public function grant($permissions)
    {
        if (is_string($permissions)) {
            $permissions = Authorization::permission()->whereName($permissions)->first();
        }

        if ($permissions instanceof Model) {
            return $this->hasPermission($permissions)
                ? $permissions
                : $this->permissions()->save($permissions);
        }

        return Collection::make($permissions)->map(function ($permission) {
            return $this->grant($permission) instanceof Model ? $permission : false;
        })->filter();
    }

    /**
     * Grant only the given permission(s), detaching any previous permission(s).
     *
     * @param string|string[]|Model|Model[] $permissions
     *
     * @return Model|Model[]|Collection
     */
    public function grantOnly($permissions)
    {
        $this->revokeAll();

        return $this->grant($permissions);
    }

    /**
     * Revoke the given permission(s).
     *
     * Returns a collection of revoked permissions.
     *
     * @param string|string[]|Model|Model[] $permissions
     *
     * @return Model|Model[]|Collection
     */
    public function revoke($permissions)
    {
        if (is_string($permissions)) {
            $permissions = Authorization::permission()->whereName($permissions)->first();
        }

        if ($permissions instanceof Model) {
            if (! $this->hasPermission($permissions)) {
                return $permissions;
            }

            $this->permissions()->detach($permissions);

            return $permissions;
        }

        return Collection::make($permissions)->map(function ($permission) {
            return $this->revoke($permission) instanceof Model ? $permission : false;
        })->filter();
    }

    /**
     * Revokes all permissions.
     *
     * Returns the number of revoked permissions.
     *
     * @return int
     */
    public function revokeAll()
    {
        $detached = $this->permissions()->detach();

        // When permissions are completely revoked, we must purge the
        // permissions relation so we do not get false positives on
        // any following permission checks during the same request.
        $this->unsetRelation('permissions');

        return $detached;
    }
}
