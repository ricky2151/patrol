<?php

namespace App\Services\Contracts;

interface RoomServiceContract {
    public function get();

    public function store($input);

    public function update($input, $id);

    public function delete($id);

    public function getWithoutFormat();

    public function find($id);
    
}