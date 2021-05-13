<?php

namespace Larapacks\Authorization\Traits;

use Larapacks\Authorization\Authorization;
use Larapacks\Authorization\RolePivot;

trait HasRoles
{
    /**
     * The belongsToMany roles relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Authorization::roleModel())->using(RolePivot::class);
    }
}
