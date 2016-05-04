<?php

namespace Larapacks\Authorization\Tests\Stubs;

use Illuminate\Foundation\Auth\User as BaseUser;
use Larapacks\Authorization\Traits\UserRolesTrait;

class User extends BaseUser
{
    use UserRolesTrait;

    protected $fillable = [
        'name',
    ];
}
