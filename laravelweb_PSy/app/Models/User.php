<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
	protected $table = 'users';
    protected $fillable = ['name', 'age', 'role_id', 'username', 'phone', 'master_key','email', 'password']; 
    protected $hidden = array('password', 'remember_token'); 
    protected $primaryKey = 'id';

    //relation
    public function role()
    {
        return $this->belongsTo('App\Models\Role');
    }

    public function shifts()
    {
        return $this->hasMany('App\Models\Shift');
    }


     /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function delete()
    {   
        foreach($this->shifts as $shift) { $shift->delete(); }
        
        return parent::delete();
    }

  
}
