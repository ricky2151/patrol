<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Shift extends Model
{
    protected $fillable = ['user_id', 'room_id', 'time_id', 'date'];
    
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
        foreach($this->histories as $history) { $history->delete(); }
        
        return parent::delete();
    }

 
}
