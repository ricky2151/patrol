<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Time extends Model
{
    public function shifts()
    {
    	return $this->hasMany('App\Models\Shift');
    }

   
}
