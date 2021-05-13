<?php

namespace Larapacks\Authorization;

use Illuminate\Database\Eloquent\Relations\Pivot as BaseModel;
use Larapacks\Authorization\Traits\FlushesLoadedRelations;

abstract class Pivot extends BaseModel
{
    use FlushesLoadedRelations;
}
