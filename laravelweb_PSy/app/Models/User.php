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
        $shiftNotAssign = DB::table('shifts')
        ->whereDate('date', '>', Carbon::now())
        ->join('rooms', 'rooms.id','shifts.room_id')
        ->join('times', 'times.id','shifts.time_id')
        ->select(
            [
                'shifts.date',
                'rooms.name as room_name',
                'rooms.id as room_id',
                'times.id as time_id',
                DB::raw('times.start || "-" || times.end as time_start_end'),
            ]
        )
        ->orderBy('date', 'DESC')
        ->orderBy('time_start_end', 'DESC')
        ->orderBy('rooms.name','ASC')
        ->orderBy('message', 'ASC')
        ->get();
       
        


        if($this->role->name == 'Superadmin')
        {
            return ['roles' => Role::all(['id','name']), 'rooms' =>  Room::with('building:id,name', 'floor:id,name')->get(), 'status_nodes' => StatusNode::all(['id','name']), 'times' => Time::select(DB::raw("id, (start || '-' || end) AS name"))->get(), 'shift_future' => $shiftNotAssign];

        }
        else if($this->role->name == 'Admin')
        {
            return ['roles' => Role::all(['id','name'])->where('id','1'), 'rooms' => Room::with('building:id,name', 'floor:id,name')->get(), 'status_nodes' => StatusNode::all(['id','name']), 'times' => Time::select(DB::raw("id, (start || '-' || end) AS name"))->get(), 'shift_future' => $shiftNotAssign];
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
    public function viewHistoryScan($id)
    {
        //dd($this->shifts()->get());
        $shifts = $this->shifts()->where('id',$id)->get()->map(function($item)
        {
            return [
                'shift_id' => $item['id'],
                'histories' => $item->histories()->get()->map(function($item){
                    return [
                        'id' => $item['id'],
                        'status_node_id' => $item['status_node_id'],
                        'status_node_name' => $item->status_node()->get()[0]['name'],
                        'message' => $item['message'],
                        'scan_time' => $item['scan_time'],
                        'photos' => $item->photos()->get()->map(function($item) {
                            return [
                                'id' => $item['id'],
                                'url' => $item['url'],
                                'photo_time' => $item['photo_time'],
                            ];
                        }),
                    ];
                }),
            ];
        });
        //dd($shifts);
        return $shifts;
    }
    public function getAllShifts($id)
    {
        $data = DB::table('shifts')
        ->where('shifts.user_id', $id)
        ->join('rooms', 'rooms.id','shifts.room_id')
        ->join('times', 'times.id','shifts.time_id')
        ->select(
            [
                'shifts.id as id',
                'shifts.user_id',
                'shifts.date',
                'rooms.name as room_name',
                DB::raw('times.start || " - " || times.end as time_start_end'),
            ]
        )
        ->orderBy('date', 'DESC')
        ->orderBy('time_start_end', 'DESC')
        ->orderBy('rooms.name','ASC')
        ->get();

        // $data = $data->map(function ($data) { 
        //     $data = Arr::add($data, 'room_name', $data['room']['name']);
        //     $data = Arr::add($data, 'status_node_name', $data['status_node']['name']);
        //     $data = Arr::add($data, 'user_name', $data['user']['name']);
        //     $data = Arr::add($data, 'time_start_end', $data['time']['start'] . ' - ' . $data['time']['end']);
        //     return Arr::except($data, ['room', 'status_node', 'time']);
        // });
        

        return $data;
    }
    public function getShiftThatCanBeScanned()
    {
        
        //return ($this->shifts()->get()->toArray());
        $shiftsToday = $this->shifts()
        ->where('date',Carbon::today()->format('Y-m-d'))
        ->get()->map(function($item)
        {
            $last_scan = $item->histories()->latest('scan_time')->first();
            unset($last_scan['created_at']);
            unset($last_scan['updated_at']);
            return [
                'id' => $item['id'],
                'room' => $item['room']['name'],
                'time_start' => $item['time']['start'],
                'time_end' => $item['time']['end'],
                'date' => $item['date'],
                'countScanned' => $item->histories()->get()->count(), 
                'last_scan' => $last_scan,
            ];
        });
        
        $shiftsToday = $shiftsToday->filter(function($value, $key){
            return $value != null;
        });
        

        $shiftsYesterday = $this->shifts()
        ->where('date',Carbon::yesterday()->format('Y-m-d'))
        ->get()->map(function($item)
        {
            $last_scan = $item->histories()->latest('scan_time')->first();
            unset($last_scan['created_at']);
            unset($last_scan['updated_at']);
            if(strtotime($item['time']['start']) > strtotime($item['time']['end']))
            {
                return [
                    'id' => $item['id'],
                    'room' => $item['room']['name'],
                    'time_start' => $item['time']['start'],
                    'time_end' => $item['time']['end'],
                    'date' => $item['date'],
                    'countScanned' => $item->histories()->get()->count(), 
                    'last_scan' => $last_scan,
                ];
            }
        });
        $shiftsYesterday = $shiftsYesterday->filter(function($value, $key){
            return $value != null;
        });
        
        
        $result = $shiftsToday->concat($shiftsYesterday);

        return $result;
    }

    public function getShiftWithId()
    {
        $resultShifts = [];
        $shifts =  $this->shifts()->doesntHave('histories')->get();
        
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
                    // 'status_node' => [
                    //     'id' => $item['status_node']['id'],
                    //     'name' => $item['status_node']['name'],
                    // ],
                    'date' => $item['date'],
                    //'message' => $item['message'],
                    //'scan_time' => $item['scan_time'],
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
                //dd($this->shifts()->get());
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

    public function delete()
    {   
        error_log("user.delete");
        foreach($this->shifts as $shift) { $shift->delete(); }
        
        return parent::delete();
    }

  
}
