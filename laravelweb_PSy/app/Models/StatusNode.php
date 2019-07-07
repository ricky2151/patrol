<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusNode extends Model
{
	protected $table = 'status_nodes';
    protected $fillable = ['name']; 
    public function shifts()
    {
    	return $this->hasMany('App\Models\Shift');
    }

    
}
