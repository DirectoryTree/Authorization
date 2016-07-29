<?php

namespace Larapacks\Authorization\Traits;

use Larapacks\Authorization\Authorization;

trait HasPermissionsTrait
{
    /**
     * A model may have multiple permissions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        $model = get_class(Authorization::permission());

        return $this->belongsToMany($model);
    }
}
