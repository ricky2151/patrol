<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use App\Models\Shift;
use Carbon\Carbon;

class AdminUserTest extends TestCase
{
    use RefreshDatabase;

    protected $idShiftsToUpdate;

    protected function setUp(): void {
        parent::setUp();

        //clear database and migrate
        Artisan::call('migrate:fresh --seed');

        //make fake shift with date is tomorrow
        $dateTomorrow = Carbon::today()->format('Y-m-d');
        $shiftsCreated = array();
        $shiftsCreated[0] = factory(Shift::class, 1)->create([
            'user_id' => 1,
            'room_id' => 1,
            'time_id' => 1,
            'date' => $dateTomorrow
        ])[0]->id;

        $shiftsCreated[1] = factory(Shift::class, 1)->create([
            'user_id' => 1,
            'room_id' => 1,
            'time_id' => 2,
            'date' => $dateTomorrow
        ])[0]->id;

        $shiftsCreated[2] = factory(Shift::class, 1)->create([
            'user_id' => 1,
            'room_id' => 1,
            'time_id' => 3,
            'date' => $dateTomorrow
        ])[0]->id;
        $this->idShiftsToUpdate = $shiftsCreated;


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
        $response = $this->actingAs($user)->getJson('/api/admin/users');
        
        //3. assert response
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    "*" => ['id', 'name', 'age', 'role_id', 'username', 'phone', 'master_key', 'email']
                ]
            ])
            ->assertJson([
                "error" => false
            ]);
    }

    /**
     * A basic feature test get user's shifts
     * @dataProvider getUserShiftsProvider
     * @return void
     */
    public function testGetUserShifts($id, $statusExpected, $jsonExpected, $jsonStructure)
    {
        //1. find user to use as an actor
        $user = User::where('username', 'test_admin')->firstOrFail();

        //2. hit API
        $response = $this->actingAs($user)->getJson('/api/admin/users/' . $id . '/getAllShifts');

        //3. assert response
        $response->assertStatus($statusExpected);
        if($jsonExpected) {
            $response->assertJson($jsonExpected);
        }
        if($jsonStructure) {
            $response->assertJsonStructure($jsonStructure);
        }
        
            
    }

    public function getUserShiftsProvider() {
        
        $responseWithShiftJsonStructure = [
            'error',
            'data' => [
                '*' => [
                    'id',
                    'user_id',
                    'date',
                    'room_name',
                    'time_start_end'
                ]
            ]
        ];  
        $responseWithShiftAssertJson = [
            'error' => false,
        ];
        $responseWithInvalidIdUserJsonStructure = [
            'error','code','message'
        ];
        $responseWithInvalidIdUserAssertJson = [
            "error" => true,
            'code' => 'E-0033',
            "message" => "data not found"
        ];
        

        return [
            '1. When user id is valid, then return correct data' => [1, 200, $responseWithShiftAssertJson, $responseWithShiftJsonStructure],
            '2. When user id is invalid, then return correct error' => [999999, 400, $responseWithInvalidIdUserAssertJson, $responseWithInvalidIdUserJsonStructure], 
        ];
    }


    /**
     * A basic feature test get shift's history 
     * @dataProvider getShiftHistoriesProvider
     * @return void
     */
    public function testGetShiftHistories($id, $statusExpected, $jsonExpected, $jsonStructure)
    {
        //1. find user to use as an actor
        $user = User::where('username', 'test_admin')->firstOrFail();

        //2. hit API
        $response = $this->actingAs($user)->getJson('/api/admin/shifts/' . $id . '/getHistories');

        //3. assert response
        $response->assertStatus($statusExpected);
        
        if($jsonExpected) {
            $response->assertJson($jsonExpected);
        }
        if($jsonStructure) {
            $response->assertJsonStructure($jsonStructure);
        }
        
            
    }

    public function getShiftHistoriesProvider() {
        
        $responseWithHistoriesJsonStructure = [
            'error',
            'data' => [
                'id',
                'user_id',
                'room_id',
                'time_id',
                'date',
                'room_name',
                'time_name',
                'histories' => [
                    '*' => [
                        'id',
                        'shift_id',
                        'status_node_id',
                        'message',
                        'scan_time',
                        'status_node_name',
                        'photos' => [
                            '*' => [
                                'id',
                                'url',
                                'photo_time'
                            ]
                        ]
                    ]
                ]
            ]
        ];  
        $responseWithHistoriesAssertJson = [
            'error' => false,
        ];
        $responseWithInvalidIdUserJsonStructure = [
            'error','code','message'
        ];
        $responseWithInvalidIdUserAssertJson = [
            "error" => true,
            'code' => 'E-0033',
            "message" => "data not found"
        ];
        

        return [
            '1. When shift id is valid, then return correct data' => [1, 200, $responseWithHistoriesAssertJson, $responseWithHistoriesJsonStructure],
            '2. When shift id is invalid, then return correct error' => [999999, 400, $responseWithInvalidIdUserAssertJson, $responseWithInvalidIdUserJsonStructure], 
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
        $response = $this->actingAs($user)->getJson('/api/admin/users/create');
        
        //3. assert response
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'roles' => [
                        '*' => [
                            'id','name'
                        ]
                    ],
                    'rooms' => [
                        '*' => [
                            'id',
                            'name', 
                            'floor_id', 
                            'building_id', 
                            'gateway_id', 
                            'building' => [
                                'id', 'name'
                            ],
                            'floor' => [
                                'id', 'name'
                            ],
                            'gateway' => [
                                'id', 'name'
                            ]
                        ]
                    ],
                    'status_nodes' => [
                        '*' => [
                            'id','name'
                        ]
                    ],
                    'times' => [
                        '*' => [
                            'id','start', 'end'
                        ]
                    ],
                    'shift_future' => [
                        '*' => [
                            'date',
                            'room_name',
                            'room_id',
                            'time_id',
                            'time_start_end'
                        ]
                    ]
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
    public function testStore($name, $age, $role_id, $username, $password, $phone, $email, $shifts, $statusExpected, $jsonExpected)
    {
        //1. find user to use as an actor
        $user = User::where('username', 'test_admin')->firstOrFail();

        //2. prepare body
        $body = [
            'name' => $name,
            'age' => $age,
            'role_id' => $role_id,
            'username' => $username,
            'password' => $password,
            'phone' => $phone,
            'email' => $email,
        ];

        //3. add properties ['shifts'][]['room_id'], ['shifts'][]['time_id'], ['shifts'][]['date']
        for($i = 0;$i<count($shifts);$i++) {
            $body['shifts'][$i] = array();
            $body['shifts'][$i]['room_id'] = $shifts[$i]['room_id'];
            $body['shifts'][$i]['time_id'] = $shifts[$i]['time_id'];
            $body['shifts'][$i]['date'] = $shifts[$i]['date'];
        }

        //4. hit API
        $response = $this->actingAs($user)->postJson('/api/admin/users', $body);

        //5. assert response
        $response
            ->assertStatus($statusExpected)
            ->assertJson($jsonExpected);
    }

    public function storeProvider() {
        //input
        $oneValidShift = [
            [
                'room_id' => 1,
                'time_id' => 1,
                'date' => '06/07/2019'
            ],
        ];
        $threeValidShifts = [
            [
                'room_id' => 1,
                'time_id' => 1,
                'date' => '06/07/2019'
            ],
            [
                'room_id' => 1,
                'time_id' => 2,
                'date' => '06/07/2019'
            ],
            [
                'room_id' => 1,
                'time_id' => 3,
                'date' => '06/07/2019'
            ],
        ];
        $oneShiftWithOneInvalidRoom = [
            [
                'room_id' => 999999,
                'time_id' => 1,
                'date' => '06/07/2019'
            ],
        ];
        $threeShiftsWithThreeInvalidRooms = [
            [
                'room_id' => 999999,
                'time_id' => 1,
                'date' => '06/07/2019'
            ],
            [
                'room_id' => 999999,
                'time_id' => 2,
                'date' => '06/07/2019'
            ],
            [
                'room_id' => 999999,
                'time_id' => 3,
                'date' => '06/07/2019'
            ],
        ];
        //output
        $successResponseMessage = ['error' => false, "message" => "create data success !"];
        $oneInvalidShiftResponseMessage = [
            "error" => true,
            "code" => "E-0031",
            "message" => [
                "shifts.0.room_id" => [
                    "The selected shifts.0.room_id is invalid."
                ],
            ]
        ];

        $threeInvalidShiftsResponseMessage = [
            "error" => true,
            "code" => "E-0031",
            "message" => [
                "shifts.0.room_id" => [
                    "The selected shifts.0.room_id is invalid."
                ],
                "shifts.1.room_id" => [
                    "The selected shifts.1.room_id is invalid."
                ],
                "shifts.2.room_id" => [
                    "The selected shifts.2.room_id is invalid."
                ]
            ]
        ];
        $emptyEmailResponseMessage = [
            "error" => true,
            "code" => "E-0031",
            "message" => [
                "email" => [
                    "The email field is required."
                ]
            ]
        ];
        $emptyPhoneResponseMessage = [
            "error" => true,
            "code" => "E-0031",
            "message" => [
                "phone" => [
                    "The phone field is required."
                ]
            ]
        ];
        $emptyPasswordResponseMessage = [
            "error" => true,
            "code" => "E-0031",
            "message" => [
                "password" => [
                    "The password field is required."
                ]
            ]
        ];
        $emptyUsernameResponseMessage = [
            "error" => true,
            "code" => "E-0031",
            "message" => [
                "username" => [
                    "The username field is required."
                ]
            ]
        ];
        $invalidRoleIdResponseMessage = [
            "error" => true,
            "code" => "E-0031",
            "message" => [
                "role_id" => [
                    "The selected role id is invalid."
                ],
            ]
        ];
        $emptyRoleIdResponseMessage = [
            "error" => true,
            "code" => "E-0031",
            "message" => [
                "role_id" => [
                    "The role id field is required."
                ]
            ]
        ];
        $emptyAgeResponseMessage = [
            "error" => true,
            "code" => "E-0031",
            "message" => [
                "age" => [
                    "The age field is required."
                ]
            ]
        ];
        $emptyNameResponseMessage = [
            "error" => true,
            "code" => "E-0031",
            "message" => [
                "name" => [
                    "The name field is required."
                ]
            ]
        ];

        return [
            '1. When all input are valid, then return success' => ['ricky', 23, 1, 'ricky2151', 'guard123', '085727933233', 'samuel.ricky@ti.ukdw.ac.id', $oneValidShift, 200, $successResponseMessage],
            '2. When all input are valid, then return success' => ['ricky', 23, 1, 'ricky2151', 'guard123', '085727933233', 'samuel.ricky@ti.ukdw.ac.id', $threeValidShifts, 200, $successResponseMessage],
            '3. When there is 1 invalid shift, then return correct error' => ['ricky', 23, 1, 'ricky2151', 'guard123', '085727933233', 'samuel.ricky@ti.ukdw.ac.id', $oneShiftWithOneInvalidRoom, 422, $oneInvalidShiftResponseMessage],
            '4. When there is 3 invalid shift, then return correct error' => ['ricky', 23, 1, 'ricky2151', 'guard123', '085727933233', 'samuel.ricky@ti.ukdw.ac.id', $threeShiftsWithThreeInvalidRooms, 422, $threeInvalidShiftsResponseMessage],
            '5. When email is empty, then return correct error' => ['ricky', 23, 1, 'ricky2151', 'guard123', '085727933233', null, $oneValidShift, 422, $emptyEmailResponseMessage],
            '6. When phone is empty, then return correct error' => ['ricky', 23, 1, 'ricky2151', 'guard123', null, 'samuel.ricky@ti.ukdw.ac.id', $oneValidShift, 422, $emptyPhoneResponseMessage],
            '7. When password is empty, then return correct error' => ['ricky', 23, 1, 'ricky2151', null, '085727933233', 'samuel.ricky@ti.ukdw.ac.id', $oneValidShift, 422, $emptyPasswordResponseMessage],
            '8. When username is empty, then return correct error' => ['ricky', 23, 1, null, 'guard123', '085727933233', 'samuel.ricky@ti.ukdw.ac.id', $oneValidShift, 422, $emptyUsernameResponseMessage],
            '9. When role_id is invalid, then return correct error' => ['ricky', 23, 99999, 'ricky2151', 'guard123', '085727933233', 'samuel.ricky@ti.ukdw.ac.id', $oneValidShift, 422, $invalidRoleIdResponseMessage],
            '10. When role_id is empty, then return correct error' => ['ricky', 23, null, 'ricky2151', 'guard123', '085727933233', 'samuel.ricky@ti.ukdw.ac.id', $oneValidShift, 422, $emptyRoleIdResponseMessage],
            '11. When age is empty, then return correct error' => ['ricky', null, 1, 'ricky2151', 'guard123', '085727933233', 'samuel.ricky@ti.ukdw.ac.id', $oneValidShift, 422, $emptyAgeResponseMessage],
            '12. When name is empty, then return correct error' => [null, 23, 1, 'ricky2151', 'guard123', '085727933233', 'samuel.ricky@ti.ukdw.ac.id', $oneValidShift, 422, $emptyNameResponseMessage],
        ];
    }

    /**
     * A basic feature test update
     * @dataProvider updateProvider
     * @return void
     */
    public function testUpdate($id, $name, $age, $role_id, $username, $password, $phone, $email, $shifts, $statusExpected, $jsonExpected)
    {
        //1. find user to use as an actor
        $user = User::where('username', 'test_admin')->firstOrFail();

        //2. prepare body
        $body = [
            'name' => $name,
            'age' => $age,
            'role_id' => $role_id,
            'username' => $username,
            'password' => $password,
            'phone' => $phone,
            'email' => $email,
            '_method' => 'PATCH'
        ];

        //3. add properties ['shifts'][]['room_id'], ['shifts'][]['time_id'], ['shifts'][]['date']
        for($i = 0;$i<count($shifts);$i++) {
            $body['shifts'][$i] = array();
            $body['shifts'][$i]['room_id'] = $shifts[$i]['room_id'];
            $body['shifts'][$i]['time_id'] = $shifts[$i]['time_id'];
            $body['shifts'][$i]['date'] = $shifts[$i]['date'];
            //convert $shift's id to real id according to $this->idShiftsToUpdate
            $tempIdShift = $shifts[$i]['id'];
            if($tempIdShift == '[indexIdShiftsToUpdate_with_index_0]') {
                $body['shifts'][$i]['id'] = $this->idShiftsToUpdate[0];
            } else if($tempIdShift == '[indexIdShiftsToUpdate_with_index_1]') {
                $body['shifts'][$i]['id'] = $this->idShiftsToUpdate[1];
            } else if($tempIdShift == '[indexIdShiftsToUpdate_with_index_2]') {
                $body['shifts'][$i]['id'] = $this->idShiftsToUpdate[2];
            }
            
             
            $body['shifts'][$i]['type'] = $shifts[$i]['type'];
        }

        //4. hit API
        $response = $this->actingAs($user)->postJson('/api/admin/users/' . $id, $body);
        
        //5. assert response
        $response
            ->assertStatus($statusExpected)
            ->assertJson($jsonExpected);
    }

    public function updateProvider() {
        //input
        $oneValidShift = [
            [
                'id' => '[indexIdShiftsToUpdate_with_index_0]',
                'type' => 0,
                'room_id' => 1,
                'time_id' => 1,
                'date' => '06/07/2019'
            ],
        ];
        $threeValidShifts = [
            [
                'id' => '[indexIdShiftsToUpdate_with_index_0]',
                'type' => 0,
                'room_id' => 1,
                'time_id' => 1,
                'date' => '06/07/2019'
            ],
            [
                'id' => '[indexIdShiftsToUpdate_with_index_1]',
                'type' => 0,
                'room_id' => 1,
                'time_id' => 2,
                'date' => '06/07/2019'
            ],
            [
                'id' => '[indexIdShiftsToUpdate_with_index_2]',
                'type' => 0,
                'room_id' => 1,
                'time_id' => 3,
                'date' => '06/07/2019'
            ],
        ];
        $oneShiftWithOneInvalidRoom = [
            [
                'id' => '[indexIdShiftsToUpdate_with_index_0]',
                'type' => 0,
                'room_id' => 999999,
                'time_id' => 1,
                'date' => '06/07/2019'
            ],
        ];
        $threeShiftsWithThreeInvalidRooms = [
            [
                'id' => '[indexIdShiftsToUpdate_with_index_0]',
                'type' => 0,
                'room_id' => 999999,
                'time_id' => 1,
                'date' => '06/07/2019'
            ],
            [
                'id' => '[indexIdShiftsToUpdate_with_index_1]',
                'type' => 0,
                'room_id' => 999999,
                'time_id' => 2,
                'date' => '06/07/2019'
            ],
            [
                'id' => '[indexIdShiftsToUpdate_with_index_2]',
                'type' => 0,
                'room_id' => 999999,
                'time_id' => 3,
                'date' => '06/07/2019'
            ],
        ];
        //output
        $successResponseMessage = ['error' => false, "message" => "update data success !"];
        $oneInvalidShiftResponseMessage = [
            "error" => true,
            "code" => "E-0031",
            "message" => [
                "shifts.0.room_id" => [
                    "The selected shifts.0.room_id is invalid."
                ],
            ]
        ];

        $threeInvalidShiftsResponseMessage = [
            "error" => true,
            "code" => "E-0031",
            "message" => [
                "shifts.0.room_id" => [
                    "The selected shifts.0.room_id is invalid."
                ],
                "shifts.1.room_id" => [
                    "The selected shifts.1.room_id is invalid."
                ],
                "shifts.2.room_id" => [
                    "The selected shifts.2.room_id is invalid."
                ]
            ]
        ];
        $emptyEmailResponseMessage = [
            "error" => true,
            "code" => "E-0031",
            "message" => [
                "email" => [
                    "The email must be a valid email address."
                ]
            ]
        ];
        $emptyPhoneResponseMessage = [
            "error" => true,
            "code" => "E-0031",
            "message" => [
                "phone" => [
                    "The phone must be a string."
                ]
            ]
        ];
        $emptyPasswordResponseMessage = [
            "error" => true,
            "code" => "E-0031",
            "message" => [
                "password" => [
                    "The password must be a string."
                ]
            ]
        ];
        $emptyUsernameResponseMessage = [
            "error" => true,
            "code" => "E-0031",
            "message" => [
                "username" => [
                    "The username must be a string."
                ]
            ]
        ];
        $invalidRoleIdResponseMessage = [
            "error" => true,
            "code" => "E-0031",
            "message" => [
                "role_id" => [
                    "The selected role id is invalid."
                ],
            ]
        ];
        $emptyRoleIdResponseMessage = [
            "error" => true,
            "code" => "E-0031",
            "message" => [
                "role_id" => [
                    "The role id must be an integer."
                ]
            ]
        ];
        $emptyAgeResponseMessage = [
            "error" => true,
            "code" => "E-0031",
            "message" => [
                "age" => [
                    "The age must be an integer."
                ]
            ]
        ];
        $emptyNameResponseMessage = [
            "error" => true,
            "code" => "E-0031",
            "message" => [
                "name" => [
                    "The name must be a string."
                ]
            ]
        ];
        $invalidUserIdResponseMessage = [
            "error" => true,
            'code' => 'E-0033',
            "message" => "data not found"
        ];

        return [
            '1. When all input are valid, then return success' => [1, 'ricky', 23, 1, 'ricky2151', 'guard123', '085727933233', 'samuel.ricky@ti.ukdw.ac.id', $oneValidShift, 200, $successResponseMessage],
            '2. When all input are valid, then return success' => [1, 'ricky', 23, 1, 'ricky2151', 'guard123', '085727933233', 'samuel.ricky@ti.ukdw.ac.id', $threeValidShifts, 200, $successResponseMessage],
            '3. When there is 1 invalid shift, then return correct error' => [1, 'ricky', 23, 1, 'ricky2151', 'guard123', '085727933233', 'samuel.ricky@ti.ukdw.ac.id', $oneShiftWithOneInvalidRoom, 422, $oneInvalidShiftResponseMessage],
            '4. When there is 3 invalid shift, then return correct error' => [1, 'ricky', 23, 1, 'ricky2151', 'guard123', '085727933233', 'samuel.ricky@ti.ukdw.ac.id', $threeShiftsWithThreeInvalidRooms, 422, $threeInvalidShiftsResponseMessage],
            '5. When email is empty, then return correct error' => [1, 'ricky', 23, 1, 'ricky2151', 'guard123', '085727933233', null, $oneValidShift, 422, $emptyEmailResponseMessage],
            '6. When phone is empty, then return correct error' => [1, 'ricky', 23, 1, 'ricky2151', 'guard123', null, 'samuel.ricky@ti.ukdw.ac.id', $oneValidShift, 422, $emptyPhoneResponseMessage],
            '7. When password is empty, then return correct error' => [1, 'ricky', 23, 1, 'ricky2151', null, '085727933233', 'samuel.ricky@ti.ukdw.ac.id', $oneValidShift, 422, $emptyPasswordResponseMessage],
            '8. When username is empty, then return correct error' => [1, 'ricky', 23, 1, null, 'guard123', '085727933233', 'samuel.ricky@ti.ukdw.ac.id', $oneValidShift, 422, $emptyUsernameResponseMessage],
            '9. When role_id is invalid, then return correct error' => [1, 'ricky', 23, 99999, 'ricky2151', 'guard123', '085727933233', 'samuel.ricky@ti.ukdw.ac.id', $oneValidShift, 422, $invalidRoleIdResponseMessage],
            '10. When role_id is empty, then return correct error' => [1, 'ricky', 23, null, 'ricky2151', 'guard123', '085727933233', 'samuel.ricky@ti.ukdw.ac.id', $oneValidShift, 422, $emptyRoleIdResponseMessage],
            '11. When age is empty, then return correct error' => [1, 'ricky', null, 1, 'ricky2151', 'guard123', '085727933233', 'samuel.ricky@ti.ukdw.ac.id', $oneValidShift, 422, $emptyAgeResponseMessage],
            '12. When name is empty, then return correct error' => [1, null, 23, 1, 'ricky2151', 'guard123', '085727933233', 'samuel.ricky@ti.ukdw.ac.id', $oneValidShift, 422, $emptyNameResponseMessage],
            '13. When is is invalid, then return correct error' => [99999, 'ricky', 23, 1, 'ricky2151', 'guard123', '085727933233', 'samuel.ricky@ti.ukdw.ac.id', $oneValidShift, 400, $invalidUserIdResponseMessage],
        ];
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
        $response = $this->actingAs($user)->getJson('/api/admin/users/' . $id . '/edit');
        
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
        
        $responseWithUserJsonStructure = [
            'error',
            'data' => [
                'user' => [
                    'id',
                    'name',
                    'age',
                    'role' => [
                        'id',
                        'name'
                    ], 
                    'username',
                    'phone',
                    'email',
                    'master_key'
                ],
                'shifts' => [
                    '*' => [
                        'id',
                        'room' => [
                            'id',
                            'name'
                        ],
                        'time' => [
                            'id',
                            'name'
                        ],
                        'date'
                    ]
                ]
            ]
        ];  
        $responseWithUserAssertJson = [
            'error' => false,
        ];
        $responseWithInvalidIdUserJsonStructure = [
            'error','code','message'
        ];
        $responseWithInvalidIdUserAssertJson = [
            "error" => true,
            'code' => 'E-0033',
            "message" => "data not found"
        ];
        

        return [
            '1. When id is valid, then return correct data' => [1, 200, $responseWithUserAssertJson, $responseWithUserJsonStructure],
            '2. When id is invalid, then return correct error' => [999999, 400, $responseWithInvalidIdUserAssertJson, $responseWithInvalidIdUserJsonStructure], 
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
        $response = $this->actingAs($user)->deleteJson('/api/admin/users/' . $id);

        //3. assert response
        $response
            ->assertStatus($statusExpected)
            ->assertJson($jsonExpected);
    }

    public function deleteProvider() {
        $successResponseMessage = ['error' => false, "message" => "delete data success !"];
        $invalidUserIdResponseMessage = [
            "error" => true,
            'code' => 'E-0033',
            "message" => "data not found"
        ];
        return [
            '1. When id is valid, then return success' => [1, 200, $successResponseMessage],
            '2. When id is invalid, then return correct error' => [999999, 400, $invalidUserIdResponseMessage], 
        ];
    }

    


    

}
