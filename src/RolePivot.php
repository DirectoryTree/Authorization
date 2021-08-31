<?php

namespace DirectoryTree\Authorization;

class RolePivot extends Pivot
{
    /**
     * Flush the roles relation on attach / detach.
     *
     * @var string
     */
    protected static $flushingRelation = 'roles';
}
