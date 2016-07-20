<?php

namespace Larapacks\Authorization;

class Authorization
{
    /**
     * Returns the user model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function user()
    {
        $model = config('authorization.user');

        return new $model;
    }

    /**
     * Returns the role model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function role()
    {
        $model = config('authorization.role');

        return new $model;
    }

    /**
     * Returns the permission model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function permission()
    {
        $model = config('authorization.permission');

        return new $model;
    }
}
