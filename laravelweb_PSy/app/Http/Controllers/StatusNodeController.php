<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StatusNode;
use Illuminate\Support\Arr;
use App\Http\Requests\StoreStatusNode;
use App\Http\Requests\UpdateStatusNode;
use Illuminate\Support\Facades\DB;

class StatusNodeController extends Controller
{
    private $status_node;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct(StatusNode $status_node)
    {
        $this->status_node = $status_node;
    }
    public function index()
    {
        $data = $this->status_node->orderBy('id', 'desc')->get();

        
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
    public function store(StoreStatusNode $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $status_node = $this->status_node->create($data);            
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
    public function update(UpdateStatusNode $request, $id)
    {
        $this->status_node->find($id)->update($request->validated());
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
        //     $this->status_node->find($id)->histories()->delete();
        //     $this->status_node->find($id)->delete();
        //     DB::commit();
        // }catch (\Throwable $e) {
        //     DB::rollback();
        //     dd($e);

        // }

        $this->status_node->find($id)->delete();
        

        return response()->json(['error' => false, 'message'=>'delete data success !']);
    }
}
