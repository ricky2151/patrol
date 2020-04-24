<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Acknowledge extends Model
{
    protected $table = 'acknowledges';
    protected $fillable = ['room_id', 'sent', 'time']; 
    protected $hidden = ['updated_at'];

    public function room()
    {
    	return $this->belongsTo('App\Models\Room', 'room_id', 'id');
    }
}
