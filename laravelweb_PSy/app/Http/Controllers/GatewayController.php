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
     * Show data by specified id
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $gateway = $this->gateway->find($id);
        
        return response()->json(['error' => false, 'data'=>['gateway'=>$gateway]]);
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
        $this->gateway->find($id)->delete();
        

        return response()->json(['error' => false, 'message'=>'delete data success !']);
    }
}
