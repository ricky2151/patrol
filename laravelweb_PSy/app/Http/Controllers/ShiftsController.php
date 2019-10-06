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
        
        $data = $this->shift->index();

        
        return response()->json(['error' => false, 'data'=>$data]);
    }
    public function graph()
    {
        $smallReportData = $this->shift->showSmallReport();

        $graphData = $this->shift->showGraph();
        $result = [];
        $result['smallReportData'] = $smallReportData;
        $result['graphData'] = $graphData;
        return response()->json(['error' => false, 'data'=>$result]);
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
