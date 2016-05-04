<?php

namespace Larapacks\Authorization\Tests\Stubs;

use Illuminate\Database\Eloquent\Model;
use Larapacks\Authorization\Traits\RolePermissionsTrait;

class Role extends Model
{
    use RolePermissionsTrait;

    /**
     * The roles table.
     *
     * @var string
     */
    protected $table = 'roles';

    protected $fillable = [
        'name',
        'label',
    ];
}
