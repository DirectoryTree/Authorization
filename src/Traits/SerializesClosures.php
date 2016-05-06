<?php

namespace Larapacks\Authorization\Traits;

use SuperClosure\Serializer;

trait SerializesClosures
{
    /**
     * Serializes the specified closure.
     *
     * @param \Closure $closure
     *
     * @return string
     */
    public function serializeClosure(\Closure $closure)
    {
        return $this->getClosureSerializer()->serialize($closure);
    }

    /**
     * Unserializes a serialized closure.
     *
     * @param string $closure
     *
     * @return \Closure
     */
    public function unserializeClosure($closure)
    {
        return $this->getClosureSerializer()->unserialize($closure);
    }

    /**
     * Returns a new closure serializer instance.
     *
     * @return Serializer
     */
    public function getClosureSerializer()
    {
        return new Serializer();
    }
}
