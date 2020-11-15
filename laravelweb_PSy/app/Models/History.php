<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Carbon\Carbon;
use DB;

class History extends Model
{
    protected $fillable = ['shift_id', 'status_node_id', 'message', 'scan_time'];

    public function shift()
    {
        return $this->belongsTo('App\Models\Shift');
    }
    public function status_node()
    {
    	return $this->belongsTo('App\Models\StatusNode', 'status_node_id', 'id');
    }
    public function photos()
    {
        return $this->hasMany('App\Models\Photo'); 
    }

}
