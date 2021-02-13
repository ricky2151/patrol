<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gateway;
use App\Http\Requests\StoreGateway;
use App\Http\Requests\UpdateGateway;
use App\Services\Contracts\GatewayServiceContract as GatewayService;
use App\Exceptions\GetDataFailedException;
use App\Exceptions\StoreDataFailedException;
use App\Exceptions\UpdateDataFailedException;
use App\Exceptions\DeleteDataFailedException;

class GatewayController extends Controller
{
    private $gateway;
    private $gatewayService;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(Gateway $gateway, GatewayService $gatewayService)
    {
        $this->gateway = $gateway;
        $this->gatewayService = $gatewayService;
    }

    public function index()
    {
        try {
            $data = $this->gatewayService->get();
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
    public function store(StoreGateway $request)
    {

        try {
            $data = $request->validated();
            $this->gatewayService->store($data);    
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
    public function update(UpdateGateway $request, Gateway $gateway)
    {
        try {
            $data = $request->validated();
            $this->gatewayService->update($data, $gateway->id);
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
    public function destroy(Gateway $gateway)
    {
        try {
            $this->gatewayService->delete($gateway->id);
            $response = ['error' => false, 'message'=>'delete data success !'];
            return response()->json($response);
        } catch (\Throwable $th) {
            throw new DeleteDataFailedException('Delete Data Failed : Undefined Error');
        }
    }
}
