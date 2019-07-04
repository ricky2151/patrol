<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $fillable = ['user_id', 'room_id', 'time_id', 'date', 'status_node_id', 'message', 'token_shift'];


    public static function index(){
        $collection = Shift::latest()->get();

        

        return $collection;
    }
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
    	return $this->belongsTo('App\Models\Time');
    }

    public function status_node()
    {
    	return $this->belongsTo('App\Models\StatusNode');
    }

    
}
