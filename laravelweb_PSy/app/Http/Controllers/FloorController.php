<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Floor;
use App\Http\Requests\StoreFloor;
use App\Http\Requests\UpdateFloor;
use App\Services\Contracts\FloorServiceContract as FloorService;
use App\Exceptions\GetDataFailedException;
use App\Exceptions\StoreDataFailedException;
use App\Exceptions\UpdateDataFailedException;
use App\Exceptions\DeleteDataFailedException;

class FloorController extends Controller
{
    private $floor;
    private $floorService;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct(Floor $floor, FloorService $floorService)
    {
        $this->floor = $floor;
        $this->floorService = $floorService;
    }
    public function index()
    {
        try {
            $data = $this->floorService->get();
            $response = ['error' => false, 'data'=>$data];
            return response()->json($response);
        } catch (\Throwable $th) {
            throw new GetDataFailedException('Get Data Failed : Undefined Error');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreFloor $request)
    {
        try {
            $data = $request->validated();
            $this->floorService->store($data);    
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
    public function edit($id)
    {
        $floor = $this->floor->find($id);
        ['error' => false, 'data'=>['floor'=>$floor]];
        return response()->json($response);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateFloor $request, Floor $floor)
    {
        try {
            $data = $request->validated();
            $this->floorService->update($data, $floor->id);
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
    public function destroy(Floor $floor)
    {   
        try {
            $this->floorService->delete($floor->id);
            $response = ['error' => false, 'message'=>'delete data success !'];
            return response()->json($response);
        } catch (\Throwable $th) {
            throw new DeleteDataFailedException('Delete Data Failed : Undefined Error');
        }
    }
}
