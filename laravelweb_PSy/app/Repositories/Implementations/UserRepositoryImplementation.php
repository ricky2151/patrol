<?php

namespace App\Repositories\Implementations;

use App\Repositories\Contracts\UserRepositoryContract;
use App\Models\User;
use Carbon\Carbon;

class UserRepositoryImplementation extends BaseRepositoryImplementation implements UserRepositoryContract  {

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function getShiftsThatCanBeScanned() {
        $shiftsToday = $this->model->shifts()
        ->where('date',Carbon::today()->format('Y-m-d'))
        ->get()->map(function($item)
        {
            $last_scan = $item->histories()->latest('scan_time')->first();
            unset($last_scan['created_at']);
            unset($last_scan['updated_at']);
            return [
                'id' => $item['id'],
                'room' => $item['room']['name'],
                'time_start' => $item['time']['start'],
                'time_end' => $item['time']['end'],
                'date' => $item['date'],
                'countScanned' => $item->histories()->get()->count(), 
                'last_scan' => $last_scan,
            ];
        });
        
        $shiftsToday = $shiftsToday->filter(function($value, $key){
            return $value != null;
        });
        

        $shiftsYesterday = $this->model->shifts()
        ->where('date',Carbon::yesterday()->format('Y-m-d'))
        ->get()->map(function($item)
        {
            $last_scan = $item->histories()->latest('scan_time')->first();
            unset($last_scan['created_at']);
            unset($last_scan['updated_at']);
            if(strtotime($item['time']['start']) > strtotime($item['time']['end']))
            {
                return [
                    'id' => $item['id'],
                    'room' => $item['room']['name'],
                    'time_start' => $item['time']['start'],
                    'time_end' => $item['time']['end'],
                    'date' => $item['date'],
                    'countScanned' => $item->histories()->get()->count(), 
                    'last_scan' => $last_scan,
                ];
            }
        });
        $shiftsYesterday = $shiftsYesterday->filter(function($value, $key){
            return $value != null;
        });
        
        
        $result = $shiftsToday->concat($shiftsYesterday);

        return $result;
    }

    public function viewMyHistoryScan($id) {
        //dd($this->shifts()->get());
        $shifts = $this->model->shifts()->where('id',$id)->get()->map(function($item)
        {
            return [
                'shift_id' => $item['id'],
                'histories' => $item->histories()->get()->map(function($item){
                    return [
                        'id' => $item['id'],
                        'status_node_id' => $item['status_node_id'],
                        'status_node_name' => $item->status_node()->get()[0]['name'],
                        'message' => $item['message'],
                        'scan_time' => $item['scan_time'],
                        'photos' => $item->photos()->get()->map(function($item) {
                            return [
                                'id' => $item['id'],
                                'url' => $item['url'],
                                'photo_time' => $item['photo_time'],
                            ];
                        }),
                    ];
                }),
            ];
        });
        return $shifts;
    }
}