<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shift;
use App\Models\History;
use App\Models\Photo;
use App\Models\StatusNode;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File; 
use Carbon\Carbon;
use Artisan;


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
    public function getShiftToday()
    {
        $data = $this->shift->indexToday();

        
        return response()->json(['error' => false, 'data'=>$data]);   
    }

    public function graph()
    {
        $smallReportData = $this->shift->showSmallReport();

        $graphData = $this->shift->showGraph();

        $statusNodeData = StatusNode::get();

        

        $result = [];
        $result['smallReportData'] = $smallReportData;
        $result['graphData'] = $graphData;
        $result['statusNodeData'] = $statusNodeData;
        return response()->json(['error' => false, 'data'=>$result]);
    }

    public function getHistories($id)
    {
        $data = $this->shift->find($id)->getHistories();
        return response()->json(['error' => false, 'data'=>$data]);
    }

    

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->shift->find($id)->delete();

        return response()->json(['error' => false, 'message'=>'delete data success !']);
    }
    public function removeAndBackup()
    {  
        Artisan::call("backup:run");
        $resultArtisanCall = Artisan::output();
        if(strpos($resultArtisanCall, "Backup failed"))
        {
            return response()->json(['error' => true, 'message'=>$resultArtisanCall]);
        }
        else
        {
            $shifts = Shift::get();


            $shifts->map(function($itemShift) {
                $deleteThisData = false;
                //check time is right
                $timeNow = Carbon::now()->timezone('Asia/Jakarta')->format('H:i:s');
                $dateNow = Carbon::now()->timezone('Asia/Jakarta')->format('Y-m-d');    
                $dateYesterday = date('Y-m-d', strtotime('-1 day', strtotime($dateNow)));

                $timeShift = $itemShift->time()->get()[0];
                $startTimeShift = $timeShift['start'];
                $endTimeShift = $timeShift['end'];
                $dateShift = $itemShift->date;

                $verifyTimeNowIsGreater = checkRangeTimeShift($timeNow, $dateNow, $dateYesterday, $dateShift, $startTimeShift, $endTimeShift, "greater");

                if($verifyTimeNowIsGreater == true) //if time now is greater than this shift, then delete
                {
                    $deleteThisData = true;
                }
                
                if($deleteThisData)
                {
                    $itemShift->delete();
                }
                
            });
            return response()->json(['error' => false, 'message'=>'success']);
        }

    }
}
