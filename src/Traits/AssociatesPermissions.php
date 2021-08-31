<?php

namespace Larapacks\Authorization\Traits;

use Illuminate\Database\Eloquent\Model;
use Larapacks\Authorization\Authorization;

trait AssociatesPermissions
{
    /**
     * Grant the given permission(s).
     *
     * Returns a collection of granted permission(s).
     *
     * @param string|string[]|Model|Model[] $permissions
     *
     * @return Model|Model[]|\Illuminate\Support\Collection
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

        return collect($permissions)->map(function ($permission) {
            return $this->grant($permission) instanceof Model ? $permission : false;
        })->filter();
    }

    /**
     * Revoke the given permission(s).
     *
     * Returns a collection of revoked permissions.
     *
     * @param string|string[]|Model|Model[] $permissions
     *
     * @return Model|Model[]|\Illuminate\Support\Collection
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

        return collect($permissions)->map(function ($permission) {
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
