<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Gateway;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;


class IotTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void {
        parent::setUp();
        //1. clear database and migrate
        Artisan::call('migrate:fresh');

        //2. run roletableseeder to make role data
        Artisan::call('db:seed', [
            '--class' => 'RolesTableSeeder'
        ]);

        //3. run usertableseeder to make user data
        Artisan::call('db:seed', [
            '--class' => 'UsersTableSeeder'
        ]);

        //4. run buildingtableseeder to make building data
        Artisan::call('db:seed', [
            '--class' => 'BuildingsTableSeeder'
        ]);

        //5. run floortableseeder to make floor data
        Artisan::call('db:seed', [
            '--class' => 'FloorsTableSeeder'
        ]);
    }
    /**
     * feature config gateway
     * @dataProvider configGatewayProvider
     * @return void
     */
    public function testConfigGateway($dataRooms, $statusExpected, $jsonExpected)
    {
        $this->markTestSkipped('shiftr.io sedang error');
        //1. make gateway data
        if(array_key_exists('gateways', $dataRooms))
        {
            for($i = 0;$i<count($dataRooms['gateways']);$i++)
            {
                factory(Gateway::class, 1)->create([
                    'name' => $dataRooms['gateways'][$i]['name'],
                ]);
            }
        }

        //2. make rooms data
        if(array_key_exists('rooms', $dataRooms))
        {
            for($i = 0;$i<count($dataRooms['rooms']);$i++)
            {
                factory(Room::class, 1)->create([
                    'name' => $dataRooms['rooms'][$i]['name'],
                    'gateway_id' => $dataRooms['rooms'][$i]['gateway_id']
                ]);
            }
        }
        
        //3. find user to use as an actor
        $user = User::where('username', 'test_admin')->firstOrFail();

        //4. hit API
        $response = $this->actingAs($user)->json('GET', '/api/admin/iot/configGateway');

        //5. assert response
        $response
            ->assertStatus($statusExpected)
            ->assertJson($jsonExpected);

    }

    public function configGatewayProvider() {

        $dataRoomsWithDifferentGateways = [
            "gateways" => [
                [
                    "name" => "GT-A",
                ],
                [
                    "name" => "GT-B",
                ]
            ],
            "rooms" => [
                [
                    "name" => "Rooms A",
                    "gateway_id" => 1
                ],
                [
                    "name" => "Rooms B",
                    "gateway_id" => 1
                ],
                [
                    "name" => "Rooms C",
                    "gateway_id" => 2
                ],
                [
                    "name" => "Rooms D",
                    "gateway_id" => 2
                ]
            ]
        ];
        $responseRoomsWithDifferentGateways = [
            "error" => false,
            "mqtt" => [
                "1" => "1#2",
                "2" => "3#4"
            ],
            "information" => [
                "GT-A" => [
                    "Rooms A",
                    "Rooms B"
                ],
                "GT-B" => [
                    "Rooms C",
                    "Rooms D"
                ]
            ]
        ];

        $fourDataRoomsInOneGateway = [
            "gateways" => [
                [
                    "name" => "GT-A",
                ],
            ],
            "rooms" => [
                [
                    "name" => "Rooms A",
                    "gateway_id" => 1
                ],
                [
                    "name" => "Rooms B",
                    "gateway_id" => 1
                ],
                [
                    "name" => "Rooms C",
                    "gateway_id" => 1
                ],
                [
                    "name" => "Rooms D",
                    "gateway_id" => 1
                ]
            ]
        ];

        $responseFourRoomsInOneGateway = [
            "error" => false,
            "mqtt" => [
                "1" => "1#2#3#4",
            ],
            "information" => [
                "GT-A" => [
                    "Rooms A",
                    "Rooms B",
                    "Rooms C",
                    "Rooms D"
                ],
            ]
        ];

        $oneDataRoomsInOneGateway = [
            "gateways" => [
                [
                    "name" => "GT-A",
                ],
            ],
            "rooms" => [
                [
                    "name" => "Rooms A",
                    "gateway_id" => 1
                ],
            ]
        ];

        $responseOneRoomsInOneGateway = [
            "error" => false,
            "mqtt" => [
                "1" => "1",
            ],
            "information" => [
                "GT-A" => [
                    "Rooms A",
                ],
            ]
        ];

        $emptyResponse = [
            "error" => false,
            "mqtt" => [],
            "information" => [],
        ];
        //[datarooms, status code, response message]
        return [
            "1. when data room(node) is empty, then return empty json" => [[], 200, $emptyResponse],
            "2. when there is 2 rooms(node) in gateway A and 2 rooms(node) in gateway B, then return correct data" => [$dataRoomsWithDifferentGateways, 200, $responseRoomsWithDifferentGateways],
            "3. when there is 4 rooms(node) in gateway A, then return correct data" => [$fourDataRoomsInOneGateway, 200, $responseFourRoomsInOneGateway],
            "4. when there is 1 room(node) in gatewayA, then return correct data" => [$oneDataRoomsInOneGateway, 200, $responseOneRoomsInOneGateway]
        ];
    }
}
