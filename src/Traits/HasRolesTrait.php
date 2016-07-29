<?php

namespace Larapacks\Authorization\Traits;

use Larapacks\Authorization\Authorization;

trait HasRolesTrait
{
    /**
     * A model may have multiple roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        $model = get_class(Authorization::role());

        return $this->belongsToMany($model);
    }
}
