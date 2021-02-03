<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Gateway;
use App\Models\Room;
use App\Models\User;
use App\Models\Shift;
use App\Models\Time;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;


class GuardShiftTest extends TestCase
{
    use RefreshDatabase;
     
    protected $idShiftToSubmit;
    protected $startTimeShiftToSubmit;

    protected function setUp(): void {
        parent::setUp();
        //1. clear database and migrate
        Artisan::call('migrate:fresh --seed');

        //2. make 1 data shift
        //user_id = 1, room_id = 1, time_id = depends on time now
        $times = Time::all();
        $idTimeSelected = 0;
        foreach($times as $time) {
            $startTime = Carbon::createFromFormat('H:i', $time->start);
            $endTime = Carbon::createFromFormat('H:i', $time->end);
            if(now()->between($startTime, $endTime, true)) {
                $idTimeSelected = $time->id;
                $this->startTimeShiftToSubmit = $startTime;
                break;
            }
        }
        $this->idShiftToSubmit = factory(Shift::class, 1)->create([
            'time_id' => $idTimeSelected
        ])[0]->id;

    }

    /**
     * feature get shift
     * @return void
     */
    public function testGetShift()
    {
        //1. find user to use as an actor
        $user = User::where('username', 'test_guard')->firstOrFail();

        //2. hit API
        $response = $this->actingAs($user)->getJson('/api/guard/users/shifts');

        //3. assert response
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data'
            ])
            ->assertJson([
                "error" => false
            ]);

    }

    /**
     * feature get master data
     * @return void
     */
    public function testGetMasterData()
    {
        //1. find user to use as an actor
        $user = User::where('username', 'test_guard')->firstOrFail();

        //2. hit API
        $response = $this->actingAs($user)->getJson('/api/guard/users/getMasterData');
        
        //3. assert response
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'status_node'
                ]
            ])
            ->assertJson([
                "error" => false
            ]);

    }

    /**
     * feature view history scan
     * @dataProvider viewHistoryScanProvider
     * @return void
     */
    public function testViewHistoryScan($id, $statusExpected, $jsonExpected)
    {
        //1. find user to use as an actor
        $user = User::where('username', 'test_guard')->firstOrFail();

        //2. hit API
        $response = $this->actingAs($user)->getJson('/api/guard/users/viewHistoryScan/' . $id);

        //3. assert response
        $response
            ->assertStatus($statusExpected)
            ->assertJson($jsonExpected);

    }

    public function viewHistoryScanProvider() {
        //[id, status code, jsonExpected]
        return [
            'when id is valid, then return success with history scan' => [1, 200, [
                "error" => false,
            ]],
            'when id is invalid, then return correct error' => [99999, 400, [
                "error" => true,
                "message" => "data not found"
            ]],
        ];
    }

    /**
     * feature submit scan
     * @dataProvider submitScanProvider
     * @return void
     */
    public function testSubmitScan($id, $mssage, $statusNodeId, $photos, $statusExpected, $jsonExpected)
    {
        
        //1. prepare fake storage
        Storage::fake('photos');

        //2. find user to use as an actor
        $user = User::where('username', 'test_guard')->firstOrFail();

        //3. prepare body
        $body = [
            'message' => $mssage,
            'status_node_id' => $statusNodeId,
        ];

        //3.1. add property id on body
        //if id from data provider is "valid_id" then fill using $this->idShiftToSubmit, else fill 99999
        if($id == '(valid_id)') {
            $body['id'] = $this->idShiftToSubmit;
        } else {
            $body['id'] = 9999999;
        }
        
        //3.2. add properties ['photos'][]['file'] and ['photos][]['photo_time] on body
        $body['photos'] = array();
        for($i = 0;$i<count($photos);$i++) {
            $body['photos'][$i] = array();
            //if photo_time from data provider is "valid_time", then fill using $this->startTimeShiftToSubmit + 1 mintues, else fill $this->startTimeShiftToSubmit + 10 hours (because it's invalid)
            if($photos[$i]['photo_time'] == '(valid_time)') {
                $photo_time = $this->startTimeShiftToSubmit->addMinutes(1)->format('Y-m-d H:i:s');
            } else if ($photos[$i]['photo_time'] == '(invalid_time)') {
                $photo_time = $this->startTimeShiftToSubmit->addMinutes(1)->addHours(10)->format('Y-m-d H:i:s');
            }
            $body['photos'][$i]['file'] = $photos[$i]['file'];
            $body['photos'][$i]['photo_time'] = $photo_time;
        }



        //4. hit API
        $response = $this->actingAs($user)->postJson('/api/guard/users/submitScan/', $body);
        
        //5. assert response
        $response
            ->assertStatus($statusExpected)
            ->assertJson($jsonExpected);

    }

    public function submitScanProvider() {
        //[id, message, statusnodeid, photos, statusExpected, jsonExpected]
        $validId = '(valid_id)';
        $invalidIdShift = '(invalid_id)';
        $validMessage = 'nih laporannya';
        $emptyMessage = '';
        $validStatusNodeId = 1;
        $invalidStatusNodeId = 9999999;
        $validTime = '(valid_time)';
        $invalidTime = '(invalid_time)';
        $onePhotosWithValidFileValidTime = [
            [
                "file" => UploadedFile::fake()->image('photo1.jpg'),
                'photo_time' => $validTime
            ]
        ];

        $onePhotosWithValidFileInvalidTime = [
            [
                "file" => UploadedFile::fake()->image('photo1.jpg'),
                'photo_time' => $invalidTime
            ]
        ];

        $onePhotosWithEmptyFileValidTime = [
            [
                "file" => null,
                'photo_time' => $validTime
            ]
        ];

        $twoPhotosWithValidFileValidTime = [
            [
                "file" => UploadedFile::fake()->image('photo1.jpg'),
                'photo_time' => $validTime
            ],
            [
                "file" => UploadedFile::fake()->image('photo2.jpg'),
                'photo_time' => $validTime
            ]
        ];

        $twoPhotosWithValidFileInvalidTime = [
            [
                "file" => UploadedFile::fake()->image('photo1.jpg'),
                'photo_time' => $invalidTime
            ],
            [
                "file" => UploadedFile::fake()->image('photo2.jpg'),
                'photo_time' => $invalidTime
            ]
        ];

        $twoPhotosWithEmptyFileValidTime = [
            [
                "file" => null,
                'photo_time' => $validTime
            ],
            [
                "file" => null,
                'photo_time' => $validTime
            ]
        ];

        $emptyPhoto = [];

        $statusBadRequest = 400;

        $statusSemanticError = 422;

        $statusSuccess = 200;

        $successResponseMessage = [
            'error' => false,
            'message' => 'submit data success !'
        ];

        $invalidTimeResponseMessage = [
            'error' => true,
            'message' => 'there is an incorrect photo_time data'
        ];

        $emptyOneFileResponseMessage = [
            "error" => true,
            "code" => "E-0031",
            "message" => [
                "photos.0.file" => [
                    "The photos.0.file field is required when photos.0.photo_time / true is present."
                ],
            ]
        ];

        $emptyTwoFileResponseMessage = [
            "error" => true,
            "code" => "E-0031",
            "message" => [
                "photos.0.file" => [
                    "The photos.0.file field is required when photos.0.photo_time / true is present."
                ],
                "photos.1.file" => [
                    "The photos.1.file field is required when photos.1.photo_time / true is present."
                ]
            ]
        ];

        $noPhotoUploadedResponseMessage = [
            "error" => true,
            "message" => "no photo uploaded"
        ];

        $invalidStatusNodeResponseMessage = [
            "error" => true,
            "code" => "E-0031",
            "message" => [
                "status_node_id" => [
                    "The selected status node id is invalid."
                ],
            ]
        ];

        $invalidShiftResponseMessage = [
            "error" => true,
            "code" => "E-0031",
            "message" => [
                "id" => [
                    "The selected id is invalid."
                ],
            ]
        ];

        return [
            'when all body is valid with 1 photo valid, then return success' =>     [$validId, $validMessage, $validStatusNodeId, $onePhotosWithValidFileValidTime, $statusSuccess ,$successResponseMessage],
            'when 1 photo time is invalid, then return correct error' =>            [$validId, $validMessage, $validStatusNodeId, $onePhotosWithValidFileInvalidTime, $statusBadRequest ,$invalidTimeResponseMessage],
            'when 1 photo file is empty, then return correct error' =>              [$validId, $validMessage, $validStatusNodeId, $onePhotosWithEmptyFileValidTime, $statusSemanticError ,$emptyOneFileResponseMessage],
            'when all body is valid with 2 photos valid, then return success' =>    [$validId, $validMessage, $validStatusNodeId, $twoPhotosWithValidFileValidTime, $statusSuccess ,$successResponseMessage],
            'when 2 photo time is invalid, then return correct error' =>            [$validId, $validMessage, $validStatusNodeId, $twoPhotosWithValidFileInvalidTime, $statusBadRequest ,$invalidTimeResponseMessage],
            'when 2 photo file is empty, then return correct error' =>              [$validId, $validMessage, $validStatusNodeId, $twoPhotosWithEmptyFileValidTime, $statusSemanticError ,$emptyTwoFileResponseMessage],
            'when no photo is uploaded, then return correct error' =>               [$validId, $validMessage, $validStatusNodeId, $emptyPhoto, $statusBadRequest ,$noPhotoUploadedResponseMessage],
            'when status node id is invalid, then return correct error' =>          [$validId, $validMessage, $invalidStatusNodeId, $onePhotosWithValidFileValidTime, $statusSemanticError ,$invalidStatusNodeResponseMessage],
            'when message is empty, then return correct error' =>                   [$validId, $emptyMessage, $validStatusNodeId, $onePhotosWithValidFileValidTime, $statusSuccess ,$successResponseMessage],
            'when shift id is invalid, then return correct error' =>                [$invalidIdShift, $emptyMessage, $validStatusNodeId, $onePhotosWithValidFileValidTime, $statusSemanticError ,$invalidShiftResponseMessage],
        ];
    }

    
}
