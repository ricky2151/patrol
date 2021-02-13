<?php
namespace App\Services\Implementations;

use App\Services\Contracts\RoleServiceContract;
use App\Repositories\Contracts\RoleRepositoryContract as RoleRepo;
use App\Exceptions\StoreDataFailedException;

class RoleServiceImplementation implements RoleServiceContract {
    protected $roleRepo;
    public function __construct(RoleRepo $roleRepo)
    {
        $this->roleRepo = $roleRepo;
    }

    public function get() {
        return $this->roleRepo->allOrder('id', 'desc');
    }

}