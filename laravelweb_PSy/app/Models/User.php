<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use Notifiable;
	protected $table = 'users';
    protected $fillable = ['name', 'age', 'role_id', 'username', 'phone', 'master_key','email']; 
    protected $hidden = array('password', 'remember_token'); 
    protected $primaryKey = 'id';

    public function role()
    {
    	return $this->belongsTo('App\Models\Role');
    }

    public function shifts()
    {
    	return $this->hasMany('App\Models\Shift');
    }

    public function canPlayARole($role)
    {
        $thisrole = $this->role->name;
        if($role == 'Guard' && ($thisrole == 'Guard' && $thisrole == 'Admin' || $thisrole == 'Superadmin'))
        {
            return true;
        }
        else if($role == 'Admin' && ($thisrole == 'Admin' || $thisrole == 'Superadmin'))
        {
            return true;
        }
        else if($role == 'Superadmin' && ($thisrole == 'Superadmin'))
        {
            return true;
        }
        else
        {
            return false;
        }
        
    }
}
