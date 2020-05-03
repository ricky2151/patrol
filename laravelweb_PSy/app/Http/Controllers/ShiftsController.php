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
    public function removeAndBackup()
    {
        // $listHasilTest = [];
        // $listHasilTest[] = checkRangeTimeShift("08:00:00", "21-04-2020", "20-04-2020", "22-04-2020", "06:00", "10:00", "between"); //0 false
        // $listHasilTest[] = checkRangeTimeShift("08:00:00", "21-04-2020", "20-04-2020", "22-04-2020", "09:00", "10:00", "between"); //1 false
        // $listHasilTest[] = checkRangeTimeShift("08:00:00", "21-04-2020", "20-04-2020", "21-04-2020", "09:00", "10:00", "between"); //2 false
        // $listHasilTest[] = checkRangeTimeShift("08:00:00", "21-04-2020", "20-04-2020", "21-04-2020", "20:00", "22:00", "between"); //3 false
        // $listHasilTest[] = checkRangeTimeShift("08:00:00", "21-04-2020", "20-04-2020", "21-04-2020", "06:00", "10:00", "between"); //4 true
        // $listHasilTest[] = checkRangeTimeShift("08:00:00", "21-04-2020", "20-04-2020", "21-04-2020", "06:00", "07:00", "between"); //5 false
        // $listHasilTest[] = checkRangeTimeShift("08:00:00", "21-04-2020", "20-04-2020", "20-04-2020", "20:00", "10:00", "between"); //6 true
        // $listHasilTest[] = checkRangeTimeShift("08:00:00", "21-04-2020", "20-04-2020", "20-04-2020", "20:00", "08:00", "between"); //7 false
        // $listHasilTest[] = checkRangeTimeShift("08:00:00", "21-04-2020", "20-04-2020", "20-04-2020", "06:00", "05:00", "between"); //8 false
        // $listHasilTest[] = checkRangeTimeShift("08:00:00", "21-04-2020", "20-04-2020", "20-04-2020", "20:00", "22:00", "between"); //9 false
        // $listHasilTest[] = checkRangeTimeShift("08:00:00", "21-04-2020", "20-04-2020", "20-04-2020", "06:00", "10:00", "between"); //10 false
        // $listHasilTest[] = checkRangeTimeShift("08:00:00", "21-04-2020", "20-04-2020", "19-04-2020", "06:00", "10:00", "between"); //11 false
        // $listHasilTest[] = checkRangeTimeShift("08:00:00", "21-04-2020", "20-04-2020", "22-04-2020", "06:00", "10:00", "between"); //12 false

        // $listHasilTest[] = checkRangeTimeShift("08:00:00", "21-04-2020", "20-04-2020", "22-04-2020", "06:00", "10:00", "greater"); //13 false
        // $listHasilTest[] = checkRangeTimeShift("08:00:00", "21-04-2020", "20-04-2020", "22-04-2020", "09:00", "10:00", "greater"); //14 false
        // $listHasilTest[] = checkRangeTimeShift("08:00:00", "21-04-2020", "20-04-2020", "21-04-2020", "09:00", "10:00", "greater"); //15 false
        // $listHasilTest[] = checkRangeTimeShift("08:00:00", "21-04-2020", "20-04-2020", "21-04-2020", "20:00", "22:00", "greater"); //16 false
        // $listHasilTest[] = checkRangeTimeShift("08:00:00", "21-04-2020", "20-04-2020", "21-04-2020", "06:00", "10:00", "greater"); //17 false
        // $listHasilTest[] = checkRangeTimeShift("08:00:00", "21-04-2020", "20-04-2020", "21-04-2020", "06:00", "07:00", "greater"); //18 true
        // $listHasilTest[] = checkRangeTimeShift("08:00:00", "21-04-2020", "20-04-2020", "20-04-2020", "20:00", "10:00", "greater"); //19 false
        // $listHasilTest[] = checkRangeTimeShift("08:00:00", "21-04-2020", "20-04-2020", "20-04-2020", "20:00", "08:00", "greater"); //20 false
        // $listHasilTest[] = checkRangeTimeShift("08:00:00", "21-04-2020", "20-04-2020", "20-04-2020", "06:00", "05:00", "greater"); //21 true
        // $listHasilTest[] = checkRangeTimeShift("08:00:00", "21-04-2020", "20-04-2020", "20-04-2020", "20:00", "22:00", "greater"); //22 true
        // $listHasilTest[] = checkRangeTimeShift("08:00:00", "21-04-2020", "20-04-2020", "20-04-2020", "06:00", "10:00", "greater"); //23 true
        // $listHasilTest[] = checkRangeTimeShift("08:00:00", "21-04-2020", "20-04-2020", "19-04-2020", "06:00", "10:00", "greater"); //24 true
        // $listHasilTest[] = checkRangeTimeShift("08:00:00", "21-04-2020", "20-04-2020", "22-04-2020", "06:00", "10:00", "greater"); //25 false

        // $listHasilTest[] = checkRangeTimeShift("08:00:00", "21-04-2020", "20-04-2020", "22-04-2020", "06:00", "10:00", "smaller"); //26 true
        // $listHasilTest[] = checkRangeTimeShift("08:00:00", "21-04-2020", "20-04-2020", "22-04-2020", "09:00", "10:00", "smaller"); //27 true
        // $listHasilTest[] = checkRangeTimeShift("08:00:00", "21-04-2020", "20-04-2020", "21-04-2020", "09:00", "10:00", "smaller"); //28 true
        // $listHasilTest[] = checkRangeTimeShift("08:00:00", "21-04-2020", "20-04-2020", "21-04-2020", "20:00", "22:00", "smaller"); //29 true 
        // $listHasilTest[] = checkRangeTimeShift("08:00:00", "21-04-2020", "20-04-2020", "21-04-2020", "06:00", "10:00", "smaller"); //30 false
        // $listHasilTest[] = checkRangeTimeShift("08:00:00", "21-04-2020", "20-04-2020", "21-04-2020", "06:00", "07:00", "smaller"); //31 false
        // $listHasilTest[] = checkRangeTimeShift("08:00:00", "21-04-2020", "20-04-2020", "20-04-2020", "20:00", "10:00", "smaller"); //32 false
        // $listHasilTest[] = checkRangeTimeShift("08:00:00", "21-04-2020", "20-04-2020", "20-04-2020", "20:00", "08:00", "smaller"); //33 false
        // $listHasilTest[] = checkRangeTimeShift("08:00:00", "21-04-2020", "20-04-2020", "20-04-2020", "06:00", "05:00", "smaller"); //34 false
        // $listHasilTest[] = checkRangeTimeShift("08:00:00", "21-04-2020", "20-04-2020", "20-04-2020", "20:00", "22:00", "smaller"); //35 false
        // $listHasilTest[] = checkRangeTimeShift("08:00:00", "21-04-2020", "20-04-2020", "20-04-2020", "06:00", "10:00", "smaller"); //36 false
        // $listHasilTest[] = checkRangeTimeShift("08:00:00", "21-04-2020", "20-04-2020", "19-04-2020", "06:00", "10:00", "smaller"); //37 false
        // $listHasilTest[] = checkRangeTimeShift("08:00:00", "21-04-2020", "20-04-2020", "22-04-2020", "06:00", "10:00", "smaller"); //38 true
        
        
        // dd($listHasilTest);

       
        Artisan::call("backup:run");
        

        $yangdihapus = [];
        
        
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
            //dd($dateNow);
            //dd(strtotime("2020-03-20") >= strtotime("2020-04-19"));

            //error_log('shiftId : ' . $itemShift->id);
            //error_log('timeNow : ' . $timeNow);
            //error_log('dateNow : ' . $dateNow);
            //error_log('dateYesterday : ' . $dateYesterday);
            //error_log('startTimeShift : ' . $startTimeShift);
            //error_log('endTimeShift : ' . $endTimeShift);
            //error_log('dateShift : ' . $dateShift);
            $verifyTimeNowIsGreater = checkRangeTimeShift($timeNow, $dateNow, $dateYesterday, $dateShift, $startTimeShift, $endTimeShift, "greater");
            //error_log("verifyTimeNowIsGreater : " . $verifyTimeNowIsGreater);
            //error_log("===========================");
            if($verifyTimeNowIsGreater == true) //if time now is greater than this shift, then delete
            {
                $deleteThisData = true;
                
            }
            

            if($deleteThisData)
            {
                $itemShift->histories()->get()->map(function($itemHistory){
                    $itemHistory->photos()->get()->map(function($itemPhoto){
                        File::delete("storage/" . $itemPhoto->url);
                        $itemPhoto->delete();
                    });
                    $itemHistory->delete();
                });
                $itemShift->delete();
            }
            
        });


       
        
        return response()->json(['error' => false, 'message'=>'success']);
        
    }
}
