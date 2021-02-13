<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;

class AdminGatewayTest extends TestCase
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
        $response = $this->actingAs($user)->getJson('/api/admin/gateways');
        
        //3. assert response
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    "*" => ['id', 'name', 'location']
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
    public function testStore($name, $location, $statusExpected, $jsonExpected)
    {
        //1. find user to use as an actor
        $user = User::where('username', 'test_admin')->firstOrFail();

        //2. prepare body
        $body = [
            'name' => $name,
            'location' => $location
        ];

        //3. hit API
        $response = $this->actingAs($user)->postJson('/api/admin/gateways', $body);

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
        $emptyLocationResponseMessage = [
            "error" => true,
            "code" => "E-0031",
            "message" => [
                "location" => [
                    "The location field is required."
                ]
            ]
        ];
        return [
            '1. When name and location are valid, then return success' => ['Gateway Didaktos', 'Deket biro 1', 200, $successResponseMessage],
            '2. When location is empty, then return correct error' => ['Gateway Didaktos', null, 422, $emptyLocationResponseMessage],
            '3. When name is empty, then return correct error' => [null, 'Deket biro 1', 422, $emptyNameResponseMessage],
        ];
    }

    /**
     * A basic feature test update
     * @dataProvider updateProvider
     * @return void
     */
    public function testUpdate($id, $name, $location, $statusExpected, $jsonExpected)
    {
        //1. find user to use as an actor
        $user = User::where('username', 'test_admin')->firstOrFail();

        //2. prepare body
        $body = [
            'name' => $name,
            'location' => $location,
            '_method' => 'PATCH'
        ];

        //3. hit API
        $response = $this->actingAs($user)->postJson('/api/admin/gateways/' . $id, $body);
        
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
        $emptyLocationResponseMessage = [
            "error" => true,
            "code" => "E-0031",
            "message" => [
                "location" => [
                    "The location must be a string."
                ]
            ]
        ];
        $invalidGatewayIdResponseMessage = [
            "error" => true,
            'code' => 'E-0033',
            "message" => "data not found"
        ];
        return [
            '1. When name and location are valid, then return success' => [1, 'Gateway Didaktos', 'Deket biro 1', 200, $successResponseMessage],
            '2. When location is empty, then return correct error' => [1, 'Gateway Didaktos', null, 422, $emptyLocationResponseMessage],
            '3. When name is empty, then return correct error' => [1, null, 'Deket Biro 1', 422, $emptyNameResponseMessage],
            '4. When id is invalid, then return correct error' => [999999, 'Lantai 6', 'Deket biro 1', 400, $invalidGatewayIdResponseMessage],
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
        $response = $this->actingAs($user)->deleteJson('/api/admin/gateways/' . $id);

        //3. assert response
        $response
            ->assertStatus($statusExpected)
            ->assertJson($jsonExpected);
    }

    public function deleteProvider() {
        $successResponseMessage = ['error' => false, "message" => "delete data success !"];
        $invalidGatewayIdResponseMessage = [
            "error" => true,
            'code' => 'E-0033',
            "message" => "data not found"
        ];
        return [
            '1. When id is valid, then return success' => [1, 200, $successResponseMessage],
            '2. When id is invalid, then return correct error' => [999999, 400, $invalidGatewayIdResponseMessage], 
        ];
    }

}
