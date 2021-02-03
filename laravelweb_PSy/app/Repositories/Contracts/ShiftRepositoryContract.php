<?php

namespace App\Repositories\Contracts;

interface ShiftRepositoryContract 
{
    public function getTime($id);

    public function getRoom($id);
}