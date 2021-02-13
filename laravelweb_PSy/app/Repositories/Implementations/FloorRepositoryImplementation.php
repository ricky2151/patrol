<?php

namespace App\Repositories\Implementations;

use App\Repositories\Contracts\FloorRepositoryContract;
use App\Models\Floor;

class FloorRepositoryImplementation extends BaseRepositoryImplementation implements FloorRepositoryContract  {
    public function __construct(Floor $builder)
    {
        $this->builder = $builder;
    }
}