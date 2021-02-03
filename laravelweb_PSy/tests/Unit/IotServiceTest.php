<?php

namespace Test\Unit;

use Tests\TestCase;
use App\Repositories\Contracts\IotRepositoryContract;
use App\Repositories\Implementations\IotRepositoryImplementations;
use App\Services\Implementations\IotServiceImplementation;
use App\Exceptions\MqttFailedException;

use App\TestUtil;

class IotServiceTest extends TestCase {

    /**
     * test config gateway function
     * @dataProvider configGatewayProvider
     * @return void
     */
    public function testConfigGateway($iotRepoGetDataRooms, $iotRepoSendConfigGateway, $expectedResult, $verifyIotRepoGetDataRooms, $verifyIotRepoSendConfigGateway)
    {
        //1. create mock for IotRepository
        $iotRepoMock = TestUtil::mockClass(IotRepositoryContract::class, [
            ['method' => 'sendToConfigGatewayTopicMqtt', 'returnOrThrow' => $iotRepoSendConfigGateway],
            ['method' => 'getDataRooms', 'returnOrThrow' => $iotRepoGetDataRooms]
        ]);
        
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
        $verifyIotRepoGetDataRooms ? $iotRepoMock->shouldReceive('GetDataRooms') : $iotRepoMock->shouldNotReceive('GetDataRooms');

        $verifyIotRepoSendConfigGateway ? $iotRepoMock->shouldReceive('sendToConfigGatewayTopicMqtt') : $iotRepoMock->shouldNotReceive('sendToConfigGatewayTopicMqtt');
    }

    public function configGatewayProvider()
    {
        //input variable
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

        //expected output variable
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

        $mqttFailedException = new MqttFailedException();

        //order : 
        //iotRepoGetDataRooms, iotRepoSendConfigGateway, 
        //expectedResult, verifyIotRepoGetDataRooms, verifyIotRepoSendConfigGateway
        return [
            'when rooms data is not empty and success send to mqtt, then return correct data structure' =>
            [
                $dataRooms, true,
                $expectedResultWithRooms, true, true
            ],
            'when rooms data is empty, then return empty data' =>
            [
                [], false,
                $expectedResultEmptyRooms, true, false
            ],
            'when rooms data is not empty but failed send to mqtt, then return correct error' =>
            [
                $dataRooms, $mqttFailedException,
                $mqttFailedException, true, true
            ]
            ];
    }
}