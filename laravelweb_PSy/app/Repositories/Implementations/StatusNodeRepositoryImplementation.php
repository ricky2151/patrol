<?php

namespace App\Repositories\Implementations;

use App\Repositories\Contracts\StatusNodeRepositoryContract;
use App\Models\StatusNode;

class StatusNodeRepositoryImplementation extends BaseRepositoryImplementation implements StatusNodeRepositoryContract  {
    public function __construct(StatusNode $model)
    {
        $this->model = $model;
    }
}