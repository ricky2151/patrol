<?php

namespace App\Repositories\Implementations;

use App\Repositories\Contracts\ShiftRepositoryContract;
use App\Models\Shift;

class ShiftRepositoryImplementation extends BaseRepositoryImplementation implements ShiftRepositoryContract  {
    protected $model;

    public function __construct(Shift $model)
    {
        $this->model = $model;
    }

    public function getTime($id) {
        return $this->model->find($id)->time;
    }

    public function getRoom($id) {
        return $this->model->find($id)->room;
    }
}