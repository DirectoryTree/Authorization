<?php

namespace DirectoryTree\Authorization\Traits;

use DirectoryTree\Authorization\Authorization;
use DirectoryTree\Authorization\RolePivot;

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
