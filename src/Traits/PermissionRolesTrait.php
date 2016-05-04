<?php

namespace Larapacks\Authorization\Traits;

trait PermissionRolesTrait
{
    use HasRolesTrait;

    /**
     * A permission may have many users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        $model = config('authorization.user');

        return $this->belongsToMany($model);
    }
}
