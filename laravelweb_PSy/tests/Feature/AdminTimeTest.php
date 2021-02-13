<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;

class AdminTimeTest extends TestCase
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
        $response = $this->actingAs($user)->getJson('/api/admin/times');
        
        //3. assert response
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    "*" => ['id', 'start', 'end']
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
    public function testStore($start, $end, $statusExpected, $jsonExpected)
    {
        //1. find user to use as an actor
        $user = User::where('username', 'test_admin')->firstOrFail();

        //2. prepare body
        $body = [
            'start' => $start,
            'end' => $end
        ];

        //3. hit API
        $response = $this->actingAs($user)->postJson('/api/admin/times', $body);

        //4. assert response
        $response
            ->assertStatus($statusExpected)
            ->assertJson($jsonExpected);
    }

    public function storeProvider() {
        $successResponseMessage = ['error' => false, "message" => "create data success !"];
        $emptyStartResponseMessage = [
            "error" => true,
            "code" => "E-0031",
            "message" => [
                "start" => [
                    "The start field is required."
                ]
            ]
        ];
        $emptyEndResponseMessage = [
            "error" => true,
            "code" => "E-0031",
            "message" => [
                "end" => [
                    "The end field is required."
                ]
            ]
        ];
        $invalidStartResponseMessage = [
            "error" => true,
            "code" => "E-0031",
            "message" => [
                "start" => [
                    "The start does not match the format H:i."
                ]
            ]
                ];
        $invalidEndResponseMessage = [
            "error" => true,
            "code" => "E-0031",
            "message" => [
                "end" => [
                    "The end does not match the format H:i."
                ]
            ]
        ];
        return [
            '1. When start and end are valid, then return success' => ['10:00', '12:00', 200, $successResponseMessage],
            '2. When end is invalid, then return correct error' => ['10:00', '12:00a', 422, $invalidEndResponseMessage],
            '3. When end is empty, then return correct error' => ['10:00', null, 422, $emptyEndResponseMessage],
            '4. When start is invalid, then return correct error' => ['10:00a', '12:00', 422, $invalidStartResponseMessage],
            '5. When start is empty, then return correct error' => [null, '12:00', 422, $emptyStartResponseMessage],
        ];
    }

    /**
     * A basic feature test update
     * @dataProvider updateProvider
     * @return void
     */
    public function testUpdate($id, $start, $end, $statusExpected, $jsonExpected)
    {
        //1. find user to use as an actor
        $user = User::where('username', 'test_admin')->firstOrFail();

        //2. prepare body
        $body = [
            'start' => $start,
            'end' => $end,
            '_method' => 'PATCH'
        ];

        //3. hit API
        $response = $this->actingAs($user)->postJson('/api/admin/times/' . $id, $body);
        
        //4. assert response
        $response
            ->assertStatus($statusExpected)
            ->assertJson($jsonExpected);
    }

    public function updateProvider() {
        $successResponseMessage = ['error' => false, "message" => "update data success !"];
        $emptyStartResponseMessage = [
            "error" => true,
            "code" => "E-0031",
            "message" => [
                "start" => [
                    "The start must be a string.",
                    "The start does not match the format H:i."
                ]
            ]
        ];
        $emptyEndResponseMessage = [
            "error" => true,
            "code" => "E-0031",
            "message" => [
                "end" => [
                    "The end must be a string.",
                    "The end does not match the format H:i."
                ]
            ]
        ];
        $invalidStartResponseMessage = [
            "error" => true,
            "code" => "E-0031",
            "message" => [
                "start" => [
                    "The start does not match the format H:i."
                ]
            ]
        ];
        $invalidEndResponseMessage = [
            "error" => true,
            "code" => "E-0031",
            "message" => [
                "end" => [
                    "The end does not match the format H:i."
                ]
            ]
        ];
        $invalidTimeIdResponseMessage = [
            "error" => true,
            'code' => 'E-0033',
            "message" => "data not found"
        ];
        return [
            '1. When start and end are valid, then return success' => [1, '10:00', '12:00', 200, $successResponseMessage],
            '2. When end is invalid, then return correct error' => [1, '10:00', '12:00a', 422, $invalidEndResponseMessage],
            '3. When end is empty, then return correct error' => [1, '10:00', null, 422, $emptyEndResponseMessage],
            '4. When start is invalid, then return correct error' => [1, '10:00a', '12:00', 422, $invalidStartResponseMessage],
            '5. When start is empty, then return correct error' => [1, null, '12:00', 422, $emptyStartResponseMessage],
            '6. When id is invalid, then return correct error' => [999999, '10:00', '12:00', 400, $invalidTimeIdResponseMessage],
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
        $response = $this->actingAs($user)->deleteJson('/api/admin/times/' . $id);

        //3. assert response
        $response
            ->assertStatus($statusExpected)
            ->assertJson($jsonExpected);
    }

    public function deleteProvider() {
        $successResponseMessage = ['error' => false, "message" => "delete data success !"];
        $invalidTimeIdResponseMessage = [
            "error" => true,
            'code' => 'E-0033',
            "message" => "data not found"
        ];
        return [
            '1. When id is valid, then return success' => [1, 200, $successResponseMessage],
            '2. When id is invalid, then return correct error' => [999999, 400, $invalidTimeIdResponseMessage], 
        ];
    }

}
