<?php
namespace App\Services\Implementations;

use App\Services\Contracts\GatewayServiceContract;
use App\Repositories\Contracts\GatewayRepositoryContract as GatewayRepo;
use App\Exceptions\StoreDataFailedException;

class GatewayServiceImplementation implements GatewayServiceContract {
    protected $gatewayRepo;
    public function __construct(GatewayRepo $gatewayRepo)
    {
        $this->gatewayRepo = $gatewayRepo;
    }

    public function get() {
        return $this->gatewayRepo->allOrder('id', 'desc');
    }

    public function store($input) {
        return $this->gatewayRepo->store($input);
    }

    public function update($input, $id) {
        return $this->gatewayRepo->update($input, $id);
    }

    public function delete($id) {
        return $this->gatewayRepo->delete($id);
    }
}