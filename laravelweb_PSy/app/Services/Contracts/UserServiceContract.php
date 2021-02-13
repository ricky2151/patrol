<?php

namespace App\Services\Contracts;

interface UserServiceContract {
    public function getShiftsThatCanBeScanned();

    public function viewMyHistoryScan($id);

    public function submitScan($message, $statusNodeId, $id, $dateTimeNow, $photos);

    public function get();

    public function storeUserWithShifts($input);

    public function updateUserWithShifts($input, $id);

    public function findWithShifts($id);

    public function delete($id);
}