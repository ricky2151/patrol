<?php

namespace App\Repositories\Implementations;

use App\Repositories\Contracts\ShiftRepositoryContract;
use App\Models\Shift;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ShiftRepositoryImplementation extends BaseRepositoryImplementation implements ShiftRepositoryContract  {

    public function __construct(Shift $builder)
    {
        $this->builder = $builder;
    }

    public function getTime($id) {
        return $this->builder->find($id)->time;
    }

    public function getRoom($id) {
        return $this->builder->find($id)->room;
    }

    public function getHistoryScan($id) {
        return $this->builder->with(['room', 'time', 'histories.photos', 'histories.status_node'])->where('id', $id)->first();
    }

    public function getShiftsNotAssign() {
        $shiftNotAssign = DB::table('shifts')
        ->whereDate('date', '>', Carbon::now())
        ->join('rooms', 'rooms.id','shifts.room_id')
        ->join('times', 'times.id','shifts.time_id')
        ->select(
            [
                'shifts.date',
                'rooms.name as room_name',
                'rooms.id as room_id',
                'times.id as time_id',
                DB::raw('times.start || "-" || times.end as time_start_end'),
            ]
        )
        ->orderBy('date', 'DESC')
        ->orderBy('time_start_end', 'DESC')
        ->orderBy('rooms.name','ASC')
        ->orderBy('message', 'ASC')
        ->get();

        return $shiftNotAssign;
    }

    public function getWithFormat() {
        return  $this->builder->with(['room:id,name', 'user:id,name', 'time:id,start,end'])->withCount('histories')->orderBy('id','desc')->get()->toArray();
    }

    public function getTodayWithFormat() {
        return  $this->builder->with(['room:id,name', 'user:id,name', 'time:id,start,end'])->where('date',date('Y-m-d'))->withCount('histories')->orderBy('id','desc')->get()->toArray();
    }

    public function backupRun() {
        Artisan::call("backup:run");
        $resultArtisanCall = Artisan::output();
        if(strpos($resultArtisanCall, "Backup failed"))
        {
            throw new DeleteDataFailedException('Delete Data Failed : Failed to backup before delete !');
        }
        else
        {
            return true;
        }
    }

    public function removeShiftsExceptToday() {
        $shifts = $this->builder->get();


        $shifts->map(function($itemShift) {
            $deleteThisData = false;
            //check time is right
            $timeNow = Carbon::now()->timezone('Asia/Jakarta')->format('H:i:s');
            $dateNow = Carbon::now()->timezone('Asia/Jakarta')->format('Y-m-d');    
            $dateYesterday = date('Y-m-d', strtotime('-1 day', strtotime($dateNow)));

            $timeShift = $itemShift->time()->get()[0];
            $startTimeShift = $timeShift['start'];
            $endTimeShift = $timeShift['end'];
            $dateShift = $itemShift->date;

            $verifyTimeNowIsGreater = checkRangeTimeShift($timeNow, $dateNow, $dateYesterday, $dateShift, $startTimeShift, $endTimeShift, "greater");

            if($verifyTimeNowIsGreater == true) //if time now is greater than this shift, then delete
            {
                $deleteThisData = true;
            }
            
            if($deleteThisData)
            {
                $itemShift->delete();
            }
            
        });
        return true;
    }
    
}