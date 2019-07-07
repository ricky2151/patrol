<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Floor extends Model
{
	protected $table = 'floors';
    protected $fillable = ['name'];

    public function rooms()
    {
    	return $this->hasMany('App\Models\Room');
    }

}
