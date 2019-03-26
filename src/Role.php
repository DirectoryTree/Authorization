<?php

namespace Larapacks\Authorization;

use Illuminate\Database\Eloquent\Model;
use Larapacks\Authorization\Traits\ManagesPermissions;

class Role extends Model
{
    use ManagesPermissions;

    /**
     * The fillable model attributes.
     *
     * @var array
     */
    protected $fillable = ['name', 'label'];
}
