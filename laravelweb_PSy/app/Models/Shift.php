<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Carbon\Carbon;
use DB;


class Shift extends Model
{
    protected $fillable = ['user_id', 'room_id', 'time_id', 'date', 'status_node_id', 'message', 'scan_time'];


    public static function index(){
        

        $data = Shift::latest()->get();

        $data = $data->map(function ($data) { 
            $data = Arr::add($data, 'room_name', $data['room']['name']);
            $data = Arr::add($data, 'status_node_name', $data['status_node']['name']);
            $data = Arr::add($data, 'time_start', $data['time']['start']);
            $data = Arr::add($data, 'time_end', $data['time']['end']);
            return Arr::except($data, ['room', 'status_node', 'time']);
        });
        

        return $data;
    }
    public static function showGraph()
    {
        $data = DB::table('shifts')->select(DB::raw('strftime("%m",date) as month, status_nodes.name as status_nodes, count(*) as count'))
        ->groupBy('month', 'status_nodes')
        ->join('status_nodes', 'status_node_id','status_nodes.id')
        ->get();
        // $data = Shift::latest()->get()->groupBy(function($d) {
        //     return Carbon::parse($d->date)->format('m');
        // });
        return $data;
        
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

    public function status_node()
    {
    	return $this->belongsTo('App\Models\StatusNode', 'status_node_id', 'id');
    }

    
}
