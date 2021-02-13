<?php

namespace App\Repositories\Implementations;

use App\Repositories\Contracts\RoomRepositoryContract;
use App\Models\Room;

class RoomRepositoryImplementation extends BaseRepositoryImplementation implements RoomRepositoryContract  {
    public function __construct(Room $builder)
    {
        $this->builder = $builder;
    }
}