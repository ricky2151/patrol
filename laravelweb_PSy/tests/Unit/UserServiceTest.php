<?php

namespace Test\Unit;

use Tests\TestCase;
use App\Repositories\Contracts\UserRepositoryContract;
use App\Repositories\Contracts\ShiftRepositoryContract;
use App\Repositories\Contracts\PhotoRepositoryContract;
use App\Repositories\Contracts\HistoryRepositoryContract;
use App\Repositories\Contracts\AuthRepositoryContract;

use App\Services\Implementations\UserServiceImplementation;

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
    public function testGetShiftsThatCanBeScanned($userRepoGetShiftsThatCanBeScanned, $expectedResult)
    {
        //1. create mock for userRepository
        $userRepoMock = TestUtil::mockClass(UserRepositoryContract::class, [['method' => 'getShiftsThatCanBeScanned', 'returnOrThrow' => $userRepoGetShiftsThatCanBeScanned]]);
        
        //2. make object UserService for testing
        $userService = new UserServiceImplementation($userRepoMock, resolve(ShiftRepositoryContract::class), resolve(PhotoRepositoryContract::class), resolve(HistoryRepositoryContract::class), resolve(AuthRepositoryContract::class));
        

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

        //output expected variable

        $expectedResultWithThreeData = $userRepoGetShiftsThatCanBeScannedReturnThreeData;

        $expectedResultEmpty = [];


        //order : 
        //userRepoGetShiftThatCanBeScanned
        //expectedResult
        return [
            '1. when userRepo.getShiftThatCanBeScanned return 3 data, then return that 3 data' =>
            [
                $userRepoGetShiftsThatCanBeScannedReturnThreeData,
                $expectedResultWithThreeData
            ],
            '2. when userRepo.getShiftThatCanBeScanned return no data, then return no data also' =>
            [
                $userRepoGetShiftsThatCanBeScannedReturnNoData,
                $expectedResultEmpty
            ],
        ];
    }

    /**
     * test view my history scan function
     * @dataProvider viewMyHistoryScanProvider
     * @return void
     */
    public function testViewMyHistoryScan($input, $userRepoViewMyHistoryScan, $expectedResult)
    {
        //1. create mock for userRepository
        $userRepoMock = TestUtil::mockClass(UserRepositoryContract::class, [['method' => 'viewMyHistoryScan', 'returnOrThrow' => $userRepoViewMyHistoryScan]]);
        
        //2. make object UserService for testing
        $userService = new UserServiceImplementation($userRepoMock, resolve(ShiftRepositoryContract::class), resolve(PhotoRepositoryContract::class), resolve(HistoryRepositoryContract::class), resolve(AuthRepositoryContract::class));

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

        //output expected variable

        $expectedResultWithThreeData = $userRepoViewMyHistoryScanReturnThreeData;

        $expectedResultEmpty = [];

        //order : 
        //input, userRepoViewMyHistoryScan
        //expectedResult
        return [
            '1. when userRepo.ViewMyHistoryScan return 3 data, then return that 3 data' =>
            [
                $inputAny, $userRepoViewMyHistoryScanReturnThreeData,
                $expectedResultWithThreeData
            ],
            '2. when userRepo.ViewMyHistoryScan return no data, then return no data also' =>
            [
                $inputAny, $userRepoViewMyHistoryScanReturnNoData,
                $expectedResultEmpty
            ],
        ];
    }


    /**
     * test submit scan function
     * @dataProvider submitScanProvider
     * @return void
     */
    public function testSubmitScan($input, $shiftRepoFind, $shiftRepoGetTime, $authRepoIsLogin, $shiftRepoGetRoom, $photoRepoSavePhotoToStorage, $historyRepoStore, $historyRepoInsertPhoto, $expectedResult)
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
        $userService = new UserServiceImplementation(resolve(UserRepositoryContract::class), $shiftRepoMock, $photoRepoMock, $historyRepoMock, $authRepoMock);
        
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

        $shiftRepoGetTimeReturnTimeData = [
            "id" => 1,
            "start" => "00:00",
            "end" => "06:00",
            "created_at" => "2021-02-01 10:44:33",
            "updated_at" => "2021-02-01 10:44:33"
        ];

        $authRepoIsLoginReturnCorrectData = ['access_token' => 'xxx', 'user' => [
            'id' => 5,
            'name' => 'ricky'
        ]];

        $shiftRepoGetRoomReturnRoom = [
            "id" => 6,
            "name" => "Ruangan Eli Hastuti",
            "floor_id" => "5",
            "building_id" => "4",
            "gateway_id" => "3",
            "created_at" => "2021-02-01 10:44:32",
            "updated_at" => "2021-02-01 10:44:32"
        ];

        $photoRepoSaveReturnPath = "photos/2020-11-18172954-Guard-LabAquariumA-H-0.jpg";

        $historyRepoSaveReturnSavedData = [
            'id' => 20,
            'shift_id' => $baseInput['id'],
            'status_node_id' => $baseInput['status_node_id'],
            'message' => $baseInput['message'],
            'scan_time' => $baseInput['date_time_now']
        ];

        $historyRepoInsertReturnTrue = true;

        //output expected variable

        $expectedResultSuccess = true;
        $expectedResultThrowSuspiciousInputWrongScanTime = new SuspiciousInputException("there is an incorrect photo_time data");
        $expectedResultThrowSuspiciousInputEmptyPhoto = new SuspiciousInputException("no photo uploaded");
        $expectedResultThrowSuspiciousInputServerTimeNotCorrect = new SuspiciousInputException("server time is not correct");
        $expectedResultThrowSuspiciousInputWrongShift = new SuspiciousInputException("this user is not have this shift");

        //order : 
        //input, shiftRepoFind, shiftRepoGetTime, authRepoIsLogin, shiftRepoGetRoom, photoRepoSave, historyRepoSave, historyRepoInsert
        //expectedResult
        return [
            '1. when input is valid (with 1 photo) and all repo return correctly, then return true' =>
            [
                $inputWithOneValidPhoto, $shiftRepoFindReturnShiftData, $shiftRepoGetTimeReturnTimeData, $authRepoIsLoginReturnCorrectData, $shiftRepoGetRoomReturnRoom, $photoRepoSaveReturnPath, $historyRepoSaveReturnSavedData, $historyRepoInsertReturnTrue,
                $expectedResultSuccess
            ],
            '2. when input is valid (with 2 photos) and all repo return correctly, then return true' =>
            [
                $inputWithOneValidPhoto, $shiftRepoFindReturnShiftData, $shiftRepoGetTimeReturnTimeData, $authRepoIsLoginReturnCorrectData, $shiftRepoGetRoomReturnRoom, $photoRepoSaveReturnPath, $historyRepoSaveReturnSavedData, $historyRepoInsertReturnTrue,
                $expectedResultSuccess
            ],
            '3. when input with 1 photo is invalid, then throw SuspiciousInputException' =>
            [
                $inputWithOneInvalidPhoto, $shiftRepoFindReturnShiftData, $shiftRepoGetTimeReturnTimeData, $authRepoIsLoginReturnCorrectData, null, null, null, null,
                $expectedResultThrowSuspiciousInputWrongScanTime
            ],
            '4. when input with 2 photos is invalid, then throw SuspiciousInputException' =>
            [
                $inputWithTwoInvalidPhoto, $shiftRepoFindReturnShiftData, $shiftRepoGetTimeReturnTimeData, $authRepoIsLoginReturnCorrectData, null, null, null, null,
                $expectedResultThrowSuspiciousInputWrongScanTime
            ],
            '5. when input with empty photo, then throw SuspiciousInputException' =>
            [
                $inputWithEmptyPhoto, $shiftRepoFindReturnShiftData, $shiftRepoGetTimeReturnTimeData, $authRepoIsLoginReturnCorrectData, null, null, null, null,
                $expectedResultThrowSuspiciousInputEmptyPhoto
            ],
            '6. when input with invalid date_time_now, then throw SuspiciousInputException' =>
            [
                $inputWithInvalidDateTimeNow, $shiftRepoFindReturnShiftData, $shiftRepoGetTimeReturnTimeData, $authRepoIsLoginReturnCorrectData, null, null, null, null,
                $expectedResultThrowSuspiciousInputServerTimeNotCorrect
            ],
            '7. when input is valid and shiftrepo.find return shift that doesnt belong to him, then throw SuspiciousInputException' =>
            [
                $inputWithOneValidPhoto, $shiftRepoFindReturnWrongShift, null, $authRepoIsLoginReturnCorrectData, null, null, null, null,
                $expectedResultThrowSuspiciousInputWrongShift
            ],

        ];
    }


    /**
     * test get function
     * @dataProvider getProvider
     * @return void
     */
    public function testGet($authRepoIsLogin, $userRepoAllOrder, $userRepoDatatableWhereOrderByGet, $expectedResult)
    {
        //1. create mock for authRepository
        $authRepoMock = TestUtil::mockClass(AuthRepositoryContract::class, [
            ['method' => 'isLogin', 'returnOrThrow' => $authRepoIsLogin],
        ]);

        //2. create mock for userRepository
        $userRepoMock = TestUtil::mockClass(UserRepositoryContract::class, [
            ['method' => 'allOrder', 'returnOrThrow' => $userRepoAllOrder],
            ['method' => 'datatable->where->orderBy->get', 'returnOrThrow' => $userRepoDatatableWhereOrderByGet],
        ]);
        
        //3. make object UserService for testing
        $userService = new UserServiceImplementation($userRepoMock, resolve(ShiftRepositoryContract::class), resolve(PhotoRepositoryContract::class), resolve(HistoryRepositoryContract::class), $authRepoMock);
        
        //4. call the function to be tested
        $thereIsException = false;
        try {
            $result = $userService->get();
        }
        catch (\Exception $e) //if there is an exception, then the exception should be same as expectedResult (because, expected result is a exeption)
        {
            dd($e);
            $this->assertEquals($e, $expectedResult);
            $thereIsException = true;
        }

        //5. assert result just only if there is no exception when calling method 
        if(!$thereIsException)
        {
            $this->assertSame($result, $expectedResult);
        }

    }

    public function getProvider() {
        //input variable
        $authRepoIsLoginReturnSuperAdminData = 
        [
            'access_token' => 'xxx', 
            'user' => [
                'id' => 5,
                'name' => 'ricky',
                'role_id' => 3,
            ]
        ];

        $authRepoIsLoginReturnAdminData = 
        [
            'access_token' => 'xxx', 
            'user' => [
                'id' => 5,
                'name' => 'ricky',
                'role_id' => 2,
            ]
        ];

        $userRepoAllOrderReturnThreeData = [
            [
                'id' => 1,
                'name' => 'Samuel Ricky',
                'age' => 1,
                'role_id' => 1,
                'username' => 'user2151',
                'phone' => '085727322755',
                'master_key' => 'xxx',
                'email' => 'samuel.ricky@ti.ukdw.ac.id',
                'role' => [
                    'id' => 1,
                    'name' => 'guard'
                ]
            ],
            [
                'id' => 2,
                'name' => 'Daniel Roy',
                'age' => 1,
                'role_id' => 1,
                'username' => 'roy123',
                'phone' => '085727321755',
                'master_key' => 'yyy',
                'email' => 'daniel.roy@ti.ukdw.ac.id',
                'role' => [
                    'id' => 1,
                    'name' => 'guard'
                ]
            ],
            [
                'id' => 3,
                'name' => 'Setiawan',
                'age' => 1,
                'role_id' => 1,
                'username' => 'setiawan2151',
                'phone' => '085727322735',
                'master_key' => 'zzz',
                'email' => 'setiawan@ti.ukdw.ac.id',
                'role' => [
                    'id' => 2,
                    'name' => 'admin'
                ]
            ],
            
        ];
        $userRepoAllOrderReturnEmptyData = [

        ];

        $userRepoDatatableWhereOrderByGetReturnThreeData = [
            [
                'id' => 1,
                'name' => 'Samuel Ricky',
                'age' => 1,
                'role_id' => 1,
                'username' => 'user2151',
                'phone' => '085727322755',
                'master_key' => 'xxx',
                'email' => 'samuel.ricky@ti.ukdw.ac.id',
                'role' => [
                    'id' => 1,
                    'name' => 'guard'
                ]
            ],
            [
                'id' => 2,
                'name' => 'Daniel Roy',
                'age' => 1,
                'role_id' => 1,
                'username' => 'roy123',
                'phone' => '085727321755',
                'master_key' => 'yyy',
                'email' => 'daniel.roy@ti.ukdw.ac.id',
                'role' => [
                    'id' => 1,
                    'name' => 'guard'
                ]
            ],
            [
                'id' => 3,
                'name' => 'Setiawan',
                'age' => 1,
                'role_id' => 1,
                'username' => 'setiawan2151',
                'phone' => '085727322735',
                'master_key' => 'zzz',
                'email' => 'setiawan@ti.ukdw.ac.id',
                'role' => [
                    'id' => 1,
                    'name' => 'guard'
                ]
            ],
            
        ];

        //output
        $expectedResultWithThreeDataGuardAndAdmin = [
            [
                'id' => 1,
                'name' => 'Samuel Ricky',
                'age' => 1,
                'role_id' => 1,
                'username' => 'user2151',
                'phone' => '085727322755',
                'master_key' => 'xxx',
                'email' => 'samuel.ricky@ti.ukdw.ac.id',
                'role_name' => 'guard'
            ],
            [
                'id' => 2,
                'name' => 'Daniel Roy',
                'age' => 1,
                'role_id' => 1,
                'username' => 'roy123',
                'phone' => '085727321755',
                'master_key' => 'yyy',
                'email' => 'daniel.roy@ti.ukdw.ac.id',
                'role_name' => 'guard'
            ],
            [
                'id' => 3,
                'name' => 'Setiawan',
                'age' => 1,
                'role_id' => 1,
                'username' => 'setiawan2151',
                'phone' => '085727322735',
                'master_key' => 'zzz',
                'email' => 'setiawan@ti.ukdw.ac.id',
                'role_name' => 'admin'
            ],
        ];
        $expectedResultEmpty = [];

        $expectedResultWithThreeDataGuard = [
            [
                'id' => 1,
                'name' => 'Samuel Ricky',
                'age' => 1,
                'role_id' => 1,
                'username' => 'user2151',
                'phone' => '085727322755',
                'master_key' => 'xxx',
                'email' => 'samuel.ricky@ti.ukdw.ac.id',
                'role_name' => 'guard'
            ],
            [
                'id' => 2,
                'name' => 'Daniel Roy',
                'age' => 1,
                'role_id' => 1,
                'username' => 'roy123',
                'phone' => '085727321755',
                'master_key' => 'yyy',
                'email' => 'daniel.roy@ti.ukdw.ac.id',
                'role_name' => 'guard'
            ],
            [
                'id' => 3,
                'name' => 'Setiawan',
                'age' => 1,
                'role_id' => 1,
                'username' => 'setiawan2151',
                'phone' => '085727322735',
                'master_key' => 'zzz',
                'email' => 'setiawan@ti.ukdw.ac.id',
                'role_name' => 'guard'
            ],
        ];

        //order : 
        //authrepo.islogin, userrepoallorder, userrepodatatablewhereorderbyget
        //expected result
        return [
            '1. when authrepo.islogin return superadmin data and userrepo.allorder return three data, then return that three data' => [
                $authRepoIsLoginReturnSuperAdminData, $userRepoAllOrderReturnThreeData, null,
                $expectedResultWithThreeDataGuardAndAdmin
            ],
            '2. when authrepo.islogin return superadmin data and userrepo.allorder return empty data, then return that empty data' => [
                $authRepoIsLoginReturnSuperAdminData, $userRepoAllOrderReturnEmptyData, null,
                $expectedResultEmpty
            ],
            '3. when authrepo.islogin return admin data and userrepo.datatablewhereorderbyget return three data, then return that three data' => [
                $authRepoIsLoginReturnAdminData, $userRepoDatatableWhereOrderByGetReturnThreeData, null,
                $expectedResultWithThreeDataGuard
            ],
            
        ];
    }


    /**
     * test getShifts function
     * @dataProvider getShiftsProvider
     * @return void
     */
    public function testGetShifts($userRepoGetShifts, $expectedResult)
    {
        //1. create mock for userRepository
        $userRepoMock = TestUtil::mockClass(UserRepositoryContract::class, [
            ['method' => 'getShifts', 'returnOrThrow' => $userRepoGetShifts],
        ]);
        
        //2. make object UserService for testing
        $userService = new UserServiceImplementation($userRepoMock, resolve(ShiftRepositoryContract::class), resolve(PhotoRepositoryContract::class), resolve(HistoryRepositoryContract::class), resolve(AuthRepositoryContract::class));
        
        //3. call the function to be tested
        $thereIsException = false;
        try {
            $result = $userService->getShifts(1);
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
        

    }

    public function getShiftsProvider() {
        //input variable
        $userRepoGetShiftsReturnThreeData = [
            [
                "id" => 1,
                "user_id" => 1,
                "date" => "2021-02-08",
                "room" => [
                    'id' => 1,
                    'name' => 'Ruangan Olivia Lailasari'
                ],
                'time' => [
                    'id' => 1,
                    'start' => '00:00',
                    'end' => '10:00'
                ]
            ],
            [
                "id" => 1,
                "user_id" => 1,
                "date" => "2021-02-08",
                "room" => [
                    'id' => 1,
                    'name' => 'Ruangan Olivia Lailasari'
                ],
                'time' => [
                    'id' => 2,
                    'start' => '10:00',
                    'end' => '12:00'
                ]
            ],
            [
                "id" => 1,
                "user_id" => 1,
                "date" => "2021-02-08",
                "room" => [
                    'id' => 1,
                    'name' => 'Ruangan Olivia Lailasari'
                ],
                'time' => [
                    'id' => 3,
                    'start' => '12:00',
                    'end' => '15:00'
                ]
            ],
            
        ];
        $userRepoGetShiftsReturnEmptyData = [

        ];

        //output
        $expectedResultWithThreeData = [
            [
                "id" => 1,
                "user_id" => 1,
                "date" => "2021-02-08",
                "room_name" => 'Ruangan Olivia Lailasari',
                'time_start_end' => '00:00 - 10:00'
            ],
            [
                "id" => 1,
                "user_id" => 1,
                "date" => "2021-02-08",
                "room_name" => 'Ruangan Olivia Lailasari',
                'time_start_end' => '10:00 - 12:00'
            ],
            [
                "id" => 1,
                "user_id" => 1,
                "date" => "2021-02-08",
                "room_name" => 'Ruangan Olivia Lailasari',
                'time_start_end' => '12:00 - 15:00'
            ],
        ];
        $expectedResultEmpty = [];

        //order : 
        //userrepogetshifts
        //expected result
        return [
            '1. when userrepo.getshifts return three data, then return that three data' => [
                $userRepoGetShiftsReturnThreeData, 
                $expectedResultWithThreeData
            ],
            '2. when userrepo.getshifts return empty data, then return that empty data' => [
                $userRepoGetShiftsReturnEmptyData, 
                $expectedResultEmpty
            ],
        ];
    }


    /**
     * test store user with shifts function
     * @dataProvider storeUserWithShiftsProvider
     * @return void
     */
    public function testStoreUserWithShifts($input, $userRepoStore, $userRepoInsertShifts, 
        $expectedResult)
    {
        //1. create mock for userRepository
        $userRepoMock = TestUtil::mockClass(UserRepositoryContract::class, [
            ['method' => 'store', 'returnOrThrow' => $userRepoStore],
            ['method' => 'insertShifts', 'returnOrThrow' => $userRepoInsertShifts],
        ]);
        
        //2. make object UserService for testing
        $userService = new UserServiceImplementation($userRepoMock, resolve(ShiftRepositoryContract::class), resolve(PhotoRepositoryContract::class), resolve(HistoryRepositoryContract::class), resolve(AuthRepositoryContract::class));
        
        //3. call the function to be tested
        $thereIsException = false;
        try {
            $result = $userService->storeUserWithShifts($input);
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
        

    }

    public function storeUserWithShiftsProvider()
    {
        $baseInput = [
            'name' => 'ricky',
            'age' => 22,
            'role_id' => 1,
            'username' => 'ricky2151',
            'password' => '123456',
            'phone' => '085727322111',
            'email' => 'samuel.ricky@ti.ukdw.ac.id',
            'shifts' => [
                [
                    'room_id' => 1,
                    'time_id' => 1,
                    'date' => '06/07/2019'
                ],
            ]
        ];

        
        //input variable
        $inputWithOneValidShift = $baseInput;
        
        $inputWithThreeValidShift = $baseInput;
        $inputWithThreeValidShift['shifts'][1] = [
            'room_id' => 1,
            'time_id' => 2,
            'date' => '06/07/2019'
        ];
        $inputWithThreeValidShift['shifts'][2] = [
            'room_id' => 1,
            'time_id' => 3,
            'date' => '06/07/2019'
        ];

        $inputWithEmptyShift = $baseInput;
        $inputWithEmptyShift['shifts'] = [];

        $userRepoStoreReturnNewData = [
            'id' => 30,
            'name' => 'ricky',
            'age' => 22,
            'role_id' => 1,
            'username' => 'ricky2151',
            'phone' => '085727322111',
            'email' => 'samuel.ricky@ti.ukdw.ac.id',
        ];

        $userRepoInsertShiftsReturnTrue = true;

        //output expected variable

        $expectedResultSuccess = true;

        //order : 
        //input, userrepostore, userrepoinsertshifts
        //expectedResult
        return [
            '1. when input is valid (with 1 shifts) and all repo return correctly, then return true' =>
            [
                $inputWithOneValidShift, $userRepoStoreReturnNewData, $userRepoInsertShiftsReturnTrue,
                $expectedResultSuccess
            ],
            '2. when input is valid (with 3 shifts) and all repo return correctly, then return true' =>
            [
                $inputWithThreeValidShift, $userRepoStoreReturnNewData, $userRepoInsertShiftsReturnTrue,
                $expectedResultSuccess
            ],
            '3. when input with empty shift, then return true' =>
            [
                $inputWithEmptyShift, $userRepoStoreReturnNewData, null,
                $expectedResultSuccess
            ],
        ];
    }

    /**
     * test update user with shifts function
     * @dataProvider updateUserWithShiftsProvider
     * @return void
     */
    public function testUpdateUserWithShifts($input, $userRepoCheckHaveShifts, $userRepoUpdate, $userRepoInsertShifts, $userRepoUpdateShifts, $userRepoDeleteShifts, $expectedResult)
    {
        //1. create mock for userRepository
        $userRepoMock = TestUtil::mockClass(UserRepositoryContract::class, [
            ['method' => 'checkHaveShifts', 'returnOrThrow' => $userRepoCheckHaveShifts],
            ['method' => 'update', 'returnOrThrow' => $userRepoUpdate],
            ['method' => 'insertShifts', 'returnOrThrow' => $userRepoInsertShifts],
            ['method' => 'updateShifts', 'returnOrThrow' => $userRepoUpdateShifts],
            ['method' => 'deleteShifts', 'returnOrThrow' => $userRepoDeleteShifts],
        ]);
        
        //2. make object UserService for testing
        $userService = new UserServiceImplementation($userRepoMock, resolve(ShiftRepositoryContract::class), resolve(PhotoRepositoryContract::class), resolve(HistoryRepositoryContract::class), resolve(AuthRepositoryContract::class));
        
        //3. call the function to be tested
        $thereIsException = false;
        try {
            $result = $userService->updateUserWithShifts($input, 1);
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
        

    }

    public function updateUserWithShiftsProvider()
    {
        $baseInput = [
            'name' => 'ricky baru',
            'age' => 23,
            'role_id' => 1,
            'username' => 'ricky2151',
            'password' => '123456',
            'phone' => '085727322111',
            'email' => 'samuel.ricky@ti.ukdw.ac.id',
        ];

        
        //input variable
        $inputWithInsertOneValidShift = $baseInput;
        $inputWithInsertOneValidShift['shifts'] = [
            [
                'room_id' => 1,
                'time_id' => 1,
                'date' => '06/07/2019',
                'type' => 1,
            ],
        ];

        $inputWithInsertThreeValidShift = $baseInput;
        $inputWithInsertThreeValidShift['shifts'] = [
            [
                'room_id' => 1,
                'time_id' => 1,
                'date' => '06/07/2019',
                'type' => 1,
            ],
            [
                'room_id' => 1,
                'time_id' => 2,
                'date' => '06/07/2019',
                'type' => 1,
            ],
            [
                'room_id' => 1,
                'time_id' => 3,
                'date' => '06/07/2019',
                'type' => 1,
            ],
        ];

        $inputWithUpdateOneValidShift = $baseInput;
        $inputWithUpdateOneValidShift['shifts'] = [
            [
                'room_id' => 1,
                'time_id' => 1,
                'date' => '06/07/2019',
                'type' => 0,
                'id' => 1,
            ],
        ];

        $inputWithUpdateThreeValidShift = $baseInput;
        $inputWithUpdateThreeValidShift['shifts'] = [
            [
                'room_id' => 1,
                'time_id' => 1,
                'date' => '06/07/2019',
                'type' => 0,
                'id' => 1
            ],
            [
                'room_id' => 1,
                'time_id' => 2,
                'date' => '06/07/2019',
                'type' => 0,
                'id' => 2
            ],
            [
                'room_id' => 1,
                'time_id' => 3,
                'date' => '06/07/2019',
                'type' => 0,
                'id' => 3
            ],
        ];

        $inputWithDeleteOneValidShift = $baseInput;
        $inputWithDeleteOneValidShift['shifts'] = [
            [
                'type' => -1,
                'id' => 1
            ],
        ];

        $inputWithDeleteThreeValidShift = $baseInput;
        $inputWithDeleteThreeValidShift['shifts'] = [
            [
                'type' => -1,
                'id' => 1
            ],
            [
                'type' => -1,
                'id' => 2
            ],
            [
                'type' => -1,
                'id' => 3
            ],
        ];

        $inputWithNoChangedShifts = $baseInput;
        $inputWithNoChangedShifts['shifts'] = [];
        

        $userRepoCheckHaveShiftsReturnTrue = true;
        $userRepoCheckHaveShiftsReturnFalse = false;

        $userRepoUpdateReturnUpdatedData = $baseInput;

        $userRepoInsertShiftsReturnTrue = true;
        $userRepoUpdateShiftsReturnTrue = true;
        $userRepoDeleteShiftsReturnTrue = true;

        //output expected variable

        $expectedResultSuccess = true;
        $expectedResultThrowSuspiciousWrongShift = new SuspiciousInputException("this user is not have this shift");
        

        //order : 
        //input, userrepocheckHaveShifts, userrepoupdate, userrepoinsertshifts, userrepoupdateshifts, userrepodeleteshifts
        //expectedResult
        return [
            '1. when input is valid (insert 1 shifts) and all repo return correctly, then return true' =>
            [
                $inputWithInsertOneValidShift, null, $userRepoUpdateReturnUpdatedData, $userRepoInsertShiftsReturnTrue, null,null,
                $expectedResultSuccess 
            ],
            '2. when input is valid (insert 3 shifts) and all repo return correctly, then return true' =>
            [
                $inputWithInsertThreeValidShift, null, $userRepoUpdateReturnUpdatedData, $userRepoInsertShiftsReturnTrue, null,null,
                $expectedResultSuccess 
            ],
            '3. when input is valid (update 1 shifts) and all repo return correctly, then return true' =>
            [
                $inputWithUpdateOneValidShift, $userRepoCheckHaveShiftsReturnTrue, $userRepoUpdateReturnUpdatedData, null, $userRepoUpdateShiftsReturnTrue,null,
                $expectedResultSuccess 
            ],
            '4. when input is valid (update 3 shifts) and all repo return correctly, then return true' =>
            [
                $inputWithUpdateThreeValidShift, $userRepoCheckHaveShiftsReturnTrue, $userRepoUpdateReturnUpdatedData, null, $userRepoUpdateShiftsReturnTrue,null,
                $expectedResultSuccess 
            ],
            '5. when input is valid (delete 1 shifts) and all repo return correctly, then return true' =>
            [
                $inputWithDeleteOneValidShift, $userRepoCheckHaveShiftsReturnTrue, $userRepoUpdateReturnUpdatedData, null, null ,$userRepoDeleteShiftsReturnTrue,
                $expectedResultSuccess 
            ],
            '6. when input is valid (delete 3 shifts) and all repo return correctly, then return true' =>
            [
                $inputWithDeleteThreeValidShift, $userRepoCheckHaveShiftsReturnTrue, $userRepoUpdateReturnUpdatedData, null, null ,$userRepoDeleteShiftsReturnTrue,
                $expectedResultSuccess 
            ],
            '7. when input is valid (update 3 shifts) and userrepo.checkHaveShifts return false, then throw suspiciousexception' =>
            [
                $inputWithUpdateThreeValidShift, $userRepoCheckHaveShiftsReturnFalse, null, null, null ,null,
                $expectedResultThrowSuspiciousWrongShift 
            ],
            '8. when input is valid (delete 3 shifts) and userrepo.checkHaveShifts return false, then throw suspiciousexception' =>
            [
                $inputWithDeleteThreeValidShift, $userRepoCheckHaveShiftsReturnFalse, null, null, null ,null,
                $expectedResultThrowSuspiciousWrongShift 
            ],
            '9. when input is valid (with no shift changes) and all repo return correctly, then return true' =>
            [
                $inputWithNoChangedShifts, null, $userRepoUpdateReturnUpdatedData, null, null ,null,
                $expectedResultSuccess 
            ],
        ];
    }


    /**
     * test findWithShifts function
     * @dataProvider findWithShiftsProvider
     * @return void
     */
    public function testFindWithShifts($userRepoFindWithShifts, $expectedResult)
    {
        //1. create mock for userRepository
        $userRepoMock = TestUtil::mockClass(UserRepositoryContract::class, [
            ['method' => 'findWithShifts', 'returnOrThrow' => $userRepoFindWithShifts],
        ]);
        
        //2. make object UserService for testing
        $userService = new UserServiceImplementation($userRepoMock, resolve(ShiftRepositoryContract::class), resolve(PhotoRepositoryContract::class), resolve(HistoryRepositoryContract::class), resolve(AuthRepositoryContract::class));
        
        //3. call the function to be tested
        $thereIsException = false;
        try {
            $result = $userService->findWithShifts(1);
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
        

    }

    public function findWithShiftsProvider() {
        //input variable
        $userRepoFindWithShiftsReturnCorrectData = 
        [
            "id" => 1,
            "name" => "Galang Ramadan S.E.",
            "age" => "45",
            "role_id" => "1",
            "username" => "test_guard",
            "phone" => "0973 9678 7997",
            "master_key" => "Piw2nq0ZuWCPdWRw",
            "email" => "knajmudin@example.com",
            "role" => 
            [
                "id" => 1,
                "name" => "Guard",
            ],
            "shifts" => 
            [
                [
                    "id" => 1,
                    "user_id" => "1",
                    "room_id" => "4",
                    "time_id" => "3",
                    "date" => "2021-02-10",
                    "room" => 
                    [
                        "id" => 4,
                        "name" => "Ruangan Cakrawala Prabowo",
                        "floor_id" => "1",
                        "building_id" => "5",
                        "gateway_id" => "1",
                    ],
                    "time" => 
                    [
                        "id" => 3,
                        "start" => "10:00",
                        "end" => "14:00",
                    ],
                ],
                [
                    "id" => 6,
                    "user_id" => "1",
                    "room_id" => "6",
                    "time_id" => "1",
                    "date" => "2021-02-10",
                    "room" => 
                    [
                        "id" => 6,
                        "name" => "Ruangan Gina Vera Wijayanti S.Farm",
                        "floor_id" => "3",
                        "building_id" => "5",
                        "gateway_id" => "1",
                    ],
                    "time" => [
                        "id" => 1,
                        "start" => "00:00",
                        "end" => "06:00",
                    ],
                ],
                [
                    "id" => 10,
                    "user_id" => "1",
                    "room_id" => "5",
                    "time_id" => "4",
                    "date" => "2021-02-10",
                    "room" => 
                    [
                        "id" => 5,
                        "name" => "Ruangan Kayla Fujiati",
                        "floor_id" => "1",
                        "building_id" => "2",
                        "gateway_id" => "1",
                    ],
                    "time" => 
                    [
                        "id" => 4,
                        "start" => "14:00",
                        "end" => "18:00",
                    ],
                ],
            ],
        ];
        
        //output
        $expectedResult = [
            "user" => [
                "id" => 1,
                "name" => "Galang Ramadan S.E.",
                "age" => "45",
                "username" => "test_guard",
                "phone" => "0973 9678 7997",
                "master_key" => "Piw2nq0ZuWCPdWRw",
                "email" => "knajmudin@example.com",
                "role" => [
                    "id" => 1,
                    "name" => "Guard"
                ],
                
            ],
            "shifts" => 
            [
                [
                    "id" => 1,
                    "date" => "2021-02-10",
                    "room" => [
                        "id" => 4,
                        "name" => "Ruangan Cakrawala Prabowo",
                        "floor_id" => "1",
                        "building_id" => "5",
                        "gateway_id" => "1",
                    ],
                    "time" => [
                        "id" => 3,
                        "name" => "10:00-14:00"
                    ],
                ],
                [
                    "id" => 6,
                    "date" => "2021-02-10",
                    "room" => [
                        "id" => 6,
                        "name" => "Ruangan Gina Vera Wijayanti S.Farm",
                        "floor_id" => "3",
                        "building_id" => "5",
                        "gateway_id" => "1",
                    ],
                    "time" => [
                        "id" => 1,
                        "name" => "00:00-06:00"
                    ],
                ],
                [
                    "id" => 10,
                    "date" => "2021-02-10",
                    "room" => [
                        "id" => 5,
                        "name" => "Ruangan Kayla Fujiati",
                        "floor_id" => "1",
                        "building_id" => "2",
                        "gateway_id" => "1",
                    ],
                    "time" => [
                        "id" => 4,
                        "name" => "14:00-18:00"
                    ],
                ]
            ]
        ];
        

        //order : 
        //userrepofindWithShifts
        //expected result
        return [
            '1. when userrepo.findWithShifts return correct data, then return that correct data' => [
                $userRepoFindWithShiftsReturnCorrectData, 
                $expectedResult
            ],
        ];
    }


    /**
     * test delete function
     * @dataProvider deleteProvider
     * @return void
     */
    public function testDelete($id, $userRepoDelete, $expectedResult)
    {
        //1. create mock for userRepository
        $userRepoMock = TestUtil::mockClass(UserRepositoryContract::class, [['method' => 'delete', 'returnOrThrow' => $userRepoDelete]]);
        
        //2. make object UserService for testing
        $userService = new UserServiceImplementation($userRepoMock, resolve(ShiftRepositoryContract::class), resolve(PhotoRepositoryContract::class), resolve(HistoryRepositoryContract::class), resolve(AuthRepositoryContract::class));
        
        //3. call the function to be tested
        $thereIsException = false;
        try {
            $result = $userService->delete($id);
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
        

    }

    public function deleteProvider() {
        //input variable
        $validId = 1;

        $userRepoDeleteReturnTrue = true;

        //output
        $expectedResultTrue = $userRepoDeleteReturnTrue;

        //order : 
        //id, userrepodelete
        //expected result
        return [
            '1. when id is valid and userrepo.delete return true, then return true' => [
                $validId, $userRepoDeleteReturnTrue,
                $expectedResultTrue
            ],
        ];
    }

    
    

    


}