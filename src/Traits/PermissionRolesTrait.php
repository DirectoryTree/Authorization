<?php

namespace Larapacks\Authorization\Traits;

use Larapacks\Authorization\Authorization;
use SuperClosure\Exception\ClosureUnserializationException;

trait PermissionRolesTrait
{
    use HasRolesTrait, SerializesClosures;

    /**
     * A permission may have many users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        $model = get_class(Authorization::user());

        return $this->belongsToMany($model);
    }

    /**
     * Determines whether the current permission contains a closure.
     *
     * @return bool
     */
    public function hasClosure()
    {
        return $this->closure ? true : false;
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
            $closure = $this->serializeClosure($closure);
        }

        $this->attributes['closure'] = $closure;
    }

    /**
     * Accessor for the permission closure attribute.
     *
     * @param string $closure
     *
     * @return \Closure|null
     */
    public function getClosureAttribute($closure)
    {
        try {
            return $this->unserializeClosure($closure);
        } catch (ClosureUnserializationException $e) {
            //
        }
    }
}
