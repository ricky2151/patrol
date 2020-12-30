<?php

namespace Test\Unit;

use Tests\TestCase;
use App\Repositories\Contracts\IotRepositoryContract;
use App\Repositories\Implementations\IotRepositoryImplementations;
use App\Services\Implementations\IotServiceImplementation;
use App\Exceptions\MqttFailedException;

class IotServiceTest extends TestCase {
    /**
     * test config gateway function
     * @dataProvider configGatewayProvider
     * @return void
     */
    public function testConfigGateway($iotRepoGetDataRooms, $iotRepoSendConfigGateway, $expectedResult, $verifySendConfigGateway)
    {
        //1. create mock for IotRepository
        $iotRepoMock = $this->mock(IotRepositoryContract::class, function ($iotRepoMock) use ($iotRepoGetDataRooms, $iotRepoSendConfigGateway, $verifySendConfigGateway){
            if(is_bool($iotRepoSendConfigGateway))
            {
                $iotRepoMock->shouldReceive('sendToConfigGatewayTopicMqtt')->andReturn($iotRepoSendConfigGateway);
            }
            else
            {
                $iotRepoMock->shouldReceive('sendToConfigGatewayTopicMqtt')->andThrow($iotRepoSendConfigGateway);
            }

            
            $iotRepoMock->shouldReceive('getDataRooms')->andReturn($iotRepoGetDataRooms);
            
            
        });
        
        //2. make object IotService for testing
        $iotService = new IotServiceImplementation($iotRepoMock);

        //3. call the function to be tested
        $thereIsException = false;
        try {
            $result = $iotService->configGateway();
        }
        catch (\Exception $e) //if there is an exception, then the exception should be same as expectedResult (because, expected result is a exeption)
        {
            $this->assertEquals($e, $expectedResult);
            $thereIsException = true;
        }

        //4. assert result just only if there is no exception when calling method 
        if(!$thereIsException)
        {
            $this->assertSame($result, $expectedResult);
        }
        
        //5. verify that mocked method is called
        if($verifySendConfigGateway)
        {
            $iotRepoMock->shouldReceive('sendToConfigGatewayTopicMqtt');
        }
        else
        {
            $iotRepoMock->shouldNotReceive('sendToConfigGatewayTopicMqtt');
        }
    }

    public function configGatewayProvider()
    {
        $dataRooms = [
            [
                "id" => 1,
                "name" => "ruangan A",
                "floor_id" => 1,
                "building_id" => 1,
                "gateway_id" => 3,
                "floor_name" => "lantai 1",
                "building_name" => "gedung agape",
                "gateway_name" => "GT-3"
            ],
            [
                "id" => 2,
                "name" => "ruangan B",
                "floor_id" => 1,
                "building_id" => 1,
                "gateway_id" => 3,
                "floor_name" => "lantai 1",
                "building_name" => "gedung agape",
                "gateway_name" => "GT-3"
            ],
            [
                "id" => 3,
                "name" => "ruangan C",
                "floor_id" => 1,
                "building_id" => 1,
                "gateway_id" => 2,
                "floor_name" => "lantai 1",
                "building_name" => "gedung agape",
                "gateway_name" => "GT-2"
            ]
        ];

        $expectedResultWithRooms = 
        [
            'mqtt' => [
                "3" => "1#2",
                "2" => "3"
            ],
            'information' => [
                "GT-3" => [
                    "ruangan A",
                    "ruangan B"
                ],
                "GT-2" => [
                    "ruangan C"
                ]
            ]
        ];

        $expectedResultEmptyRooms = 
        [
            'mqtt' => [],
            'information' => []
        ];

        $mqttFailedException = new MqttFailedException("cannot send data to topic 'config-gateway'");

        //order : 
        //iotRepoGetDataRooms, iotRepoSendConfigGateway, 
        //expectedResult, verifySendConfigGateway
        return [
            'when rooms data is not empty and success send to mqtt, then return correct data structure' =>
            [
                $dataRooms, true,
                $expectedResultWithRooms, true
            ],
            'when rooms data is empty, then return empty data' =>
            [
                [], false,
                $expectedResultEmptyRooms, false
            ],
            'when rooms data is not empty but failed send to mqtt, then return correct error' =>
            [
                $dataRooms, $mqttFailedException,
                $mqttFailedException, true
            ]
            ];
    }
}