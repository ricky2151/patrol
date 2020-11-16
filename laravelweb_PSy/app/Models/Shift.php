<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Carbon\Carbon;
use DB;


class Shift extends Model
{
    protected $fillable = ['user_id', 'room_id', 'time_id', 'date'];


    public static function index(){
        

        // $data = Shift::latest()->get();

        // $data = $data->map(function ($data) { 
        //     $data['date'] = Carbon::parse($data['date'])->format('d-m-Y');
        //     $data = Arr::add($data, 'room_name', $data['room']['name']);
        //     $data = Arr::add($data, 'status_node_name', $data['status_node']['name']);
        //     $data = Arr::add($data, 'user_name', $data['user']['name']);
        //     $data = Arr::add($data, 'time_start_end', $data['time']['start'] . ' - ' . $data['time']['end']);
        //     return Arr::except($data, ['room', 'status_node', 'time']);
        // });


        $data = DB::table('shifts')
        ->join('rooms', 'rooms.id','room_id')
        ->join('users', 'users.id','user_id')
        ->join('times', 'times.id','time_id')
        ->select(
            [
                //'histories.*',
                'shifts.id as id',
                'shifts.date as date',
                'rooms.name as room_name',
                'users.name as user_name',
                DB::raw('times.start || " - " || times.end as time_start_end'),
            ]
        )
        ->orderBy('date', 'DESC')
        ->orderBy('time_start_end', 'DESC')
        ->orderBy('rooms.name','ASC')
        ->orderBy('user_name', 'ASC')
        ->get();

        $data->map(function($item){
            $total_histories = Shift::find($item->id)->histories()->count();
            $item->total_histories = $total_histories;
        });

        return $data;
        

        
    }
    public static function indexToday(){
        
        $thisDay = date('Y-m-d');
        //ruangan, start time, end time, nama satpam, status, message
        $data = DB::table('shifts')->where('date', $thisDay)
        ->join('rooms', 'rooms.id','room_id')
        ->join('users', 'users.id','user_id')
        ->join('times', 'times.id','time_id')
        ->select(
            [
                'shifts.id as id',
                'rooms.id as room_id',
                'rooms.name as room_name',
                'users.name as user_name',
                DB::raw('times.start || " - " || times.end as time_start_end'),
            ]
        )->orderBy('rooms.name','ASC')
        ->orderBy('time_start_end', 'DESC')
        ->orderBy('user_name', 'ASC')
        ->get();

        $data->map(function($item){
            $total_histories = Shift::find($item->id)->histories()->count();
            $item->total_histories = $total_histories;
        });

  
        

        return $data;
    }

    
   
    public static function showGraph()
    {
        $thisyear = date("Y");
        $data = DB::table('histories')->select(DB::raw('strftime("%Y",shifts.date) as year, strftime("%m",shifts.date) as month, status_nodes.name as status_nodes, status_nodes.id as status_nodes_id, count(*) as count'))
        //$data = DB::table('shifts')->select(DB::raw('strftime("%Y",date) as year, strftime("%m",date) as month, status_nodes.name as status_nodes, count(*) as count'))
        ->groupBy('month', 'status_nodes')
        ->where('year', $thisyear)
        ->join('status_nodes', 'status_node_id','status_nodes.id')
        ->join('shifts', 'shift_id','shifts.id')
        ->get();
        // $data = Shift::latest()->get()->groupBy(function($d) {
        //     return Carbon::parse($d->date)->format('m');
        // });

        return $data;
        
    }
    public function getHistories()
    {
        $result = $this;
        $result['room_name'] = $result->room()->get()[0]['name'];
        $result['time_name'] = $result->time()->get()[0]['start'] . ' - ' . $result->time()->get()[0]['end'];
        $histories = $this->histories()->get();
        $histories = $histories->map(function ($item) {
            $item['status_node_name'] = $item->status_node()->get()[0]['name'];
            $item['photos'] = $item->photos()->get()->map(function($item) {
                return [
                    'id' => $item['id'],
                    'url' => $item['url'],
                    'photo_time' => $item['photo_time'],
                ];
            });
            //$item = collect($item)->put('photos', $item->photos()->get());
            //$item = collect($item)->put('status_node_name', $item->status_node()->get());
            return $item;
        });
        $result['histories'] = $histories;
        return $result;
    }
    public static function showSmallReport()
    {
        $thisMonth = date('m');
        $thisYear = date('Y');
        $thisDay = date('Y-m-d');
        $beforeThisMonth = date("m", strtotime("-1 months"));


        //get current event
        $totalRooms = Room::count();
        $currentEvent = History::with(["shift" => function($q) use ($thisDay){
            $q->where('shifts.date', '=', $thisDay);
        }])->where('scan_time', '!=', '')->limit($totalRooms)->with('shift.room:id,name')->get();
        
        //dd($currentEvent);
        
        $result = [];
        //$result['securePercentageThisMonth'] = $securePercentageThisMonth;
        //$result['differentSecureBetweenMonth'] = $differentSecureBetweenMonth;
        //$result['presencePercentageThisMonth'] = $presencePercentageThisMonth;
        //$result['differentPresenceBetweenMonth'] = $differentPresenceBetweenMonth;
        $result['currentEvent'] = $currentEvent;
        return $result;
    }

    // public static function indexThisUser()
    // {
    //     $iduser = Auth::user()->id;
    //     $data = Shift::latest()->where('user_id',$iduser)->get();

    //      $data = $data->map(function ($data) { 
    //         $data = Arr::add($data, 'room_name', $data['room']['name']);
    //         $data = Arr::add($data, 'status_node_name', $data['status_node']['name']);
    //         $data = Arr::add($data, 'time_start', $data['time']['start']);
    //         $data = Arr::add($data, 'time_end', $data['time']['end']);
    //         return Arr::except($data, ['room', 'status_node', 'time']);
    //     });
    //      return $data;
    // }
    
    public function user()
    {
    	return $this->belongsTo('App\Models\User');
    }

    public function room()
    {
    	return $this->belongsTo('App\Models\Room', 'room_id', 'id');
    }

    public function time()
    {
    	return $this->belongsTo('App\Models\Time', 'time_id', 'id');
    }

    public function histories()
    {
    	return $this->hasMany('App\Models\History');
    }

    public function delete()
    {   
        error_log("shift.delete");
        foreach($this->histories as $history) { $history->delete(); }
        
        return parent::delete();
    }

 
}
