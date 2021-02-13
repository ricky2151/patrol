<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;

class AdminShiftTest extends TestCase
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
        $response = $this->actingAs($user)->getJson('/api/admin/shifts');
        
        //3. assert response
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    "*" => ['id', 'date', 'room_name', 'user_name', 'time_start_end', 'total_histories']
                ]
            ])
            ->assertJson([
                "error" => false
            ]);
    }

    /**
     * A basic feature test get today.
     *
     * @return void
     */
    public function testGetToday()
    {
        //1. find user to use as an actor
        $user = User::where('username', 'test_admin')->firstOrFail();

        //2. hit API
        $response = $this->actingAs($user)->getJson('/api/admin/shifts/shifttoday');
        
        //3. assert response
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    "*" => ['id', 'room_name', 'user_name', 'time_start_end', 'total_histories']
                ]
            ])
            ->assertJson([
                "error" => false
            ]);
    }

    /**
     * A basic feature test get histories
     * @dataProvider getHistoriesProvider
     * @return void
     */
    public function testGetHistories($id, $statusExpected, $jsonExpected, $jsonStructure)
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

    public function getHistoriesProvider() {
        
        $responseWithShiftJsonStructure = [
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
        $responseWithShiftAssertJson = [
            'error' => false,
        ];
        $responseWithInvalidIdShiftJsonStructure = [
            'error','code','message'
        ];
        $responseWithInvalidIdShiftAssertJson = [
            "error" => true,
            'code' => 'E-0033',
            "message" => "data not found"
        ];
        

        return [
            '1. When id is valid, then return correct data' => [1, 200, $responseWithShiftAssertJson, $responseWithShiftJsonStructure],
            '2. When id is invalid, then return correct error' => [999999, 400, $responseWithInvalidIdShiftAssertJson, $responseWithInvalidIdShiftJsonStructure], 
        ];
    }

    /**
     * A basic feature test removeAndBackup.
     *
     * @return void
     */
    public function testRemoveAndBackup()
    {
        $this->markTestSkipped('library spatie/backup masih error');
        //1. find user to use as an actor
        $user = User::where('username', 'test_admin')->firstOrFail();

        //2. hit API
        $response = $this->actingAs($user)->postJson('/api/admin/shifts/removeAndBackup');
        
        //3. assert response
        $response
            ->assertStatus(200)
            ->assertJson([
                "error" => false
            ]);
    }

}
