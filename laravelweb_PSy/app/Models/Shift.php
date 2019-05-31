<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    public function user()
    {
    	return $this->belongsTo('App\Models\User');
    }

    public function room()
    {
    	return $this->belongsTo('App\Models\Room');
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
