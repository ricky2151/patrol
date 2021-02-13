<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;

class AdminRoomTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void {
        parent::setUp();

        //clear database and migrate
        Artisan::call('migrate:fresh --seed');


    }

    /**
     * A basic feature test index.
     *
     * @return void
     */
    public function testIndex()
    {
        //1. find user to use as an actor
        $user = User::where('username', 'test_admin')->firstOrFail();

        //2. hit API
        $response = $this->actingAs($user)->getJson('/api/admin/rooms');
        
        //3. assert response
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    "*" => ['id', 'name', 'floor_id', 'building_id', 'gateway_id', 'floor_name', 'building_name', 'gateway_name']
                ]
            ])
            ->assertJson([
                "error" => false
            ]);
    }


    /**
     * A basic feature test store
     * @dataProvider storeProvider
     * @return void
     */
    public function testStore($name, $floor_id, $building_id, $gateway_id, $statusExpected, $jsonExpected)
    {
        //1. find user to use as an actor
        $user = User::where('username', 'test_admin')->firstOrFail();

        //2. prepare body
        $body = [
            'name' => $name,
            'floor_id' => $floor_id,
            'building_id' => $building_id,
            'gateway_id' => $gateway_id
        ];

        //3. hit API
        $response = $this->actingAs($user)->postJson('/api/admin/rooms', $body);

        //4. assert response
        $response
            ->assertStatus($statusExpected)
            ->assertJson($jsonExpected);
    }

    public function storeProvider() {
        $successResponseMessage = ['error' => false, "message" => "create data success !"];
        $emptyNameResponseMessage = [
            "error" => true,
            "code" => "E-0031",
            "message" => [
                "name" => [
                    "The name field is required."
                ]
            ]
        ];
        $emptyFloorIdResponseMessage = [
            "error" => true,
            "code" => "E-0031",
            "message" => [
                "floor_id" => [
                    "The floor id field is required."
                ]
            ]
        ];
        $emptyBuildingIdResponseMessage = [
            "error" => true,
            "code" => "E-0031",
            "message" => [
                "building_id" => [
                    "The building id field is required."
                ]
            ]
        ];
        $emptyGatewayIdResponseMessage = [
            "error" => true,
            "code" => "E-0031",
            "message" => [
                "gateway_id" => [
                    "The gateway id field is required."
                ]
            ]
        ];
        $invalidFloorIdResponseMessage = [
            "error" => true,
            "code" => "E-0031",
            "message" => [
                "floor_id" => [
                    "The selected floor id is invalid."
                ],
            ]
        ];
        $invalidBuildingIdResponseMessage = [
            "error" => true,
            "code" => "E-0031",
            "message" => [
                "building_id" => [
                    "The selected building id is invalid."
                ],
            ]
        ];
        $invalidGatewayIdResponseMessage = [
            "error" => true,
            "code" => "E-0031",
            "message" => [
                "gateway_id" => [
                    "The selected gateway id is invalid."
                ],
            ]
        ];



        return [
            '1. When all input are valid, then return success' => ['Ruang Didaktos', 1, 1, 1, 200, $successResponseMessage],
            '2. When gateway id is invalid, then return correct error' => ['Ruang Didaktos', 1, 1, 99999, 422, $invalidGatewayIdResponseMessage],
            '3. When gateway id is empty, then return correct error' => ['Ruang Didaktos', 1, 1, null, 422, $emptyGatewayIdResponseMessage],
            '4. When building id is invalid, then return correct error' => ['Ruang Didaktos', 1, 99999, 1, 422, $invalidBuildingIdResponseMessage],
            '5. When building id is empty, then return correct error' => ['Ruang Didaktos', 1, null, 1, 422, $emptyBuildingIdResponseMessage],
            '6. When floor id is invalid, then return correct error' => ['Ruang Didaktos', 99999, 1, 1, 422, $invalidFloorIdResponseMessage],
            '7. When floor id is empty, then return correct error' => ['Ruang Didaktos', null, 1, 1, 422, $emptyFloorIdResponseMessage],
            '8. When name id is empty, then return correct error' => [null, 1, 1, 1, 422, $emptyNameResponseMessage],
        ];
    }

    /**
     * A basic feature test update
     * @dataProvider updateProvider
     * @return void
     */
    public function testUpdate($id, $name, $floor_id, $building_id, $gateway_id, $statusExpected, $jsonExpected)
    {
        //1. find user to use as an actor
        $user = User::where('username', 'test_admin')->firstOrFail();

        //2. prepare body
        $body = [
            'name' => $name,
            'floor_id' => $floor_id,
            'building_id' => $building_id,
            'gateway_id' => $gateway_id,
            '_method' => 'PATCH'
        ];

        //3. hit API
        $response = $this->actingAs($user)->postJson('/api/admin/rooms/' . $id, $body);
        
        //4. assert response
        $response
            ->assertStatus($statusExpected)
            ->assertJson($jsonExpected);
    }

    public function updateProvider() {
        $successResponseMessage = ['error' => false, "message" => "update data success !"];
        $emptyNameResponseMessage = [
            "error" => true,
            "code" => "E-0031",
            "message" => [
                "name" => [
                    "The name must be a string."
                ]
            ]
        ];
        $emptyFloorIdResponseMessage = [
            "error" => true,
            "code" => "E-0031",
            "message" => [
                "floor_id" => [
                    "The floor id must be an integer."
                ]
            ]
        ];
        $emptyBuildingIdResponseMessage = [
            "error" => true,
            "code" => "E-0031",
            "message" => [
                "building_id" => [
                    "The building id must be an integer."
                ]
            ]
        ];
        $emptyGatewayIdResponseMessage = [
            "error" => true,
            "code" => "E-0031",
            "message" => [
                "gateway_id" => [
                    "The gateway id must be an integer."
                ]
            ]
        ];
        $invalidFloorIdResponseMessage = [
            "error" => true,
            "code" => "E-0031",
            "message" => [
                "floor_id" => [
                    "The selected floor id is invalid."
                ],
            ]
        ];
        $invalidBuildingIdResponseMessage = [
            "error" => true,
            "code" => "E-0031",
            "message" => [
                "building_id" => [
                    "The selected building id is invalid."
                ],
            ]
        ];
        $invalidGatewayIdResponseMessage = [
            "error" => true,
            "code" => "E-0031",
            "message" => [
                "gateway_id" => [
                    "The selected gateway id is invalid."
                ],
            ]
        ];
        $invalidRoomIdResponseMessage = [
            "error" => true,
            'code' => 'E-0033',
            "message" => "data not found"
        ];
        return [
            '1. When all input are valid, then return success' => [1, 'Ruang Didaktos', 1, 1, 1, 200, $successResponseMessage],
            '2. When gateway id is invalid, then return correct error' => [1, 'Ruang Didaktos', 1, 1, 99999, 422, $invalidGatewayIdResponseMessage],
            '3. When gateway id is empty, then return correct error' => [1, 'Ruang Didaktos', 1, 1, null, 422, $emptyGatewayIdResponseMessage],
            '4. When building id is invalid, then return correct error' => [1, 'Ruang Didaktos', 1, 99999, 1, 422, $invalidBuildingIdResponseMessage],
            '5. When building id is empty, then return correct error' => [1, 'Ruang Didaktos', 1, null, 1, 422, $emptyBuildingIdResponseMessage],
            '6. When floor id is invalid, then return correct error' => [1, 'Ruang Didaktos', 99999, 1, 1, 422, $invalidFloorIdResponseMessage],
            '7. When floor id is empty, then return correct error' => [1, 'Ruang Didaktos', null, 1, 1, 422, $emptyFloorIdResponseMessage],
            '8. When name id is empty, then return correct error' => [1, null, 1, 1, 1, 422, $emptyNameResponseMessage],
            '9. When id is invalid, then return correct error' => [99999, 'Ruang Didaktos', 1, 1, 1, 400, $invalidRoomIdResponseMessage],
        ];
    }


    /**
     * A basic feature test delete
     * @dataProvider deleteProvider
     * @return void
     */
    public function testDelete($id, $statusExpected, $jsonExpected)
    {
        //1. find user to use as an actor
        $user = User::where('username', 'test_admin')->firstOrFail();

        //2. hit API
        $response = $this->actingAs($user)->deleteJson('/api/admin/rooms/' . $id);

        //3. assert response
        $response
            ->assertStatus($statusExpected)
            ->assertJson($jsonExpected);
    }

    public function deleteProvider() {
        $successResponseMessage = ['error' => false, "message" => "delete data success !"];
        $invalidRoomIdResponseMessage = [
            "error" => true,
            'code' => 'E-0033',
            "message" => "data not found"
        ];
        return [
            '1. When id is valid, then return success' => [1, 200, $successResponseMessage],
            '2. When id is invalid, then return correct error' => [999999, 400, $invalidRoomIdResponseMessage], 
        ];
    }

    /**
     * A basic feature test get master data.
     *
     * @return void
     */
    public function testGetMasterData()
    {
        //1. find user to use as an actor
        $user = User::where('username', 'test_admin')->firstOrFail();

        //2. hit API
        $response = $this->actingAs($user)->getJson('/api/admin/rooms/create');
        
        //3. assert response
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'floors' => [
                        '*' => [
                            'id','name'
                        ]
                    ],
                    'buildings' => [
                        '*' => [
                            'id','name'
                        ]
                    ],
                    'gateways' => [
                        '*' => [
                            'id','name'
                        ]
                    ]
                ]
            ])
            ->assertJson([
                "error" => false
            ]);
    }


    /**
     * A basic feature test edit
     * @dataProvider editProvider
     * @return void
     */
    public function testEdit($id, $statusExpected, $jsonExpected, $jsonStructure)
    {
        //1. find user to use as an actor
        $user = User::where('username', 'test_admin')->firstOrFail();

        //2. hit API
        $response = $this->actingAs($user)->getJson('/api/admin/rooms/' . $id . '/edit');

        //3. assert response
        $response->assertStatus($statusExpected);
        if($jsonExpected) {
            $response->assertJson($jsonExpected);
        }
        if($jsonStructure) {
            $response->assertJsonStructure($jsonStructure);
        }
        
            
    }

    public function editProvider() {
        
        $responseWithRoomJsonStructure = [
            'error',
            'data' => [
                'room' => [
                    'id',
                    'name',
                    'floor_id',
                    'building_id',
                    'gateway_id'

                ]
            ]
        ];  
        $responseWithRoomAssertJson = [
            'error' => false,
        ];
        $responseWithInvalidIdRoomJsonStructure = [
            'error','code','message'
        ];
        $responseWithInvalidIdRoomAssertJson = [
            "error" => true,
            'code' => 'E-0033',
            "message" => "data not found"
        ];
        

        return [
            '1. When id is valid, then return correct data' => [1, 200, $responseWithRoomAssertJson, $responseWithRoomJsonStructure],
            '2. When id is invalid, then return correct error' => [999999, 400, $responseWithInvalidIdRoomAssertJson, $responseWithInvalidIdRoomJsonStructure], 
        ];
    }

}
