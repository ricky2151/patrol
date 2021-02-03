<?php

namespace Test\Unit;

use Tests\TestCase;
use App\Repositories\Contracts\UserRepositoryContract;
use App\Repositories\Contracts\ShiftRepositoryContract;
use App\Repositories\Contracts\PhotoRepositoryContract;
use App\Repositories\Contracts\HistoryRepositoryContract;
use App\Repositories\Contracts\AuthRepositoryContract;
use App\Repositories\Contracts\StatusNodeRepositoryContract;

use App\Services\Implementations\UserServiceImplementation;

use App\Exceptions\LoginFailedException;
use App\Exceptions\SaveFileFailedException;
use App\Exceptions\StoredataFailedException;
use App\Exceptions\GetDataFailedException;
use App\Exceptions\SuspiciousInputException;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use App\TestUtil;

class UserServiceTest extends TestCase {

    /**
     * test get my shift function
     * @dataProvider getShiftsThatCanBeScannedProvider
     * @return void
     */
    public function testGetShiftsThatCanBeScanned($userRepoGetShiftsThatCanBeScanned, $expectedResult, $verifyUserRepoGetShiftsThatCanBeScanned)
    {
        //1. create mock for userRepository
        $userRepoMock = TestUtil::mockClass(UserRepositoryContract::class, [['method' => 'getShiftsThatCanBeScanned', 'returnOrThrow' => $userRepoGetShiftsThatCanBeScanned]]);
        
        //2. make object UserService for testing
        $userService = new UserServiceImplementation($userRepoMock, resolve(ShiftRepositoryContract::class), resolve(PhotoRepositoryContract::class), resolve(HistoryRepositoryContract::class), resolve(AuthRepositoryContract::class), resolve(StatusNodeRepositoryContract::class));
        

        //3. call the function to be tested
        $thereIsException = false;
        try {
            $result = $userService->getShiftsThatCanBeScanned();
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
        $verifyUserRepoGetShiftsThatCanBeScanned ? $userRepoMock->shouldReceive('getShiftsThatCanBeScanned') : $userRepoMock->shouldNotReceive('getShiftsThatCanBeScanned');

    }

    public function getShiftsThatCanBeScannedProvider()
    {
        //input variable

        $userRepoGetShiftsThatCanBeScannedReturnThreeData = [
            [
                "id" => 6,
                "room" => "Ruangan Hasna Hani Palastri S.Farm",
                "time_start" => "06:00",
                "time_end" => "10:00",
                "date" => "2021-02-01",
                "countScanned" => 0,
                "last_scan" => null
            ],
            [
                "id" => 10,
                "room" => "Ruangan Parman Wakiman Irawan",
                "time_start" => "10:00",
                "time_end" => "14:00",
                "date" => "2021-02-01",
                "countScanned" => 0,
                "last_scan" => null
            ],
            [
                "id" => 11,
                "room" => "Ruangan Parman Wakiman Irawan",
                "time_start" => "14:00",
                "time_end" => "18:00",
                "date" => "2021-02-01",
                "countScanned" => 0,
                "last_scan" => null
            ]
        ];

        $userRepoGetShiftsThatCanBeScannedReturnNoData = [];

        $userRepoGetShiftsThatCanBeScannedThrowGetFailed = new GetDataFailedException();

        //output expected variable

        $expectedResultWithThreeData = $userRepoGetShiftsThatCanBeScannedReturnThreeData;

        $expectedResultEmpty = [];

        $getDataFailedException = new GetDataFailedException();

        //order : 
        //userRepoGetShiftThatCanBeScanned
        //expectedResult, verifyUserRepoGetShiftThatCanBeScanned
        return [
            'when userRepo.getShiftThatCanBeScanned return 3 data, then return that 3 data' =>
            [
                $userRepoGetShiftsThatCanBeScannedReturnThreeData,
                $expectedResultWithThreeData, true
            ],
            'when userRepo.getShiftThatCanBeScanned return no data, then return no data also' =>
            [
                $userRepoGetShiftsThatCanBeScannedReturnNoData,
                $expectedResultEmpty, true
            ],
            'when userRepo.getShiftThatCanBeScanned throw GetDataFailedException, then throw GetDataFailedException also' =>
            [
                $userRepoGetShiftsThatCanBeScannedThrowGetFailed,
                $getDataFailedException, true
            ],
        ];
    }


    /**
     * test get my shift function
     * @dataProvider getMasterDataSubmitScanProvider
     * @return void
     */
    public function testGetMasterDataSubmitScan($statusNodeRepoGetAll, $expectedResult, $verifyStatusNodeRepoGetAll)
    {
        //1. create mock for statusNodeRepository
        $statusNodeRepoMock = TestUtil::mockClass(StatusNodeRepositoryContract::class, [['method' => 'getAll', 'returnOrThrow' => $statusNodeRepoGetAll]]);
        
        //2. make object UserService for testing
        $userService = new UserServiceImplementation(resolve(UserRepositoryContract::class), resolve(ShiftRepositoryContract::class), resolve(PhotoRepositoryContract::class), resolve(HistoryRepositoryContract::class), resolve(AuthRepositoryContract::class), $statusNodeRepoMock);

        //3. call the function to be tested
        $thereIsException = false;
        try {
            $result = $userService->getMasterDataSubmitScan();
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
        $verifyStatusNodeRepoGetAll ? $statusNodeRepoMock->shouldReceive('getAll'):$statusNodeRepoMock->shouldNotReceive('getAll');

    }

    public function getMasterDataSubmitScanProvider()
    {
        //input variable
        $statusNodeRepoGetAllReturnThreeData = [
            [
              "id" => 1,
              "name" => "Aman",
            ],
            [
              "id" => 2,
              "name" => "Mencurigakan",
            ],
            [
              "id" => 3,
              "name" => "Tidak Aman",
            ]
        ];

        $statusNodeRepoGetAllReturnNoData = [];

        $statusNodeRepoGetAllThrowGetFailed = new GetDataFailedException();

        //output expected variable

        $expectedResultWithThreeData = $statusNodeRepoGetAllReturnThreeData;

        $expectedResultEmpty = [];

        $getDataFailedException = new GetDataFailedException();

        //order : 
        //statusNodeRepoGetAll
        //expectedResult, verifyStatusNodeRepoGetAll
        return [
            'when statusNodeRepo.getAll return 3 data, then return that 3 data' =>
            [
                $statusNodeRepoGetAllReturnThreeData,
                $expectedResultWithThreeData, true
            ],
            'when statusNodeRepo.getAll return no data, then return no data also' =>
            [
                $statusNodeRepoGetAllReturnNoData,
                $expectedResultEmpty, true
            ],
            'when statusNodeRepo.getAll throw GetDataFailedException, then throw GetDataFailedException also' =>
            [
                $statusNodeRepoGetAllThrowGetFailed,
                $getDataFailedException, true
            ],
        ];
    }

    /**
     * test view my history scan function
     * @dataProvider viewMyHistoryScanProvider
     * @return void
     */
    public function testViewMyHistoryScan($input, $userRepoViewMyHistoryScan, $expectedResult, $verifyUserRepoViewMyHistoryScan)
    {
        //1. create mock for userRepository
        $userRepoMock = TestUtil::mockClass(UserRepositoryContract::class, [['method' => 'viewMyHistoryScan', 'returnOrThrow' => $userRepoViewMyHistoryScan]]);
        
        //2. make object UserService for testing
        $userService = new UserServiceImplementation($userRepoMock, resolve(ShiftRepositoryContract::class), resolve(PhotoRepositoryContract::class), resolve(HistoryRepositoryContract::class), resolve(AuthRepositoryContract::class), resolve(StatusNodeRepositoryContract::class));

        //3. call the function to be tested
        $thereIsException = false;
        try {
            $result = $userService->viewMyHistoryScan($input['shiftId']);
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
        $verifyUserRepoViewMyHistoryScan ? $userRepoMock->shouldReceive('viewMyHistoryScan') : $userRepoMock->shouldNotReceive('viewMyHistoryScan');

    }

    public function viewMyHistoryScanProvider()
    {
        //input variable
        $inputAny = [
            'shiftId' => 1
        ];

        $userRepoViewMyHistoryScanReturnThreeData = [
            "shift_id" => 3,
            "histories" => [
                [
                    "id" => 1,
                    "status_node_id" => "1",
                    "status_node_name" => "Aman",
                    "message" => "Waduh Mencurigakan !",
                    "scan_time" => "00:00:05",
                    "photos" => [
                        [
                            "id" => 1,
                            "url" => "www.google.com",
                            "photo_time" => "06:00:05"
                        ],
                        [
                            "id" => 2,
                            "url" => "www.google.com",
                            "photo_time" => "06:00:05"
                        ],
                        [
                            "id" => 3,
                            "url" => "www.google.com",
                            "photo_time" => "06:00:05"
                        ]
                    ]
                ],
                [
                    "id" => 20,
                    "status_node_id" => "2",
                    "status_node_name" => "Mencurigakan",
                    "message" => "Sepiii",
                    "scan_time" => "00:00:05",
                    "photos" => []
                ],
                [
                    "id" => 21,
                    "status_node_id" => "2",
                    "status_node_name" => "Mencurigakan",
                    "message" => "Sepiii",
                    "scan_time" => "00:00:06",
                    "photos" => []
                ]
            ]
        ];

        $userRepoViewMyHistoryScanReturnNoData = [];

        $userRepoViewMyHistoryScanThrowGetFailed = new GetDataFailedException();

        //output expected variable

        $expectedResultWithThreeData = $userRepoViewMyHistoryScanReturnThreeData;

        $expectedResultEmpty = [];

        $getDataFailedException = new GetDataFailedException();

        //order : 
        //input, userRepoViewMyHistoryScan
        //expectedResult, verifyUserRepoViewMyHistoryScan
        return [
            'when userRepo.ViewMyHistoryScan return 3 data, then return that 3 data' =>
            [
                $inputAny, $userRepoViewMyHistoryScanReturnThreeData,
                $expectedResultWithThreeData, true
            ],
            'when userRepo.ViewMyHistoryScan return no data, then return no data also' =>
            [
                $inputAny, $userRepoViewMyHistoryScanReturnNoData,
                $expectedResultEmpty, true
            ],
            'when userRepo.ViewMyHistoryScan throw GetDataFailedException, then throw GetDataFailedException also' =>
            [
                $inputAny, $userRepoViewMyHistoryScanThrowGetFailed,
                $getDataFailedException, true
            ],
        ];
    }


    /**
     * test submit scan function
     * @dataProvider submitScanProvider
     * @return void
     */
    public function testSubmitScan($input, $shiftRepoFind, $shiftRepoGetTime, $authRepoIsLogin, $shiftRepoGetRoom, $photoRepoSavePhotoToStorage, $historyRepoStore, $historyRepoInsertPhoto, 
        $expectedResult, $verifyShiftRepoFind, $verifyShiftRepoGetTime, $verifyAuthRepoIsLogin, $verifyShiftRepoGetRoom, $verifyPhotoRepoSave, $verifyHistoryRepoStore, $verifyHistoryRepoInsert)
    {
        //1. create mock for shiftRepository
        $shiftRepoMock = TestUtil::mockClass(ShiftRepositoryContract::class, [
            ['method' => 'find', 'returnOrThrow' => $shiftRepoFind],
            ['method' => 'getTime', 'returnOrThrow' => $shiftRepoGetTime],
            ['method' => 'getRoom', 'returnOrThrow' => $shiftRepoGetRoom]
        ]);

        //2. create mock for authRepository
        $authRepoMock = TestUtil::mockClass(AuthRepositoryContract::class, [
            ['method' => 'isLogin', 'returnOrThrow' => $authRepoIsLogin],
        ]);

        //3. create mock for photoRepository
        $photoRepoMock = TestUtil::mockClass(PhotoRepositoryContract::class, [
            ['method' => 'savePhotoToStorage', 'returnOrThrow' => $photoRepoSavePhotoToStorage],
        ]);

        //4. create mock for historyRepository
        $historyRepoMock = TestUtil::mockClass(HistoryRepositoryContract::class, [
            ['method' => 'store', 'returnOrThrow' => $historyRepoStore],
            ['method' => 'insertPhotos', 'returnOrThrow' => $historyRepoInsertPhoto],
        ]);
        
        //5. make object UserService for testing
        $userService = new UserServiceImplementation(resolve(UserRepositoryContract::class), $shiftRepoMock, $photoRepoMock, $historyRepoMock, $authRepoMock, resolve(StatusNodeRepositoryContract::class));
        
        //6. call the function to be tested
        $thereIsException = false;
        try {
            $result = $userService->submitScan($input['message'], $input['status_node_id'], $input['id'], $input['date_time_now'], $input['photos']);
        }
        catch (\Exception $e) //if there is an exception, then the exception should be same as expectedResult (because, expected result is a exeption)
        {
            $this->assertEquals($e, $expectedResult);
            $thereIsException = true;
        }

        //7. assert result just only if there is no exception when calling method 
        if(!$thereIsException)
        {
            $this->assertSame($result, $expectedResult);
        }
        
        //8. verify that mocked method is called
        $verifyShiftRepoFind ? $shiftRepoMock->shouldReceive('find') : $shiftRepoMock->shouldNotReceive('find');
        $verifyShiftRepoGetTime ? $shiftRepoMock->shouldReceive('getTime') : $shiftRepoMock->shouldNotReceive('getTime');
        $verifyAuthRepoIsLogin ? $authRepoMock->shouldReceive('isLogin') : $authRepoMock->shouldNotReceive('isLogin');
        $verifyShiftRepoGetRoom ? $shiftRepoMock->shouldReceive('getRoom') : $shiftRepoMock->shouldNotReceive('getRoom');
        $verifyPhotoRepoSave ? $photoRepoMock->shouldReceive('savePhotoToStorage') : $photoRepoMock->shouldNotReceive('savePhotoToStorage');
        $verifyHistoryRepoStore ? $historyRepoMock->shouldReceive('store') : $historyRepoMock->shouldNotReceive('store');
        $verifyHistoryRepoInsert ? $historyRepoMock->shouldReceive('insertPhoto') : $historyRepoMock->shouldNotReceive('insertPhoto');
    }

    public function submitScanProvider()
    {
        $baseInput = [
            "message" => "aman",
            "status_node_id" => 1,
            "id" => 1,
            "date_time_now" => '2021-02-01 05:00:00',
            "photos" => [
                [
                    'file' => UploadedFile::fake()->image('photo1.jpg'),
                    'photo_time' => '2021-02-01 05:10:00'
                ],
            ]
        ];
        
        //input variable
        $inputWithOneValidPhoto = $baseInput;

        $inputWithTwoValidPhoto = $baseInput;
        array_push($inputWithTwoValidPhoto['photos'], [
            'file' => UploadedFile::fake()->image('photo2.jpg'),
            'photo_time' => '2021-02-01 05:15:00'
        ]);

        $inputWithOneInvalidPhoto = $baseInput;
        $inputWithOneInvalidPhoto['photos'][0]['photo_time'] = '2021-02-01 18:00:00';

        $inputWithTwoInvalidPhoto = $inputWithTwoValidPhoto;
        $inputWithTwoInvalidPhoto['photos'][0]['photo_time'] = '2021-02-01 18:00:00';
        $inputWithTwoInvalidPhoto['photos'][1]['photo_time'] = '2021-02-01 18:05:00';

        $inputWithEmptyPhoto = $baseInput;
        $inputWithEmptyPhoto['photos'] = [];
        
        $inputWithInvalidDateTimeNow = $baseInput;
        $inputWithInvalidDateTimeNow['date_time_now'] = '2021-01-31 05:00:00';
        

        $shiftRepoFindReturnShiftData = [
            "id" => 1,
            "user_id" => "5",
            "room_id" => "6",
            "time_id" => "1",
            "date" => "2021-02-01",
            "created_at" => "2021-02-01 10:44:33",
            "updated_at" => "2021-02-01 10:44:33"
        ];
        $shiftRepoFindReturnWrongShift = $shiftRepoFindReturnShiftData;
        $shiftRepoFindReturnWrongShift['user_id'] = 10;
        $shiftRepoFindThrowGetFailed = new GetDataFailedException();

        $shiftRepoGetTimeReturnTimeData = [
            "id" => 1,
            "start" => "00:00",
            "end" => "06:00",
            "created_at" => "2021-02-01 10:44:33",
            "updated_at" => "2021-02-01 10:44:33"
        ];
        $shiftRepoGetTimeThrowGetFailed = new GetDataFailedException();

        $authRepoIsLoginReturnCorrectData = ['access_token' => 'xxx', 'user' => [
            'id' => 5,
            'name' => 'ricky'
        ]];
        $authRepoIsLoginThrowLoginFailed = new LoginFailedException();

        $shiftRepoGetRoomReturnRoom = [
            "id" => 6,
            "name" => "Ruangan Eli Hastuti",
            "floor_id" => "5",
            "building_id" => "4",
            "gateway_id" => "3",
            "created_at" => "2021-02-01 10:44:32",
            "updated_at" => "2021-02-01 10:44:32"
        ];
        $shiftRepoGetRoomThrowGetFailed = new GetDataFailedException();

        $photoRepoSaveReturnPath = "photos/2020-11-18172954-Guard-LabAquariumA-H-0.jpg";
        $photoRepoSaveThrowSaveFailed = new SaveFileFailedException();

        $historyRepoSaveReturnSavedData = [
            'id' => 20,
            'shift_id' => $baseInput['id'],
            'status_node_id' => $baseInput['status_node_id'],
            'message' => $baseInput['message'],
            'scan_time' => $baseInput['date_time_now']
        ];
        $historyRepoSaveThrowStoreFailed = new StoreDataFailedException();

        $historyRepoInsertReturnTrue = true;
        $historyRepoInsertThrowStoreFailed = new StoreDataFailedException();

        //output expected variable

        $expectedResultSuccess = true;
        $expectedResultThrowStoreDataFailed = new StoreDataFailedException();
        $expectedResultThrowGetDataFailed = new GetDataFailedException();
        $expectedResultThrowSaveFileFailed = new SaveFileFailedException();
        $expectedResultThrowLoginFailed = new LoginFailedException();
        $expectedResultThrowSuspiciousInputWrongScanTime = new SuspiciousInputException("there is an incorrect photo_time data");
        $expectedResultThrowSuspiciousInputEmptyPhoto = new SuspiciousInputException("no photo uploaded");
        $expectedResultThrowSuspiciousInputServerTimeNotCorrect = new SuspiciousInputException("server time is not correct");
        $expectedResultThrowSuspiciousInputWrongShift = new SuspiciousInputException("this user is not have this shift");

        //order : 
        //input, shiftRepoFind, shiftRepoGetTime, authRepoIsLogin, shiftRepoGetRoom, photoRepoSave, historyRepoSave, historyRepoInsert
        //expectedResult, verifyshiftRepoFind, verifyshiftRepoGetTime, verifyauthRepoIsLogin, verifyshiftRepoGetRoom, verifyphotoRepoSave, verifyhistoryRepoSave, verifyhistoryRepoInsert
        return [
            '1. when input is valid (with 1 photo) and all repo return correctly, then return true' =>
            [
                $inputWithOneValidPhoto, $shiftRepoFindReturnShiftData, $shiftRepoGetTimeReturnTimeData, $authRepoIsLoginReturnCorrectData, $shiftRepoGetRoomReturnRoom, $photoRepoSaveReturnPath, $historyRepoSaveReturnSavedData, $historyRepoInsertReturnTrue,
                $expectedResultSuccess, true, true, true, true, true, true, true
            ],
            '2. when input is valid and historyrepo.insertphoto throw storedatafailed, then throw storedatafailed' =>
            [
                $inputWithOneValidPhoto, $shiftRepoFindReturnShiftData, $shiftRepoGetTimeReturnTimeData, $authRepoIsLoginReturnCorrectData, $shiftRepoGetRoomReturnRoom, $photoRepoSaveReturnPath, $historyRepoSaveReturnSavedData, $historyRepoInsertThrowStoreFailed,
                $expectedResultThrowStoreDataFailed, true, true, true, true, true, true, true
            ],
            '3. when input is valid and historyrepo.store throw storedatafailed, then throw storedatafailed' =>
            [
                $inputWithOneValidPhoto, $shiftRepoFindReturnShiftData, $shiftRepoGetTimeReturnTimeData, $authRepoIsLoginReturnCorrectData, $shiftRepoGetRoomReturnRoom, $photoRepoSaveReturnPath, $historyRepoSaveThrowStoreFailed, null,
                $expectedResultThrowStoreDataFailed, true, true, true, true, true, true, false
            ],
            '4. when input is valid and photorepo.savephototostorage throw savefilefailed, then throw savefilefailed' =>
            [
                $inputWithOneValidPhoto, $shiftRepoFindReturnShiftData, $shiftRepoGetTimeReturnTimeData, $authRepoIsLoginReturnCorrectData, $shiftRepoGetRoomReturnRoom, $photoRepoSaveThrowSaveFailed, null, null,
                $expectedResultThrowSaveFileFailed, true, true, true, true, true, false, false
            ],
            '5. when input is valid and shiftrepo.getroom throw getdatafailed, then throw getdatafailed' =>
            [
                $inputWithOneValidPhoto, $shiftRepoFindReturnShiftData, $shiftRepoGetTimeReturnTimeData, $authRepoIsLoginReturnCorrectData, $shiftRepoGetRoomThrowGetFailed, null, null, null,
                $expectedResultThrowGetDataFailed, true, true, true, true, false, false, false
            ],
            '6. when input is valid and shiftrepo.gettime throw getdatafailed, then throw getdatafailed' =>
            [
                $inputWithOneValidPhoto, $shiftRepoFindReturnShiftData, $shiftRepoGetTimeThrowGetFailed, $authRepoIsLoginReturnCorrectData, null, null, null, null,
                $expectedResultThrowGetDataFailed, true, true, true, false, false, false, false
            ],
            '7. when input is valid and authrepo.islogin throw loginfailed, then throw loginfailed' =>
            [
                $inputWithOneValidPhoto, $shiftRepoFindReturnShiftData, null, $authRepoIsLoginThrowLoginFailed, null, null, null, null,
                $expectedResultThrowLoginFailed, true, false, true, false, false, false, false
            ],
            '8. when input is valid and shiftrepo.find throw getdatafailed, then throw getdatafailed' =>
            [
                $inputWithOneValidPhoto, $shiftRepoFindThrowGetFailed, null, null, null, null, null, null,
                $expectedResultThrowGetDataFailed, true, false, false, false, false, false, false
            ],
            '9. when input is valid (with 2 photos) and all repo return correctly, then return true' =>
            [
                $inputWithOneValidPhoto, $shiftRepoFindReturnShiftData, $shiftRepoGetTimeReturnTimeData, $authRepoIsLoginReturnCorrectData, $shiftRepoGetRoomReturnRoom, $photoRepoSaveReturnPath, $historyRepoSaveReturnSavedData, $historyRepoInsertReturnTrue,
                $expectedResultSuccess, true, true, true, true, true, true, true
            ],
            '10. when input with 1 photo is invalid, then throw SuspiciousInputException' =>
            [
                $inputWithOneInvalidPhoto, $shiftRepoFindReturnShiftData, $shiftRepoGetTimeReturnTimeData, $authRepoIsLoginReturnCorrectData, null, null, null, null,
                $expectedResultThrowSuspiciousInputWrongScanTime, true, true, true, false, false, false, false
            ],
            '11. when input with 2 photos is invalid, then throw SuspiciousInputException' =>
            [
                $inputWithTwoInvalidPhoto, $shiftRepoFindReturnShiftData, $shiftRepoGetTimeReturnTimeData, $authRepoIsLoginReturnCorrectData, null, null, null, null,
                $expectedResultThrowSuspiciousInputWrongScanTime, true, true, true, false, false, false, false
            ],
            '12. when input with empty photo, then throw SuspiciousInputException' =>
            [
                $inputWithEmptyPhoto, $shiftRepoFindReturnShiftData, $shiftRepoGetTimeReturnTimeData, $authRepoIsLoginReturnCorrectData, null, null, null, null,
                $expectedResultThrowSuspiciousInputEmptyPhoto, true, true, true, false, false, false, false
            ],
            '13. when input with invalid date_time_now, then throw SuspiciousInputException' =>
            [
                $inputWithInvalidDateTimeNow, $shiftRepoFindReturnShiftData, $shiftRepoGetTimeReturnTimeData, $authRepoIsLoginReturnCorrectData, null, null, null, null,
                $expectedResultThrowSuspiciousInputServerTimeNotCorrect, true, true, true, false, false, false, false
            ],
            '14. when input is valid and shiftrepo.find return shift that doesnt belong to him, then throw SuspiciousInputException' =>
            [
                $inputWithOneValidPhoto, $shiftRepoFindReturnWrongShift, null, $authRepoIsLoginReturnCorrectData, null, null, null, null,
                $expectedResultThrowSuspiciousInputWrongShift, true, false, true, false, false, false, false
            ],

        ];
    }


}