<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Http\Requests\StoreRoom;
use App\Http\Requests\UpdateRoom;
use App\Services\Contracts\RoomServiceContract as RoomService;
use App\Services\Contracts\FloorServiceContract as FloorService;
use App\Services\Contracts\BuildingServiceContract as BuildingService;
use App\Services\Contracts\GatewayServiceContract as GatewayService;
use App\Exceptions\GetDataFailedException;
use App\Exceptions\StoreDataFailedException;
use App\Exceptions\UpdateDataFailedException;
use App\Exceptions\DeleteDataFailedException;


class RoomController extends Controller
{
     private $roomService;
     private $floorService;
     private $buildingService;
     private $gatewayService;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct(RoomService $roomService, FloorService $floorService, BuildingService $buildingService, GatewayService $gatewayService)
    {
        $this->roomService = $roomService;
        $this->floorService = $floorService;
        $this->buildingService = $buildingService;
        $this->gatewayService = $gatewayService;
    }
    public function index()
    {
        try {
            $data = $this->roomService->get();
            $response = ['error' => false, 'data'=>$data];
            return response()->json($response);
        } catch (\Throwable $th) {
            dd($th);
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
            $dataFloor = $this->floorService->get();
            $dataBuilding = $this->buildingService->get();
            $dataGateway = $this->gatewayService->get();
            $result = [
                'floors' => $dataFloor,
                'buildings' => $dataBuilding,
                'gateways' => $dataGateway
            ];
            $response = ['error' => false, 'data'=>$result];
            return response()->json($response);
        } catch (\Throwable $th) {
            dd($th);
            throw new GetDataFailedException('Get Data Failed : Undefined Error');
        }

        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRoom $request)
    {
        
        try {
            $data = $request->validated();
            $this->roomService->store($data);    
            $response = ['error' => false, 'message'=>'create data success !'];
            return response()->json($response);
        } catch (\Throwable $th) {
            throw new StoreDataFailedException('Store Data Failed : Undefined Error');
        }
    }

    /**
     * Show data by specified id
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Room $room)
    {
        try {
            $data = $this->roomService->find($room->id);
            $response = ['error' => false, 'data'=> [
                'room' => $data
            ]];
            return response()->json($response);
        } catch (\Throwable $th) {
            dd($th);
            throw new GetDataFailedException('Get Data Failed : Undefined Error');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRoom $request, Room $room)
    {
        try {
            $data = $request->validated();
            $this->roomService->update($data, $room->id);
            $response = ['error' => false, 'message'=>'update data success !'];
            return response()->json($response);
        } catch (\Throwable $th) {
            dd($th);
            throw new UpdateDataFailedException('Update Data Failed : Undefined Error');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Room $room)
    {
        try {
            $this->roomService->delete($room->id);
            $response = ['error' => false, 'message'=>'delete data success !'];
            return response()->json($response);
        } catch (\Throwable $th) {
            throw new DeleteDataFailedException('Delete Data Failed : Undefined Error');
        }
    }
}
