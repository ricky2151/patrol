<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreAcknowledge;
use App\Models\Acknowledge;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class AcknowledgeController extends Controller
{
    private $acknowledge;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(Acknowledge $acknowledge)
    {
        $this->middleware('RoleAdmin', ['except' => ['store']]);
        $this->acknowledge = $acknowledge;
    }
    public function index()
    {
        $data = $this->acknowledge->orderBy('id', 'desc')->get();

        $data = $data->map(function ($data) { 
            $data = Arr::add($data, 'room_name', $data['room']['name']);
            return Arr::except($data, ['room']);
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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAcknowledge $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $acknowledge = $this->acknowledge->create($data);                     
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
