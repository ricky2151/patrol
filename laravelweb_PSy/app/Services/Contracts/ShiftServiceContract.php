<?php

namespace App\Services\Contracts;

interface ShiftServiceContract {
    public function getDashboardData();
    public function getHistoryScan($id);
    public function getShiftsNotAssign();
    public function get();
    public function getToday();
    public function removeAndBackup();

}