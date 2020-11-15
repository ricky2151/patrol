<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
	protected $table = 'buildings';
    protected $fillable = ['name'];

    public function rooms()
    {
    	return $this->hasMany('App\Models\Room');
    }
}
