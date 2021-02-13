<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $table = 'rooms';
    protected $fillable = ['name', 'floor_id','building_id', 'gateway_id'];

    public function floor()
    {
    	return $this->belongsTo('App\Models\Floor');
    }

    public function building()
    {
    	return $this->belongsTo('App\Models\Building');
    }

    public function gateway()
    {
        return $this->belongsTo('App\Models\Gateway');
    }

    public function shifts()
    {
    	return $this->hasMany('App\Models\Shift'); 
    }

    public function acknowledges()
    {
    	return $this->hasMany('App\Models\Acknowledge'); 
    }

   
    public function delete()
    {   
        foreach($this->shifts as $shift) { $shift->delete(); }
        foreach($this->acknowledges as $acknowledge) { $acknowledge->delete(); }
        
        return parent::delete();
    }
   

}
