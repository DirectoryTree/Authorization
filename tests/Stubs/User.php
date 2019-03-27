<?php

namespace Larapacks\Authorization\Tests\Stubs;

use Illuminate\Foundation\Auth\User as BaseUser;
use Larapacks\Authorization\Traits\Authorizable;

class User extends BaseUser
{
    use Authorizable;

    protected $fillable = ['name'];
}
