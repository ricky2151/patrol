<?php

namespace App\Repositories\Contracts;

interface UserRepositoryContract 
{
    public function getShiftsThatCanBeScanned();

    public function viewMyHistoryScan($id);

}