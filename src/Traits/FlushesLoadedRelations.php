<?php

namespace DirectoryTree\Authorization\Traits;

use DirectoryTree\Authorization\Pivot;

trait FlushesLoadedRelations
{
    /**
     * The relation to flush on creation / modification / deletion.
     *
     * @var string
     */
    protected static $flushingRelation;

    /**
     * The "boot" method of the model.
     *
     * @return void
     */
    public static function bootFlushesLoadedRelations()
    {
        static::saved(function (Pivot $pivot) {
            $pivot->pivotParent->unsetRelation(static::$flushingRelation);
        });

        static::deleted(function (Pivot $pivot) {
            $pivot->pivotParent->unsetRelation(static::$flushingRelation);
        });
    }
}
