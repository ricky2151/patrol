<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $table = 'rooms';
    protected $fillable = ['name', 'floor_id','building_id', 'gateway_id'];

    //detail
    public function getRoom()
    {
        $user = [
                'id' => $this['id'],
                'name' => $this['name'],
                'floor_id' => $this['floor']['id'],
                'building_id' => $this['building']['id'],
                'gateway_id' => $this['gateway']['id'],
                
                
            ];
       
        return $user;
    }

    public function getRoomWIthId()
    {
        $user = [
                'id' => $this['id'],
                'name' => $this['name'],
                'floor' => [
                    'id' => $this['floor']['id'],
                    'name' => $this['floor']['name'],
                ],
                'building' => [
                    'id' => $this['building']['id'],
                    'name' => $this['building']['name'],
                ],
                'gateway' => [
                    'id' => $this['gateway']['id'],
                    'name' => $this['gateway']['name'],
                ],
                
            ];
       
        return $user;
    }

    public static function allDataCreate(){
        return ['floors' => Floor::all(['id','name']), 'buildings' => Building::all(['id','name']), 'gateways' => Gateway::all(['id','name'])];
    }

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
        error_log("room.delete");
        foreach($this->shifts as $shift) { $shift->delete(); }
        foreach($this->acknowledges as $acknowledge) { $acknowledge->delete(); }
        
        return parent::delete();
    }
   

}
