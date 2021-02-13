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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Services\Contracts\UserServiceContract as UserService;
use App\Services\Contracts\RoleServiceContract as RoleService;
use App\Services\Contracts\RoomServiceContract as RoomService;
use App\Services\Contracts\TimeServiceContract as TimeService;
use App\Services\Contracts\ShiftServiceContract as ShiftService;
use App\Services\Contracts\StatusNodeServiceContract as StatusNodeService;
use App\Exceptions\SuspiciousInputException;
use App\Exceptions\GetDataFailedException;
use App\Exceptions\StoreDataFailedException;


class UserController extends Controller
{
    private $user;
    private $userService;
    private $statusNodeService;
    private $roleService;
    private $roomService;
    private $timeService;
    private $shiftService;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct(User $user, UserService $userService, StatusNodeService $statusNodeService, RoleService $roleService, RoomService $roomService, TimeService $timeService, ShiftService $shiftService)
    {
        $this->user = $user;
        $this->userService = $userService;
        $this->statusNodeService = $statusNodeService;
        $this->roleService = $roleService;
        $this->roomService = $roomService;
        $this->timeService = $timeService;
        $this->shiftService = $shiftService;
    }
    public function index()
    {
        try {
            $data = $this->userService->get();
            $response = ['error' => false, 'data'=>$data];
            return response()->json($response);
        } catch (\Throwable $th) {
            dd($th);
            throw new GetDataFailedException('Get Data Failed : Undefined Error');
        }

    }
    public function getAllShifts(User $user)
    {
        try {
            
            $data = $this->userService->getShifts($user->id);
            $response = ['error' => false, 'data'=>$data];
            return response()->json($response);
        } catch (\Throwable $th) {
            dd($th);
            throw new GetDataFailedException('Get Data Failed : Undefined Error');
        }
    }
    public function shifts()
    {
        try {
            $data = $this->userService->getShiftsThatCanBeScanned();
            $response = ['error' => false, 'data' => $data];
            return response()->json($response);
        } catch (\Throwable $th) {
            throw new GetDataFailedException('Get Data Failed : Undefined Error');
        }
        
    }

    public function viewHistoryScan(Shift $shift)
    {
        try {
            $data = $this->userService->viewMyHistoryScan($shift->id);
            $response = ['error' => false, 'data' => $data];
            return response()->json($response);
        } catch (\Throwable $th) {
            throw new GetDataFailedException('Get Data Failed : Undefined Error');
        }
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            $dataRole = $this->roleService->get();
            $dataRoom = $this->roomService->getWithoutFormat();
            $dataStatusNode = $this->statusNodeService->get();
            $dataTime = $this->timeService->get();
            $shiftFuture = $this->shiftService->getShiftsNotAssign();
            $result = [
                'roles' => $dataRole,
                'rooms' => $dataRoom,
                'status_nodes' => $dataStatusNode,
                'times' => $dataTime,
                'shift_future' => $shiftFuture,
            ];
            $response = ['error' => false, 'data'=>$result];
            return response()->json($response);
        } catch (\Throwable $th) {
            dd($th);
            throw new GetDataFailedException('Get Data Failed : Undefined Error');
        }
    }

    public function getMasterData()
    {
        try {
            $data = $this->statusNodeService->get();
            $result = [
                'status_node' => $data
            ];
            $response = ['error' => false, 'data'=>$result];
            return response()->json($response);
        } catch (\Throwable $th) {
            throw new GetDataFailedException('Get Data Failed : Undefined Error');
        }
        
        
    }

    public function submitScan(SubmitScan $request)
    {
        DB::beginTransaction();
        try {
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
                DB::commit();
                $response = ['error' => false, 'message'=>'submit data success !'];
                return response()->json($response);
            }
            else{
                throw new StoreDataFailedException("Store Failed");
            }
        } catch (\Throwable $th) {
            DB::rollback();
            if($th instanceof SuspiciousInputException) {
                throw $th;
            } else {
                throw new StoreDataFailedException("Store Data Failed : Undefined Error");
            }
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
            $this->userService->storeUserWithShifts($data);
            DB::commit();
            $response = ['error' => false, 'message'=>'create data success !'];
            return response()->json($response);
        } catch (\Throwable $e) {
            DB::rollback();
            dd($e);
            throw new StoreDataFailedException('Store Data Failed : Undefined Error');
            
        }
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
    public function edit(User $user)
    {
        try {
            $data = $this->userService->findWithShifts($user->id);
            $response = ['error' => false, 'data'=>$data];
            return response()->json($response);
        } catch (\Throwable $th) {
            dd($th);
            throw new GetDataFailedException('Get Data Failed : Undefined Error');
        }
        // $userEdit = $this->user->find($user->id);
        // $shiftsWithId = $userEdit->getShiftWithId();
        // $userWithId = $userEdit->getUserWithId();
        // $userWithId['password'] = '';
        // return response()->json(['error' => false, 'data'=>['user'=>$userWithId,'shifts'=>$shiftsWithId]]);
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUser $request, User $user)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $this->userService->updateUserWithShifts($data, $user->id);
            DB::commit();
            $response = ['error' => false, 'message'=>'update data success !'];
            return response()->json($response);
        } catch (\Throwable $e) {
            DB::rollback();
            //dd($e);
            throw new StoreDataFailedException('Store Data Failed : Undefined Error');
            
        }

        // $data = $request->validated();
        // $user = $this->user->find($user->id);
        // //dd($data);
        // DB::beginTransaction();
        // try {
        //     if(array_key_exists('password', $data))
        //     {
        //      $data["password"] = bcrypt($data["password"]);

        //     }
        //     $user->update($data);
        //     if(array_key_exists('shifts', $data))
        //     {
        //         $user->updateShifts($data['shifts']);
        //     }
            

        //     DB::commit();
        //     return response()->json(['error' => false, 'message'=>'update data success !']);
        // } catch (\Throwable $e) {

        //     DB::rollback();
        //     dd($e);
        // }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {

        try {
            $this->userService->delete($user->id);
            $response = ['error' => false, 'message'=>'delete data success !'];
            return response()->json($response);
        } catch (\Throwable $th) {
            throw new DeleteDataFailedException('Delete Data Failed : Undefined Error');
        }
    }
}
