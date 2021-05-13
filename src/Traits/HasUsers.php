<?php

namespace Larapacks\Authorization\Traits;

use Larapacks\Authorization\Authorization;
use Larapacks\Authorization\UserPivot;

trait HasUsers
{
    /**
     * The belongsToMany users relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(Authorization::userModel())->using(UserPivot::class);
    }
}
