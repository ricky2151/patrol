<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
	protected $table = 'users';
    protected $fillable = ['name', 'age', 'role_id', 'username', 'phone', 'master_key','email']; 
    protected $hidden = array('password'); 

    public function role()
    {
    	return $this->belongsTo('App\Models\Role');
    }

    public function shifts()
    {
    	return $this->hasMany('App\Models\Shift');
    }
}
