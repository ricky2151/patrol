<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Time extends Model
{
	protected $table = 'times';
    protected $fillable = ['start', 'end']; 
    public function shifts()
    {
    	return $this->hasMany('App\Models\Shift');
    }

   
}
