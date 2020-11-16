<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gateway extends Model
{
    protected $table = 'gateways';
    protected $fillable = ['name', 'location'];

    public function rooms()
    {
    	return $this->hasMany('App\Models\Room');
    }

    public function delete()
    {   
        error_log("gateway.delete");
        foreach($this->rooms as $room) { $room->delete(); }
        
        return parent::delete();
    }

  
}
