<?php

namespace DirectoryTree\Authorization\Tests\Stubs;

use Illuminate\Foundation\Auth\User as BaseUser;
use DirectoryTree\Authorization\Traits\Authorizable;

class User extends BaseUser
{
    use Authorizable;

    protected $fillable = ['name'];
}
