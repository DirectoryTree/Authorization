<?php

namespace DirectoryTree\Authorization\Traits;

use DirectoryTree\Authorization\Authorization;
use DirectoryTree\Authorization\PermissionPivot;

trait HasPermissions
{
    /**
     * The belongsToMany permissions relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Authorization::permissionModel())->using(PermissionPivot::class);
    }
}
