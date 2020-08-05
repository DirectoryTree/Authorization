<?php

namespace Larapacks\Authorization;

use Larapacks\Authorization\Traits\FlushesLoadedRelations;
use Illuminate\Database\Eloquent\Relations\Pivot as BaseModel;

abstract class Pivot extends BaseModel
{
    use FlushesLoadedRelations;
}
