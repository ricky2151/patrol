<?php

namespace App\Repositories\Implementations;

use App\Repositories\Contracts\BuildingRepositoryContract;
use App\Models\Building;

class BuildingRepositoryImplementation extends BaseRepositoryImplementation implements BuildingRepositoryContract  {
    public function __construct(Building $builder)
    {
        $this->builder = $builder;
    }
}