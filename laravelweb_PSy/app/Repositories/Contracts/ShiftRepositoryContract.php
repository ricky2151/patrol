<?php

namespace App\Repositories\Contracts;

interface ShiftRepositoryContract 
{
    public function getTime($id);

    public function getRoom($id);

    public function getHistoryScan($id);

    public function getShiftsNotAssign();

    public function getWithFormat();

    public function getTodayWithFormat();

    public function backupRun();

    public function removeShiftsExceptToday();

}