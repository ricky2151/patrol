<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name'];

    

    public function users()
    {
    	return $this->hasMany('App\Models\User');
    }
    public function delete()
    {   
        error_log("role.delete");
        foreach($this->users as $user) { $user->delete(); }
        
        return parent::delete();
    }

  
}
