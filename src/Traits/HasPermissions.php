<?php

namespace Larapacks\Authorization\Traits;

use Larapacks\Authorization\Authorization;
use Larapacks\Authorization\PermissionPivot;

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
