<?php

namespace Larapacks\Authorization\Traits;

trait HasPermissionsTrait
{
    /**
     * A model may have multiple permissions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        $model = config('authorization.permission');

        return $this->belongsToMany($model);
    }
}
