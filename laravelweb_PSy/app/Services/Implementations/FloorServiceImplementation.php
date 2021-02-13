<?php
namespace App\Services\Implementations;

use App\Services\Contracts\FloorServiceContract;
use App\Repositories\Contracts\FloorRepositoryContract as FloorRepo;
use App\Exceptions\StoreDataFailedException;

class FloorServiceImplementation implements FloorServiceContract {
    protected $floorRepo;
    public function __construct(FloorRepo $floorRepo)
    {
        $this->floorRepo = $floorRepo;
    }

    public function get() {
        return $this->floorRepo->allOrder('id', 'desc');
    }

    public function store($input) {
        return $this->floorRepo->store($input);
    }

    public function update($input, $id) {
        return $this->floorRepo->update($input, $id);
    }

    public function delete($id) {
        return $this->floorRepo->delete($id);
    }
}