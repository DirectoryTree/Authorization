<?php

namespace Larapacks\Authorization\Traits;

use Larapacks\Authorization\Authorization;

trait HasRoles
{
    /**
     * The belongsToMany roles relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Authorization::roleModel());
    }
}
