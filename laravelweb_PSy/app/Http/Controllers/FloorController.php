<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Floor;
use Illuminate\Support\Arr;
use App\Http\Requests\StoreFloor;
use App\Http\Requests\UpdateFloor;
use Illuminate\Support\Facades\DB;

class FloorController extends Controller
{
    private $floor;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct(Floor $floor)
    {
        $this->floor = $floor;
    }
    public function index()
    {
        $data = $this->floor->orderBy('id', 'desc')->get();
        return response()->json(['error' => false, 'data'=>$data]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreFloor $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $floor = $this->floor->create($data);            
            DB::commit();
        } catch (\Throwable $e) {
            dd($e);
            DB::rollback();
            return response()->json(['error' => true, 'message'=>$e->message()]);
        }
        return response()->json(['error' => false, 'message'=>'create data success !']);
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
        
        return response()->json(['error' => false, 'data'=>['floor'=>$floor]]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateFloor $request, $id)
    {
        $this->floor->find($id)->update($request->validated());
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
        $this->floor->find($id)->delete();

        return response()->json(['error' => false, 'message'=>'delete data success !']);
    }
}
