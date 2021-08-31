<?php

namespace DirectoryTree\Authorization;

use Illuminate\Database\Eloquent\Model;
use DirectoryTree\Authorization\Traits\ClearsCachedPermissions;
use DirectoryTree\Authorization\Traits\HasRoles;
use DirectoryTree\Authorization\Traits\HasUsers;

class Permission extends Model
{
    use HasRoles, HasUsers, ClearsCachedPermissions;

    /**
     * The fillable model attributes.
     *
     * @var array
     */
    protected $fillable = ['name', 'label'];
}
