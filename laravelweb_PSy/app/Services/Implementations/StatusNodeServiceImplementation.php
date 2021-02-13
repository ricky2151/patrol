<?php
namespace App\Services\Implementations;

use App\Services\Contracts\StatusNodeServiceContract;
use App\Repositories\Contracts\StatusNodeRepositoryContract as StatusNodeRepo;
use App\Exceptions\StoreDataFailedException;

class StatusNodeServiceImplementation implements StatusNodeServiceContract {
    protected $statusNodeRepo;
    public function __construct(StatusNodeRepo $statusNodeRepo)
    {
        $this->statusNodeRepo = $statusNodeRepo;
    }

    public function get() {
        return $this->statusNodeRepo->allOrder('id', 'desc');
    }

    public function store($input) {
        return $this->statusNodeRepo->store($input);
    }

    public function update($input, $id) {
        return $this->statusNodeRepo->update($input, $id);
    }

    public function delete($id) {
        return $this->statusNodeRepo->delete($id);
    }
}