<?php

namespace App\Services\Contracts;

interface StatusNodeServiceContract {
    public function get();

    public function store($input);

    public function update($input, $id);

    public function delete($id);
    
}