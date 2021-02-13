<?php
namespace App\Services\Implementations;

use App\Services\Contracts\RoomServiceContract;
use App\Repositories\Contracts\RoomRepositoryContract as RoomRepo;
use App\Exceptions\StoreDataFailedException;
use Illuminate\Support\Arr;

class RoomServiceImplementation implements RoomServiceContract {
    protected $roomRepo;
    public function __construct(RoomRepo $roomRepo)
    {
        $this->roomRepo = $roomRepo;
    }

    public function get() {
        $data = $this->roomRepo->datatableWith(['floor:id,name', 'building:id,name', 'gateway:id,name'])
            ->orderBy('id', 'desc')->get();
        
        $data = collect($data)->map(function ($item) {
            $item = Arr::add($item, 'floor_name', $item['floor']['name']);
            $item = Arr::add($item, 'building_name', $item['building']['name']);
            $item = Arr::add($item, 'gateway_name', $item['gateway']['name']);
            return Arr::except($item, ['floor', 'building', 'gateway']);
        });
        return $data->toArray();
    }

    public function store($input) {
        return $this->roomRepo->store($input);
    }

    public function update($input, $id) {
        return $this->roomRepo->update($input, $id);
    }

    public function delete($id) {
        return $this->roomRepo->delete($id);
    }

    public function getWithoutFormat() {
        $data = $this->roomRepo->datatableWith(['floor:id,name', 'building:id,name', 'gateway:id,name'])
            ->orderBy('id', 'desc')->get();
        return $data;
    }

    public function find($id) {
        return $this->roomRepo->find($id);
    }
}