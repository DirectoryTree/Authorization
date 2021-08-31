<?php

namespace DirectoryTree\Authorization\Traits;

use DirectoryTree\Authorization\Authorization;
use DirectoryTree\Authorization\UserPivot;

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
