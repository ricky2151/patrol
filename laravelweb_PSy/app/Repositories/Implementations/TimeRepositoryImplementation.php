<?php

namespace App\Repositories\Implementations;

use App\Repositories\Contracts\TimeRepositoryContract;
use App\Models\Time;

class TimeRepositoryImplementation extends BaseRepositoryImplementation implements TimeRepositoryContract  {
    public function __construct(Time $builder)
    {
        $this->builder = $builder;
    }
}