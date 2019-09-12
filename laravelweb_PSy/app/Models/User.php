<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
	protected $table = 'users';
    protected $fillable = ['name', 'age', 'role_id', 'username', 'phone', 'master_key','email', 'password']; 
    protected $hidden = array('password', 'remember_token'); 
    protected $primaryKey = 'id';

    //alldatacreate
    public function allDataCreate(){

        if($this->role->name == 'Superadmin')
        {
            return ['roles' => Role::all(['id','name']), 'rooms' =>  Room::all(['id','name']), 'status_nodes' => StatusNode::all(['id','name']), 'times' => Time::all(['start','end'])];

        }
        else if($this->role->name == 'Admin')
        {
            return ['roles' => Role::all(['id','name'])->where('id','1'), 'rooms' => Room::with('building:id,name', 'floor:id,name')->get(), 'status_nodes' => StatusNode::all(['id','name']), 'times' => Time::select(DB::raw("id, (start || '-' || end) AS name"))->get()];
        }
    }

    //detail
    public function getUserWIthId()
    {
        $user = [
                'id' => $this['id'],
                'name' => $this['name'],
                'age' => $this['age'],
                'role' => [
                    'id' => $this['role']['id'],
                    'name' => $this['role']['name'],
                ],
                'username' => $this['username'],
                'password' => $this['password'],
                'phone' => $this['phone'],
                'email' => $this['email'],
                'master_key' => $this['master_key'],
            ];
       
        return $user;
    }
    public function getAllShifts()
    {
        $shifts = $this->shifts->map(function($item)
        {
            return [
                'id' => $item['id'],
                'room' => $item['room']['name'],
                'time_start' => $item['time']['start'],
                'time_end' => $item['time']['end'],
                'date' => $item['date'],
                'status_node' => $item['status_node']['name'],
                'message' => $item['message'],
                'scan_time' => $item['scan_time'],
            ];
        });
        return $shifts;
    }
    public function getShiftToday()
    {
        
        $shifts = $this->shifts->where('date',Carbon::today()->format('Y-m-d'))->map(function($item)
        {
            return [
                'id' => $item['id'],
                'room' => $item['room']['name'],
                'time_start' => $item['time']['start'],
                'time_end' => $item['time']['end'],
                'date' => $item['date'],
                'status_node' => $item['status_node']['name'],
                'message' => $item['message'],
                'scan_time' => $item['scan_time'],
            ];
        });
        return $shifts;
    }

    public function getShiftWithId()
    {
        $resultShifts = [];
        $shifts =  $this->shifts->where('scan_time', null);
        
        $shifts = $shifts->map(function($item)
        {
            
                return [
                    'id' => $item['id'],
                    'room' => [
                        'id' => $item['room']['id'],
                        'name' => $item['room']['name'],
                    ],
                    'time' => $item->time()->select(DB::raw("id, (start || '-' || end) AS name"))->get()[0]
                    ,
                    'status_node' => [
                        'id' => $item['status_node']['id'],
                        'name' => $item['status_node']['name'],
                    ],
                    'date' => $item['date'],
                    'message' => $item['message'],
                    'scan_time' => $item['scan_time'],
                ];

            
            
        });

        foreach ($shifts as $key => $value) {
            $resultShifts[] = $value;
        }
        
        //dd($resultShifts);
    
       
        return $resultShifts;
    }


    //update
    public function updateShifts($shifts){

        foreach($shifts as $shift){
            if($shift['type'] == 1) {
                $this->shifts()->create($shift);
            }
            else if($shift['type'] == 0) {
                $this->shifts()->find($shift['id'])->update($shift);
            } else {
                $this->shifts()->find($shift['id'])->delete();
            }
        }
    }

    //auth
    public function canPlayARole($role)
    {
        $thisrole = $this->role->name;

        if($role == 'Guard' && ($thisrole == 'Guard' || $thisrole == 'Admin' || $thisrole == 'Superadmin'))
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



    //relation
    public function role()
    {
        return $this->belongsTo('App\Models\Role');
    }

    public function shifts()
    {
        return $this->hasMany('App\Models\Shift');
    }



    //untuk jwt

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
}
