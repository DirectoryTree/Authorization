<?php

namespace Larapacks\Authorization;

class Authorization
{
    /**
     * Indicates if Authorization migrations will be run.
     *
     * @var bool
     */
    public static $runsMigrations = true;

    /**
     * Indicates if Authorization will register permissions into the gate.
     *
     * @var bool
     */
    public static $registersInGate = true;

    /**
     * The user model class name.
     *
     * @var string
     */
    public static $userModel = 'App\User';

    /**
     * The role model class name.
     *
     * @var string
     */
    public static $roleModel = 'Larapacks\Authorization\Role';

    /**
     * The permission model class name.
     *
     * @var string
     */
    public static $permissionModel = 'Larapacks\Authorization\Permission';

    /**
     * Get the user model class name.
     *
     * @return string
     */
    public static function userModel()
    {
        return static::$userModel;
    }

    /**
     * Get a new user model instance.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function user()
    {
        return new static::$userModel;
    }

    /**
     * Get the role model class name.
     *
     * @return string
     */
    public static function roleModel()
    {
        return static::$roleModel;
    }

    /**
     * Get a new role model instance.
     *
     * @return Role
     */
    public static function role()
    {
        return new static::$roleModel;
    }

    /**
     * Get the permission model class name.
     *
     * @return string
     */
    public static function permissionModel()
    {
        return static::$permissionModel;
    }

    /**
     * Get a new permission model instance.
     *
     * @return Permission
     */
    public static function permission()
    {
        return new static::$permissionModel;
    }
}
