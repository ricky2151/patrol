<?php

namespace App\Http\Controllers;
use App\Services\Contracts\IotServiceContract as IotService;
use App\Exceptions\MqttFailedException;

class IotController extends Controller
{
    protected $iotService;
    
    public function __construct(IotService $iotService)
    {
        $this->iotService = $iotService;
    }

    public function configGateway()
    {
        try {
            $data = $this->iotService->configGateway();
            $response = ['error' => false, 'mqtt' => $data['mqtt'], 'information' => $data['information']];
            return response()->json($response);
        } catch (\Throwable $th) {
            throw new MqttFailedException('MQTT Failed : Undefined Error');
        }
        
    }
}
