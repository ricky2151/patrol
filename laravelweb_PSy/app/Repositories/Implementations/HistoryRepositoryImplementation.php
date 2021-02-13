<?php

namespace App\Repositories\Implementations;

use App\Repositories\Contracts\HistoryRepositoryContract;
use App\Models\History;
use App\Models\Room;
use App\Exceptions\StoreDataFailedException;
use Illuminate\Support\Facades\DB;


class HistoryRepositoryImplementation extends BaseRepositoryImplementation implements HistoryRepositoryContract  {

    public function __construct(History $builder)
    {
        $this->builder = $builder;
    }

    public function insertPhotos($id, $photos) {
        try {
            $this->builder->find($id)->photos()->createMany($photos);
            return true;
        } catch (\Throwable $th) {
            throw new StoreDataFailedException("insert photos failed");
        }
        
    }

    public function getGraphData() {
        $thisyear = date("Y");
        $data = DB::table('histories')->select(DB::raw('strftime("%Y",shifts.date) as year, strftime("%m",shifts.date) as month, status_nodes.name as status_nodes, status_nodes.id as status_nodes_id, count(*) as count'))
        ->groupBy('month', 'status_nodes')
        ->where('year', $thisyear)
        ->join('status_nodes', 'status_node_id','status_nodes.id')
        ->join('shifts', 'shift_id','shifts.id')
        ->get();

        return $data;
    }

    public function getCurrentEvent() {
        
        $thisDay = date('Y-m-d');

        //get current event
        $totalRooms = Room::count();
        $currentEvent = History::with(["shift" => function($q) use ($thisDay){
            $q->where('shifts.date', '=', $thisDay);
        }])->where('scan_time', '!=', '')->limit($totalRooms)->with('shift.room:id,name')->get();

        return $currentEvent;
    }

}