<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreUser;
use App\Http\Requests\UpdateUser;
use App\Http\Requests\SubmitScan;
use App\Models\User;
use App\Models\Shift;
use App\Models\History;
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
        $data = $this->user->find($iduser)->getShiftThatCanBeScanned();
        return response()->json(['error' => false, 'data'=>$data]);
    }

    public function viewHistoryScan($id)
    {
        $iduser = Auth::user()->id;
        $data = $this->user->find($iduser)->viewHistoryScan($id);
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

    public function submitScan(SubmitScan $request)
    {
        
        $data = $request->validated();
        
        $id = $data['id'];
        unset($data['id']);
        $shift = new Shift();
        $shift = $shift->find($id);
        $idUserThisShift = $shift->user()->get()[0]->id;

        $dataIsRight = false;

        //make variable that store time
        $timeNow = Carbon::now()->timezone('Asia/Jakarta')->format('H:i:s');
        $dateNow = Carbon::now()->timezone('Asia/Jakarta')->format('Y-m-d');    
        $dateYesterday = date('Y-m-d', strtotime('-1 day', strtotime($dateNow)));

        $timeShift = $shift->time()->get()[0];
        $startTimeShift = $timeShift['start'];
        $endTimeShift = $timeShift['end'];
        $dateShift = $shift->date;


        //check user is right or not
        if($idUserThisShift == Auth::user()->id)
        {   
            //check scan time is right or not
            $verifyScanTime = checkRangeTimeShift($timeNow, $dateNow, $dateYesterday, $dateShift, $startTimeShift, $endTimeShift, "between");
            if($verifyScanTime == true)
            {
                //check all photo time is right or not
                $countRightPhotoTime = 0;
                if(count($data['photos']) > 0)
                {
                    foreach($data['photos'] as $photo)
                    {
                        $photoDate = substr($photo['photo_time'],0, 10);
                        $photoTime = substr($photo['photo_time'],11, 8);
                        $photoDateYesterday = date('Y-m-d', strtotime('-1 day', strtotime($photoDate)));
                        
                        $verifyPhotoTime = checkRangeTimeShift($photoTime, $photoDate, $photoDateYesterday, $dateShift, $startTimeShift, $endTimeShift, "between");
                        if($verifyPhotoTime == true)
                        {
                            $countRightPhotoTime += 1;
                        }
                    }
                    if($countRightPhotoTime == count($data['photos']))
                    {
                        $dataIsRight = true;
                    }
                }
            }            
        }

        if($dataIsRight)
        {
            //get room name
            
            $room_name = Shift::find($id)->room()->get()[0]->name;
            $temp_shift = [];
            $temp_shift['shift_id'] = $id;
            $temp_shift['status_node_id'] = $data['status_node_id'];
            $temp_shift['scan_time'] = $timeNow;
            isset($data['message']) ? $temp_shift['message'] = $data['message'] : $temp_shift['message'] = '';
            //set image
            $photosSaved = [];
            if(count($data['photos']) > 0)
            {
                $indexPhoto = 0;
                foreach($data['photos'] as $photo)
                {
                    
                    $path = '';
                    $folder = 'photos';
                    $image = $photo['file'];
                    $photo_time = $photo['photo_time'];

                    if(!is_null($image)){

                        //make file name
                        //[photo time]-[user fullname]-[room name]-[index photo]
                        //example : 
                        //132324-ricky-RuanganPosSatpamAgape-0
                        $name = preg_replace('/[^a-zA-Z0-9-_\.]/','',$photo_time . " - " . Auth::user()->name . " - " . $room_name . " - " . $indexPhoto);
                        $path = $image->storeAs($folder, $name . ".jpg");
                       
                    }
                    else{
                        $path = $folder."/default.png";
                    }

                    $photosSaved[] = ['url' => $path, 'photo_time' => $photo_time];
                    
                    $indexPhoto += 1;
                }

            }
            
            //saving
            $history = new History();
            $history = $history->create($temp_shift);
    //        $temp_shift->save();

            if(count($photosSaved) > 0)
                $history->photos()->createMany($photosSaved);
            
            return response()->json(['error' => false, 'message'=>'submit data success !']);
        }
        else{
            return response()->json(['error' => true, 'message'=>"your time scan is not correct"]);
        }
        
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
