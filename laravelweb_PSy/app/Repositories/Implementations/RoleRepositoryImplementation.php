<?php

namespace App\Repositories\Implementations;

use App\Repositories\Contracts\RoleRepositoryContract;
use App\Models\Role;

class RoleRepositoryImplementation extends BaseRepositoryImplementation implements RoleRepositoryContract  {
    public function __construct(Role $builder)
    {
        $this->builder = $builder;
    }
}