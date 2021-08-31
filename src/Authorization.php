<?php

namespace DirectoryTree\Authorization;

use DateTimeInterface;

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
     * The date when the permission cache expires.
     *
     * @var DateTimeInterface|null
     */
    public static $cacheExpiresAt;

    /**
     * Indicates if Authorization will cache permissions.
     *
     * @var bool
     */
    public static $cachesPermissions = true;

    /**
     * The cache key.
     *
     * @var string
     */
    public static $cacheKey = 'authorization.permissions';

    /**
     * The user model class name.
     *
     * @var string
     */
    public static $userModel = 'App\Models\User';

    /**
     * The role model class name.
     *
     * @var string
     */
    public static $roleModel = 'DirectoryTree\Authorization\Role';

    /**
     * The permission model class name.
     *
     * @var string
     */
    public static $permissionModel = 'DirectoryTree\Authorization\Permission';

    /**
     * Get or set when the permission cache expires.
     *
     * @param DateTimeInterface|null $date
     *
     * @return DateTimeInterface|static
     */
    public static function cacheExpiresIn(DateTimeInterface $date = null)
    {
        if (is_null($date)) {
            return static::$cacheExpiresAt ?? now()->addDay();
        }

        static::$cacheExpiresAt = $date;

        return new static;
    }

    /**
     * Get or set the cache key.
     *
     * @param string|null $cacheKey
     *
     * @return static|string
     */
    public static function cacheKey($cacheKey = null)
    {
        if (is_null($cacheKey)) {
            return static::$cacheKey;
        }

        static::$cacheKey = $cacheKey;

        return new static;
    }

    /**
     * Set the user model class name.
     *
     * @param $userModel
     */
    public static function useUserModel($userModel)
    {
        static::$userModel = $userModel;
    }

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
     * Set the role model class name.
     *
     * @param $roleModel
     */
    public static function useRoleModel($roleModel)
    {
        static::$roleModel = $roleModel;
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
     * Set the permission model class name.
     *
     * @param $permissionModel
     */
    public static function usePermissionModel($permissionModel)
    {
        static::$permissionModel = $permissionModel;
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

    /**
     * Configure Authorization to not register in the auth gate.
     *
     * @return static
     */
    public function disableGateRegistration()
    {
        static::$registersInGate = false;

        return new static;
    }

    /**
     * Configure Authorization to not cache permissions.
     *
     * @return static
     */
    public static function disablePermissionCache()
    {
        static::$cachesPermissions = false;

        return new static;
    }

    /**
     * Configure Authorization to not register its migrations.
     *
     * @return static
     */
    public static function ignoreMigrations()
    {
        static::$runsMigrations = false;

        return new static;
    }
}
