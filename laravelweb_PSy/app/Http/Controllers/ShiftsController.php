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
use App\Services\Contracts\ShiftServiceContract as ShiftService;
use App\Exceptions\GetDataFailedException;


class ShiftsController extends Controller
{
    private $shift;
    private $shiftService;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct(Shift $shift, ShiftService $shiftService)
    {
        $this->shift = $shift;
        $this->shiftService = $shiftService;
    }
    public function index()
    {
        try {
            $data = $this->shiftService->get();
            $response = ['error' => false, 'data'=>$data];
            return response()->json($response);
        } catch (\Throwable $th) {
            dd($th);
            throw new GetDataFailedException('Get Data Failed : Undefined Error');
        }
    }
    public function getShiftToday()
    {
        try {
            $data = $this->shiftService->getToday();
            $response = ['error' => false, 'data'=>$data];
            return response()->json($response);
        } catch (\Throwable $th) {
            dd($th);
            throw new GetDataFailedException('Get Data Failed : Undefined Error');
        }
    }

    public function graph()
    {
        try {
            $data = $this->shiftService->getDashboardData();
            $result = [
                'smallReportData' => [
                    'currentEvent' => $data['currentEvent']
                ],
                'graphData' => $data['graphData'],
                'statusNodeData' => $data['statusNodeData']
            ];
            $response = ['error' => false, 'data'=>$result];
            return response()->json($response);
        } catch (\Throwable $th) {
            dd($th);
            throw new GetDataFailedException('Get Data Failed : Undefined Error');
        }
    }

    public function getHistories(Shift $shift)
    {
        try {
            $data = $this->shiftService->getHistoryScan($shift->id);
            $response = ['error' => false, 'data'=>$data];
            return response()->json($response);
        } catch (\Throwable $th) {
            dd($th);
            throw new GetDataFailedException('Get Data Failed : Undefined Error');
        }

        // $data = $this->shift->find($shift->id)->getHistories();
        // return response()->json(['error' => false, 'data'=>$data]);
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
        try {
            $data = $this->shiftService->removeAndBackup();
            $response = ['error' => false, 'data'=>$data];
            return response()->json($response);
        } catch (\Throwable $th) {
            if($th instanceof DeleteDataFailedException) {
                throw $th;
            } else {
                throw new DeleteDataFailedException('Delete Data Failed : Undefined Error');
            }
        }

    }
}
