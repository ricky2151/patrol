<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Salman\Mqtt\MqttClass\Mqtt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Carbon\Carbon;
use App\Models\Room;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use App\Services\Contracts\IotServiceContract as IotService;

class IotController extends Controller
{
    protected $iotService;
    
    public function __construct(IotService $iotService)
    {
        $this->iotService = $iotService;
    }

    public function configGateway()
    {
        $data = $this->iotService->configGateway();
        $response = ['error' => false, 'mqtt' => $data['mqtt'], 'information' => $data['information']];
        return response()->json($response);
    }
}
