<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;

class AdminFloorTest extends TestCase
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
        $response = $this->actingAs($user)->getJson('/api/admin/floors');
        
        //3. assert response
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    "*" => ['id', 'name']
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
    public function testStore($name, $statusExpected, $jsonExpected)
    {
        //1. find user to use as an actor
        $user = User::where('username', 'test_admin')->firstOrFail();

        //2. prepare body
        $body = [
            'name' => $name
        ];

        //3. hit API
        $response = $this->actingAs($user)->postJson('/api/admin/floors', $body);

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
        return [
            '1. When name is valid, then return success' => ['Lantai 1', 200, $successResponseMessage],
            '2. When name is empty, then return correct error' => [null, 422, $emptyNameResponseMessage],
        ];
    }

    /**
     * A basic feature test update
     * @dataProvider updateProvider
     * @return void
     */
    public function testUpdate($id, $name, $statusExpected, $jsonExpected)
    {
        //1. find user to use as an actor
        $user = User::where('username', 'test_admin')->firstOrFail();

        //2. prepare body
        $body = [
            'name' => $name,
            '_method' => 'PATCH'
        ];

        //3. hit API
        $response = $this->actingAs($user)->postJson('/api/admin/floors/' . $id, $body);
        
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
        $invalidFloorIdResponseMessage = [
            "error" => true,
            'code' => 'E-0033',
            "message" => "data not found"
        ];
        return [
            '1. When name is valid, then return success' => [1, 'Lantai 6', 200, $successResponseMessage],
            '2. When name is empty, then return correct error' => [1, null, 422, $emptyNameResponseMessage],
            '3. When id is invalid, then return correct error' => [999999, 'Lantai 6', 400, $invalidFloorIdResponseMessage],
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
        $response = $this->actingAs($user)->deleteJson('/api/admin/floors/' . $id);

        //3. assert response
        $response
            ->assertStatus($statusExpected)
            ->assertJson($jsonExpected);
    }

    public function deleteProvider() {
        $successResponseMessage = ['error' => false, "message" => "delete data success !"];
        $invalidFloorIdResponseMessage = [
            "error" => true,
            'code' => 'E-0033',
            "message" => "data not found"
        ];
        return [
            '1. When id is valid, then return success' => [1, 200, $successResponseMessage],
            '2. When id is invalid, then return correct error' => [999999, 400, $invalidFloorIdResponseMessage], 
        ];
    }

}
