<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use Illuminate\Support\Arr;
use App\Http\Requests\StoreRoom;
use App\Http\Requests\UpdateRoom;
use Illuminate\Support\Facades\DB;


class RoomController extends Controller
{
     private $room;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct(Room $room)
    {
        $this->room = $room;
    }
    public function index()
    {
        
        $data = $this->room->orderBy('id', 'desc')->get();

        $data = $data->map(function ($data) { 
            $data = Arr::add($data, 'floor_name', $data['floor']['name']);
            $data = Arr::add($data, 'building_name', $data['building']['name']);
            $data = Arr::add($data, 'gateway_name', $data['gateway']['name']);
            return Arr::except($data, ['floor', 'building', 'gateway']);
        });
        
        return response()->json(['error' => false, 'data'=>$data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return response()->json(['error' => false, 'data'=>$this->room->allDataCreate()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRoom $request)
    {
        
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $room = $this->room->create($data);            
            DB::commit();
        } catch (\Throwable $e) {
            dd($e);
            DB::rollback();
            return response()->json(['error' => true, 'message'=>$e->message()]);
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
        $room = $this->room->find($id);
        
        return response()->json(['error' => false, 'data'=>['room'=>$room]]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRoom $request, $id)
    {
        $this->room->find($id)->update($request->validated());
        return response()->json(['error' => false, 'message'=>'update data success !']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // DB::beginTransaction();
        // try {
        //     $this->room->find($id)->shifts()->delete();
        //     $this->room->find($id)->delete();
        //     DB::commit();
        // }catch (\Throwable $e) {
        //     DB::rollback();
        //     dd($e);

        // }
        $this->room->find($id)->delete();
        

        return response()->json(['error' => false, 'message'=>'delete data success !']);
    }
}
