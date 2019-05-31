<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = ['name', 'floor_id','building_id'];

    public function floor()
    {
    	return $this->belongsTo('App\Models\Floor');
    }

    public function building()
    {
    	return $this->belongsTo('App\Models\Building');
    }

    public function shifts()
    {
    	return $this->hasMany('App\Models\Shift'); 
    }

}
