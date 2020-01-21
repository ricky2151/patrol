<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreUser;
use App\Http\Requests\UpdateUser;
use App\Http\Requests\SubmitShift;
use App\Models\User;
use App\Models\Shift;
use App\Models\StatusNode;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class UserController extends Controller
{
    private $user;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct(User $user)
    {
        $this->user = $user;
    }
    public function index()
    {
        if(Auth::user()->canPlayARole('Superadmin'))
        {
            $data = $this->user->where('role_id','<','3')->orderBy('id', 'desc')->get();
        }
        else
        {
            $data = $this->user->where('role_id','1')->orderBy('id', 'desc')->get();
        }
        

        $data = $data->map(function ($data) { 
            $data = Arr::add($data, 'role_name', $data['role']['name']);
            return Arr::except($data, ['role']);
        });
        return response()->json(['error' => false, 'data'=>$data]);
    }
    public function getAllShifts($id)
    {
        
        
        $data = $this->user->getAllShifts($id);
        return response()->json(['error' => false, 'data'=>$data]);
    }
    public function shifts()
    {
        $iduser = Auth::user()->id;
        $data = $this->user->find($iduser)->getShiftToday();
        return response()->json(['error' => false, 'data'=>$data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return response()->json(['error' => false, 'data'=>Auth::user()->allDataCreate()]);
    }

    public function getMasterData()
    {
        $data = ['status_node' => StatusNode::all(['id','name'])];
        return response()->json(['error' => false, 'data'=>$data]);
        
    }

    public function submitShift(SubmitShift $request)
    {
        $data = $request->validated();
        $id = $data['id'];
        unset($data['id']);
        $temp_shift = Shift::find($id);
        $temp_shift->status_node_id = $data['status_node_id'];
        $temp_shift->scan_time = Carbon::now()->timezone('Asia/Jakarta')->format('H:i:s');
        isset($data['message']) ? $temp_shift->message = $data['message'] : $temp_shift->message = '';
        //set image
        $photosSaved = [];
        if($request->file('photos'))
        {
            foreach($request->file('photos') as $image)
            {
                $path = '';
                $folder = 'photos';
                $name =  $id.Str::random(10);

                if(!is_null($image)){
                    $name = strtolower(preg_replace('/[^a-zA-Z0-9-_\.]/','',$name));

                    $path = $image->storeAs($folder, $name . ".jpg");
                }
                else{
                    $path = $folder."/default.png";
                }

                $photosSaved[] = ['url' => $path];

            }

        }
        
        //saving
        $temp_shift->save();
        if(count($photosSaved) > 0)
            $temp_shift->photos()->createMany($photosSaved);

        return response()->json(['error' => false, 'message'=>'submit data success !']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUser $request)
    {

        DB::beginTransaction();
        try {
            $data = $request->validated();
            $data["password"] = bcrypt($data["password"]);
            //make random string
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < 16; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            //======
            $data['master_key'] = $randomString;
            
            
            

            //dd($data);
            

            $user = $this->user->create($data);
            
            if(array_key_exists('shifts', $data))
            {
                $datashifts = collect(Arr::pull($data,'shifts'))->toArray();
                $user->shifts()->createMany($datashifts);
            }
            

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();
            dd($e);
            
        }
        return response()->json(['error' => false, 'message'=>'create data success !']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = $this->user->find($id);
        $shiftsWithId = $user->getShiftWithId();
        $userWithId = $user->getUserWithId();
        $userWithId['password'] = '';
        return response()->json(['error' => false, 'data'=>['user'=>$userWithId,'shifts'=>$shiftsWithId]]);
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUser $request, $id)
    {
        $data = $request->validated();
        $user = $this->user->find($id);
        //dd($data);
        DB::beginTransaction();
        try {
            if(array_key_exists('password', $data))
            {
             $data["password"] = bcrypt($data["password"]);

            }
            
            $user->update($data);
            if(array_key_exists('shifts', $data))
            {
                $user->updateShifts($data['shifts']);
            }
            

            DB::commit();
            return response()->json(['error' => false, 'message'=>'update data success !']);
        } catch (\Throwable $e) {

            DB::rollback();
            dd($e);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
        DB::beginTransaction();
        try {
            $this->user->find($id)->shifts()->delete();
            $this->user->find($id)->delete();
            DB::commit();
        }catch (\Throwable $e) {
            DB::rollback();
            dd($e);

        }
        

        return response()->json(['error' => false, 'message'=>'delete data success !']);
    }
}
