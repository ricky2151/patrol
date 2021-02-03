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
use Illuminate\Support\Facades\Log;
use App\Services\Contracts\UserServiceContract as UserService;
use App\Exceptions\SuspiciousInputException;


class UserController extends Controller
{
    private $user;
    private $userService;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct(User $user, UserService $userService)
    {
        $this->user = $user;
        $this->userService = $userService;
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
        $data = $this->userService->getShiftsThatCanBeScanned();
        return response()->json(['error' => false, 'data' => $data]);
    }

    public function viewHistoryScan(Shift $shift)
    {
        $data = $this->userService->viewMyHistoryScan($shift->id);
        return response()->json(['error' => false, 'data' => $data]);
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
        $data = $this->userService->getMasterDataSubmitScan();
        return response()->json(['error' => false, 'data' => ['status_node' => $data]]);
        // $data = ['status_node' => StatusNode::all(['id','name'])];
        // return response()->json(['error' => false, 'data'=>$data]);
        
    }

    public function submitScan(SubmitScan $request)
    {
        //1. log first to easily debug on sentry. 
        Log::channel('daily')->info('Request from /users/submitScan : ' . json_encode($request->all()));

        //2. validate all input
        $data = $request->validated();
        if(!isset($data['photos']))
            throw new SuspiciousInputException("no photo uploaded");

        //3. get datetimenow
        $dateTimeNow = Carbon::now()->timezone('Asia/Jakarta')->format('Y-m-d H:i:s');

        //4. submit scan
        $result = $this->userService->submitScan($data['message'], $data['status_node_id'], $data['id'], $dateTimeNow, $data['photos']);

        //5. return result
        if($result){
            return response()->json(['error' => false, 'message'=>'submit data success !']);
        }
        else{
            throw new StoreDataFailedException("Store Failed");
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
     * Show data by specified id
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

        $this->user->find($id)->delete();
        

        return response()->json(['error' => false, 'message'=>'delete data success !']);
    }
}
