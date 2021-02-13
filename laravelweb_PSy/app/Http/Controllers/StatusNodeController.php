<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StatusNode;
use App\Http\Requests\StoreStatusNode;
use App\Http\Requests\UpdateStatusNode;
use App\Services\Contracts\StatusNodeServiceContract as StatusNodeService;
use App\Exceptions\GetDataFailedException;
use App\Exceptions\StoreDataFailedException;
use App\Exceptions\UpdateDataFailedException;
use App\Exceptions\DeleteDataFailedException;

class StatusNodeController extends Controller
{
    private $status_node;
    private $statusNodeService;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct(StatusNode $status_node, StatusNodeService $statusNodeService)
    {
        $this->status_node = $status_node;
        $this->statusNodeService = $statusNodeService;

    }
    public function index()
    {
        try {
            $data = $this->statusNodeService->get();
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
    public function store(StoreStatusNode $request)
    {
        try {
            $data = $request->validated();
            $this->statusNodeService->store($data);    
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
        $status_node = $this->status_node->find($id);
        
        return response()->json(['error' => false, 'data'=>['status_node'=>$status_node]]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateStatusNode $request, StatusNode $statusNode)
    {
        try {
            $data = $request->validated();
            $this->statusNodeService->update($data, $statusNode->id);
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
    public function destroy(StatusNode $statusNode)
    {
        try {
            $this->statusNodeService->delete($statusNode->id);
            $response = ['error' => false, 'message'=>'delete data success !'];
            return response()->json($response);
        } catch (\Throwable $th) {
            throw new DeleteDataFailedException('Delete Data Failed : Undefined Error');
        }
    }
}
