<?php

namespace App\Services\Contracts;

interface UserServiceContract {
    public function getShiftsThatCanBeScanned();

    public function getMasterDataSubmitScan();

    public function viewMyHistoryScan($id);

    public function submitScan($message, $statusNodeId, $id, $dateTimeNow, $photos);
}