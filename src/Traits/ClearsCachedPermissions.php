<?php

namespace Larapacks\Authorization\Traits;

use Larapacks\Authorization\PermissionRegistrar;

trait ClearsCachedPermissions
{
    /**
     * Clears the permission cache upon saving and deleting permissions.
     *
     * @return void
     */
    public static function bootClearsCachedPermissions()
    {
        static::saved(function () {
            app(PermissionRegistrar::class)->flushCache();
        });

        static::deleted(function () {
            app(PermissionRegistrar::class)->flushCache();
        });
    }
}
