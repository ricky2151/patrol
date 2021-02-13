<?php

namespace App\Repositories\Implementations;

use App\Repositories\Contracts\GatewayRepositoryContract;
use App\Models\Gateway;

class GatewayRepositoryImplementation extends BaseRepositoryImplementation implements GatewayRepositoryContract  {
    public function __construct(Gateway $builder)
    {
        $this->builder = $builder;
    }
}