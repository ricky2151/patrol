<?php
namespace App\Services\Implementations;

use App\Services\Contracts\TimeServiceContract;
use App\Repositories\Contracts\TimeRepositoryContract as TimeRepo;
use App\Exceptions\StoreDataFailedException;

class TimeServiceImplementation implements TimeServiceContract {
    protected $timeRepo;
    public function __construct(TimeRepo $timeRepo)
    {
        $this->timeRepo = $timeRepo;
    }

    public function get() {
        $data = $this->timeRepo->allOrder('id', 'desc');
        for($i = 0;$i<count($data);$i++) {
            $data[$i]['name'] = $data[$i]['start'] . '-' . $data[$i]['end'];
        }
        return $data;
        
    }

    public function store($input) {
        return $this->timeRepo->store($input);
    }

    public function update($input, $id) {
        return $this->timeRepo->update($input, $id);
    }

    public function delete($id) {
        return $this->timeRepo->delete($id);
    }
}