<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gateway;
use Illuminate\Support\Arr;
use App\Http\Requests\StoreGateway;
use App\Http\Requests\UpdateGateway;
use Illuminate\Support\Facades\DB;

class GatewayController extends Controller
{
    private $gateway;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(Gateway $gateway)
    {
        $this->gateway = $gateway;
    }

    public function index()
    {
        $data = $this->gateway->orderBy('id', 'desc')->get();

        
        return response()->json(['error' => false, 'data'=>$data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

       
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreGateway $request)
    {

        DB::beginTransaction();
        try {
            $data = $request->validated();
            $gateway = $this->gateway->create($data);            
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
    public function update(UpdateGateway $request, $id)
    {
        $this->gateway->find($id)->update($request->validated());
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
        
        DB::beginTransaction();
        try {
            $this->gateway->find($id)->rooms->map(function($item){
                $item->shifts()->delete();
            });
            
            $this->gateway->find($id)->rooms()->delete();
            $this->gateway->find($id)->delete();
            DB::commit();
        }catch (\Throwable $e) {
            DB::rollback();
            dd($e);

        }
        

        return response()->json(['error' => false, 'message'=>'delete data success !']);
    }
}
