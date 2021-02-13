<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;

class AdminDashboardTest extends TestCase
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
        $response = $this->actingAs($user)->getJson('/api/admin/shifts/graph');
        
        //3. assert response
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'smallReportData' => [
                        'currentEvent' => [
                            '*' => [
                                'id', 
                                'shift_id', 
                                'status_node_id', 
                                'message', 
                                'scan_time', 
                                'shift' => [
                                    'id',
                                    'user_id',
                                    'room_id',
                                    'time_id',
                                    'room' => [
                                        'id',
                                        'name'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'graphData' => [
                        '*' => [
                            'year',
                            'month',
                            'status_nodes',
                            'status_nodes_id',
                            'count'
                        ]
                    ],
                    'statusNodeData' => [
                        '*' => [
                            'id',
                            'name'
                        ]
                    ]

                    //"*" => ['id', 'name', 'location']
                ]
            ])
            ->assertJson([
                "error" => false
            ]);
    }


    

}
