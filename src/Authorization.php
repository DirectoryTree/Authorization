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

        return class_exists($model) ? new $model() : null;
    }

    /**
     * Returns the role model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function role()
    {
        $model = config('authorization.role');

        return class_exists($model) ? new $model() : null;
    }

    /**
     * Returns the permission model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function permission()
    {
        $model = config('authorization.permission');

        return class_exists($model) ? new $model() : null;
    }
}
