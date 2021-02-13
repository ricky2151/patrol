<?php

namespace App\Repositories\Contracts;

interface UserRepositoryContract 
{
    public function getShiftsThatCanBeScanned();

    public function viewMyHistoryScan($id);

    public function insertShifts($id, $shifts);

    public function updateShifts($id, $shifts);

    public function deleteShifts($id, $shifts);

    public function checkHaveShifts($id, $shifts);

    public function findWithShifts($id);

    public function getShifts($id);

}