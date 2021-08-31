<?php

namespace DirectoryTree\Authorization;

use Illuminate\Database\Eloquent\Relations\Pivot as BaseModel;
use DirectoryTree\Authorization\Traits\FlushesLoadedRelations;

abstract class Pivot extends BaseModel
{
    use FlushesLoadedRelations;
}
