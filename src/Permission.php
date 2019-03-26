<?php

namespace Larapacks\Authorization;

use Illuminate\Database\Eloquent\Model;
use Larapacks\Authorization\Traits\HasUsers;
use Larapacks\Authorization\Traits\HasRoles;

class Permission extends Model
{
    use HasRoles, HasUsers;

    /**
     * The fillable model attributes.
     *
     * @var array
     */
    protected $fillable = ['name', 'label'];
}
