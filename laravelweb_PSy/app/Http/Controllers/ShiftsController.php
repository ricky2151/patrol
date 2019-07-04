<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shift;
use Illuminate\Support\Arr;

class ShiftsController extends Controller
{
    private $shift;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct(Shift $shift)
    {
        $this->shift = $shift;
    }
    public function index()
    {
        $data = $this->shift->get();

        $data = $data->map(function ($data) { 
            $data = Arr::add($data, 'room_name', $data['room']['name']);
            $data = Arr::add($data, 'status_node_name', $data['status_node']['name']);
            return Arr::except($data, ['room', 'status_node']);
        });
        return response()->json(['error' => false, 'data'=>$data]);
    }

    // public function list()
    // {
    //     $data = $this->shift->list();
    //     return response()->json(['error' => false, 'data'=>$data]);
    // }

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
    public function store(Request $request)
    {
        //
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
