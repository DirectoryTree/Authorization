<?php

namespace DirectoryTree\Authorization;

class PermissionPivot extends Pivot
{
    /**
     * Flush the permissions relation on attach / detach.
     *
     * @var string
     */
    protected static $flushingRelation = 'permissions';
}
