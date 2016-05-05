<?php

namespace Larapacks\Authorization\Traits;

use SuperClosure\Exception\ClosureUnserializationException;
use SuperClosure\Serializer;

trait PermissionRolesTrait
{
    use HasRolesTrait;

    /**
     * A permission may have many users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        $model = config('authorization.user');

        return $this->belongsToMany($model);
    }

    /**
     * Determines whether the current permission contains a closure.
     *
     * @return bool
     */
    public function hasClosure()
    {
        try {
            if ($this->closure) return true;
        } catch (ClosureUnserializationException $e) {
            //
        }

        return false;
    }

    /**
     * Mutator for the permission closure attribute.
     *
     * @param \Closure|string $closure
     *
     * @return string
     */
    public function setClosureAttribute($closure)
    {
        if ($closure instanceof \Closure) {
            $closure = (new Serializer())->serialize($closure);
        }

        $this->attributes['closure'] = $closure;
    }

    /**
     * Accessor for the permission closure attribute.
     *
     * @param string $closure
     *
     * @return \Closure
     */
    public function getClosureAttribute($closure)
    {
        return (new Serializer())->unserialize($closure);
    }
}
