<?php
namespace App\Services\Implementations;

use App\Services\Contracts\BuildingServiceContract;
use App\Repositories\Contracts\BuildingRepositoryContract as BuildingRepo;
use App\Exceptions\StoreDataFailedException;

class BuildingServiceImplementation implements BuildingServiceContract {
    protected $buildingRepo;
    public function __construct(BuildingRepo $buildingRepo)
    {
        $this->buildingRepo = $buildingRepo;
    }

    public function get() {
        return $this->buildingRepo->allOrder('id', 'desc');
    }

    public function store($input) {
        return $this->buildingRepo->store($input);
    }

    public function update($input, $id) {
        return $this->buildingRepo->update($input, $id);
    }

    public function delete($id) {
        return $this->buildingRepo->delete($id);
    }
}