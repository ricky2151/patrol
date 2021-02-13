<?php

namespace Test\Unit;

use Tests\Testcase;
use App\Repositories\Contracts\HistoryRepositoryContract;
use App\Repositories\Contracts\StatusNodeRepositoryContract;
use App\Repositories\Contracts\ShiftRepositoryContract;

use App\Services\Implementations\ShiftServiceImplementation;

use App\Exceptions\GetDataFailedException;

use App\TestUtil;

class ShiftServiceTest extends TestCase {

    /**
     * test getDashboardData function
     * @dataProvider getDashboardDataProvider
     * @return void
     */
    public function testGetDashboardData($statusNodeRepoGetAll, $historyRepoGetGraphData, $historyRepoGetCurrentEvent, $expectedResult)
    {
        //1. create mock for historyRepository
        $historyRepoMock = TestUtil::mockClass(HistoryRepositoryContract::class, [
            ['method' => 'getGraphData', 'returnOrThrow' => $historyRepoGetGraphData],
            ['method' => 'getCurrentEvent', 'returnOrThrow' => $historyRepoGetCurrentEvent],
        ]);

        //1. create mock for statusNodeRepository
        $statusNodeRepoMock = TestUtil::mockClass(StatusNodeRepositoryContract::class, [
            ['method' => 'getAll', 'returnOrThrow' => $statusNodeRepoGetAll],
        ]);
        
        //2. make object ShiftService for testing
        $shiftService = new ShiftServiceImplementation(resolve(ShiftRepositoryContract::class), $statusNodeRepoMock, $historyRepoMock);
        
        //3. call the function to be tested
        $thereIsException = false;
        try {
            $result = $shiftService->getDashboardData();
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

    public function getDashboardDataProvider() {
        //input variable
        $historyRepoGetCurrentEventReturnThreeData = [
            [
                "id" => 1,
                "shift_id" => "6",
                "status_node_id" => "2",
                "message" => "Ada malling !!!",
                "scan_time" => "14:00:05",
                "created_at" => "2021-02-04 18:37:22",
                "updated_at" => "2021-02-04 18:37:22",
                "shift" => [
                    "id" => 6,
                    "user_id" => "5",
                    "room_id" => "8",
                    "time_id" => "4",
                    "date" => "2021-02-04",
                    "created_at" => "2021-02-04 18:37:20",
                    "updated_at" => "2021-02-04 18:37:20",
                    "room" => [
                        "id" => 8,
                        "name" => "Ruangan Oman Jinawi Ramadan M.Ak"
                    ]
                ]
            ],
            [
                "id" => 2,
                "shift_id" => "6",
                "status_node_id" => "1",
                "message" => "Ada malling !!!",
                "scan_time" => "14:00:05",
                "created_at" => "2021-02-04 18:37:22",
                "updated_at" => "2021-02-04 18:37:22",
                "shift" => [
                    "id" => 6,
                    "user_id" => "5",
                    "room_id" => "8",
                    "time_id" => "4",
                    "date" => "2021-02-04",
                    "created_at" => "2021-02-04 18:37:20",
                    "updated_at" => "2021-02-04 18:37:20",
                    "room" => [
                        "id" => 8,
                        "name" => "Ruangan Oman Jinawi Ramadan M.Ak"
                    ]
                ]
            ],
            [
                "id" => 3,
                "shift_id" => "5",
                "status_node_id" => "2",
                "message" => "Aman pak !",
                "scan_time" => "14:00:05",
                "created_at" => "2021-02-04 18:37:22",
                "updated_at" => "2021-02-04 18:37:22",
                "shift" => [
                    "id" => 5,
                    "user_id" => "4",
                    "room_id" => "6",
                    "time_id" => "3",
                    "date" => "2021-02-04",
                    "created_at" => "2021-02-04 18:37:20",
                    "updated_at" => "2021-02-04 18:37:20",
                    "room" => [
                        "id" => 6,
                        "name" => "Ruangan Restu Winarsih"
                    ]
                ]
            ]
        ];

        $historyRepoGetCurrentEventReturnEmptyData = [];

        

        $historyRepoGetGraphDataReturnThreeData = [
            [
                "year" => "2021",
                "month" => "02",
                "status_nodes" => "Aman",
                "status_nodes_id" => "1",
                "count" => "14"
            ],
            [
                "year" => "2021",
                "month" => "02",
                "status_nodes" => "Mencurigakan",
                "status_nodes_id" => "2",
                "count" => "16"
            ],
            [
                "year" => "2021",
                "month" => "02",
                "status_nodes" => "Tidak Aman",
                "status_nodes_id" => "3",
                "count" => "20"
            ]
        ];

        $historyRepoGetGraphDataReturnEmptyData = [];

        

        $statusNodeRepoGetAllReturnThreeData = [
            [
                "id" => 1,
                "name" => "Aman"
            ],
            [
                "id" => 2,
                "name" => "Mencurigakan"
            ],
            [
                "id" => 3,
                "name" => "Tidak Aman"
            ]
        ];

        $statusNodeRepoGetAllReturnEmptyData = [];
        

        //output
        $expectedResultWithCorrectData = [
            'currentEvent' => $historyRepoGetCurrentEventReturnThreeData,
            'graphData' => $historyRepoGetGraphDataReturnThreeData,
            'statusNodeData' => $statusNodeRepoGetAllReturnThreeData
        ];

        $expectedResultWithEmptyCurrentEvent = $expectedResultWithCorrectData;
        $expectedResultWithEmptyCurrentEvent['currentEvent'] = [];

        $expectedResultWithEmptyGraphData = $expectedResultWithCorrectData;
        $expectedResultWithEmptyGraphData['graphData'] = [];

        $expectedResultThrowGetDataFailed = new GetDataFailedException();

        //order : 
        //statusnoderepo.getall, historyrepo.getgraphdata, historyrepo.getcurrentevent, 
        //expected result
        return [
            '1. when all repo return three data, then return correct dashboard data' => [
                $statusNodeRepoGetAllReturnThreeData, $historyRepoGetGraphDataReturnThreeData, $historyRepoGetCurrentEventReturnThreeData,
                $expectedResultWithCorrectData
            ],
            '2. when historyrepo.getcurrentevent return empty data, then return correct dashboard data without event data' => [
                $statusNodeRepoGetAllReturnThreeData, $historyRepoGetGraphDataReturnThreeData, $historyRepoGetCurrentEventReturnEmptyData,
                $expectedResultWithEmptyCurrentEvent
            ],
            '3. when historyrepo.getgraphdata return empty data, then return correct dashboard data without graph data' => [
                $statusNodeRepoGetAllReturnThreeData, $historyRepoGetGraphDataReturnEmptyData, $historyRepoGetCurrentEventReturnThreeData,
                $expectedResultWithEmptyGraphData
            ],
            '4. when statusnode.getall return empty data, then throw getdatafailedexception' => [
                $statusNodeRepoGetAllReturnEmptyData, null, null,
                $expectedResultThrowGetDataFailed
            ],
        ];
    }

    /**
     * test GetHistoryScan function
     * @dataProvider GetHistoryScanProvider
     * @return void
     */
    public function testGetHistoryScan($shiftRepoGetHistoryScan, $expectedResult)
    {
        //1. create mock for shiftRepository
        $shiftRepoMock = TestUtil::mockClass(ShiftRepositoryContract::class, [
            ['method' => 'getHistoryScan', 'returnOrThrow' => $shiftRepoGetHistoryScan],
        ]);
        
        //2. make object ShiftService for testing
        $shiftService = new ShiftServiceImplementation($shiftRepoMock, resolve(StatusNodeRepositoryContract::class), resolve(HistoryRepositoryContract::class));
        
        //3. call the function to be tested
        $thereIsException = false;
        try {
            $result = $shiftService->getHistoryScan(1);
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

    public function getHistoryScanProvider() {
        //input variable
        $shiftRepoGetHistoryScanReturnThreeData = [
            "id" => 1,
            "user_id" => 1,
            'room_id' => 1,
            'time_id' => 1,
            "date" => "2021-02-08",
            "room" => [
                'id' => 1,
                'name' => 'Ruangan Olivia Lailasari'
            ],
            'time' => [
                'id' => 1,
                'start' => '00:00',
                'end' => '10:00'
            ],
            'histories' => [
                [
                    'id' => 1,
                    'shift_id' => 1,
                    'status_node_id' => 1,
                    'message' => 'ada maling !!!',
                    'scan_time' => '10:00:05',
                    'photos' => [
                        [
                            'id' => 1,
                            'url' => 'www.google.com',
                            'history_id' => 1,
                            'photo_time' => '10:00:05'
                        ],
                        [
                            'id' => 2,
                            'url' => 'www.google.com',
                            'history_id' => 1,
                            'photo_time' => '10:00:05'
                        ],
                        [
                            'id' => 3,
                            'url' => 'www.google.com',
                            'history_id' => 1,
                            'photo_time' => '10:00:05'
                        ]
                    ],
                    'status_node' => [
                        'id' => 1,
                        'name' => 'aman'
                    ]
                ],
                [
                    'id' => 2,
                    'shift_id' => 1,
                    'status_node_id' => 1,
                    'message' => 'ada maling !!!',
                    'scan_time' => '10:00:05',
                    'photos' => [],
                    'status_node' => [
                        'id' => 1,
                        'name' => 'aman'
                    ]
                ],
                [
                    'id' => 3,
                    'shift_id' => 1,
                    'status_node_id' => 1,
                    'message' => 'ada maling !!!',
                    'scan_time' => '10:00:05',
                    'photos' => [],
                    'status_node' => [
                        'id' => 1,
                        'name' => 'aman'
                    ]
                ],
            ]
        ];
        $userRepoGetHistoryScanReturnEmptyData = [
            "id" => 1,
            "user_id" => 1,
            'room_id' => 1,
            'time_id' => 1,
            "date" => "2021-02-08",
            "room" => [
                'id' => 1,
                'name' => 'Ruangan Olivia Lailasari'
            ],
            'time' => [
                'id' => 1,
                'start' => '00:00',
                'end' => '10:00'
            ],
            'histories' => []
        ];

        //output
        $expectedResultWithThreeData = [
            "id" => 1,
            "user_id" => 1,
            'room_id' => 1,
            'time_id' => 1,
            "date" => "2021-02-08",
            'histories' => [
                [
                    'id' => 1,
                    'shift_id' => 1,
                    'status_node_id' => 1,
                    'message' => 'ada maling !!!',
                    'scan_time' => '10:00:05',
                    'photos' => [
                        [
                            'id' => 1,
                            'url' => 'www.google.com',
                            'photo_time' => '10:00:05'
                        ],
                        [
                            'id' => 2,
                            'url' => 'www.google.com',
                            'photo_time' => '10:00:05'
                        ],
                        [
                            'id' => 3,
                            'url' => 'www.google.com',
                            'photo_time' => '10:00:05'
                        ]
                    ],
                    'status_node_name' => 'aman',
                ],
                [
                    'id' => 2,
                    'shift_id' => 1,
                    'status_node_id' => 1,
                    'message' => 'ada maling !!!',
                    'scan_time' => '10:00:05',
                    'photos' => [],
                    'status_node_name' => 'aman',
                ],
                [
                    'id' => 3,
                    'shift_id' => 1,
                    'status_node_id' => 1,
                    'message' => 'ada maling !!!',
                    'scan_time' => '10:00:05',
                    'photos' => [],
                    'status_node_name' => 'aman',
                ],
            ],
            'room_name' => 'Ruangan Olivia Lailasari',
            'time_name' => '00:00 - 10:00',
        ];
        $expectedResultEmpty = [
            "id" => 1,
            "user_id" => 1,
            'room_id' => 1,
            'time_id' => 1,
            "date" => "2021-02-08",
            'histories' => [],
            'room_name' => 'Ruangan Olivia Lailasari',
            'time_name' => '00:00 - 10:00',
        ];

        //order : 
        //shiftrepogethistoryscan
        //expected result
        return [
            '1. when shiftrepo.gethistoryscan return three data, then return that three data' => [
                $shiftRepoGetHistoryScanReturnThreeData, 
                $expectedResultWithThreeData
            ],
            '2. when userrepo.gethistoryscan return empty data, then return that empty data' => [
                $userRepoGetHistoryScanReturnEmptyData, 
                $expectedResultEmpty
            ],
        ];
    }


    /**
     * test GetShiftsNotAssign function
     * @dataProvider getShiftsNotAssignProvider
     * @return void
     */
    public function testGetShiftsNotAssign($shiftRepoGetShiftsNotAssign, $expectedResult)
    {
        //1. create mock for shiftRepository
        $shiftRepoMock = TestUtil::mockClass(ShiftRepositoryContract::class, [
            ['method' => 'getShiftsNotAssign', 'returnOrThrow' => $shiftRepoGetShiftsNotAssign],
        ]);
        
        //2. make object ShiftService for testing
        $shiftService = new ShiftServiceImplementation($shiftRepoMock, resolve(StatusNodeRepositoryContract::class), resolve(HistoryRepositoryContract::class));
        
        //3. call the function to be tested
        $thereIsException = false;
        try {
            $result = $shiftService->getShiftsNotAssign();
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

    public function getShiftsNotAssignProvider() {
        //input variable
        $shiftRepoGetShiftsNotAssignReturnThreeData = [
            [
                'date' => "2021-02-10",
                'room_name' => "Ruangan Cakrawala Prabowo",
                'room_id' => 4,
                'time_id' => 4,
                'time_start_end' => '14:00-18:00'
            ],
            [
                'date' => "2021-02-10",
                'room_name' => "Ruangan Cakrawala Prabowo",
                'room_id' => 2,
                'time_id' => 4,
                'time_start_end' => '14:00-18:00'
            ],
            [
                'date' => "2021-02-10",
                'room_name' => "Ruangan Cakrawala Prabowo",
                'room_id' => 5,
                'time_id' => 4,
                'time_start_end' => '14:00-18:00'
            ],
        ];
        $shiftRepoGetShiftsNotAssignReturnEmptyData = [];

        //output
        $expectedResultWithThreeData = $shiftRepoGetShiftsNotAssignReturnThreeData;
        $expectedResultEmpty = [];

        //order : 
        //shiftrepogetshiftsnotassign
        //expected result
        return [
            '1. when shiftrepo.getshiftsnotassign return three data, then return that three data' => [
                $shiftRepoGetShiftsNotAssignReturnThreeData, 
                $expectedResultWithThreeData
            ],
            '2. when userrepo.getshiftsnotassign return empty data, then return that empty data' => [
                $shiftRepoGetShiftsNotAssignReturnEmptyData, 
                $expectedResultEmpty
            ],
        ];
    }

    /**
     * test get function
     * @dataProvider getProvider
     * @return void
     */
    public function testGet($shiftRepoGetWithFormat, $expectedResult)
    {
        //1. create mock for shiftRepository
        $shiftRepoMock = TestUtil::mockClass(ShiftRepositoryContract::class, [
            ['method' => 'getWithFormat', 'returnOrThrow' => $shiftRepoGetWithFormat],
        ]);
        
        //2. make object ShiftService for testing
        $shiftService = new ShiftServiceImplementation($shiftRepoMock, resolve(StatusNodeRepositoryContract::class), resolve(HistoryRepositoryContract::class));
        
        //3. call the function to be tested
        $thereIsException = false;
        try {
            $result = $shiftService->get();
        }
        catch (\Exception $e) //if there is an exception, then the exception should be same as expectedResult (because, expected result is a exeption)
        {
            dd($e);
            $this->assertEquals($e, $expectedResult);
            $thereIsException = true;
        }

        //4. assert result just only if there is no exception when calling method 
        if(!$thereIsException)
        {
            $this->assertSame($result, $expectedResult);
        }

    }

    public function getProvider() {
        //input variable
        $shiftRepoGetWithFormatReturnThreeData = [
            [
              "id" => 10,
              "user_id" => "2",
              "room_id" => "7",
              "time_id" => "1",
              "date" => "2021-02-12",
              "histories_count" => "0",
              "room" => [
                "id" => 7,
                "name" => "Ruangan Tari Oktaviani",
              ],
              "user" => [
                "id" => 2,
                "name" => "Chelsea Shania Yolanda",
              ],
              "time" => [
                "id" => 1,
                "start" => "00:00",
                "end" => "06:00",
              ],
            ],
            [
              "id" => 9,
              "user_id" => "3",
              "room_id" => "2",
              "time_id" => "3",
              "date" => "2021-02-12",
              "histories_count" => "2",
              "room" => [
                "id" => 2,
                "name" => "Ruangan Kania Widiastuti",
              ],
              "user" => [
                "id" => 3,
                "name" => "Maria Permata",
              ],
              "time" => [
                "id" => 3,
                "start" => "10:00",
                "end" => "14:00",
              ],
            ],
            [
              "id" => 8,
              "user_id" => "1",
              "room_id" => "2",
              "time_id" => "4",
              "date" => "2021-02-12",
              "histories_count" => "5",
              "room" => [
                "id" => 2,
                "name" => "Ruangan Kania Widiastuti",
              ],
              "user" => [
                "id" => 1,
                "name" => "Tari Ulva Hartati",
              ],
              "time" => [
                "id" => 4,
                "start" => "14:00",
                "end" => "18:00",
              ],
            ],
        ];

        $shiftRepoGetWithFormatReturnEmptyData = [];
        

        //output
        $expectedResultWithCorrectData = [
            [
                'id' => 10,
                'date' => '2021-02-12',
                'room_name' => 'Ruangan Tari Oktaviani',
                'user_name' => 'Chelsea Shania Yolanda',
                'time_start_end' => '00:00 - 06:00',
                'total_histories' => 0
            ],
            [
                'id' => 9,
                'date' => '2021-02-12',
                'room_name' => 'Ruangan Kania Widiastuti',
                'user_name' => 'Maria Permata',
                'time_start_end' => '10:00 - 14:00',
                'total_histories' => 2
            ],
            [
                'id' => 8,
                'date' => '2021-02-12',
                'room_name' => 'Ruangan Kania Widiastuti',
                'user_name' => 'Tari Ulva Hartati',
                'time_start_end' => '14:00 - 18:00',
                'total_histories' => 5
            ],
        ];

        $expectedResultEmpty = [];

        //order : 
        //shiftrepo.getwithformat
        //expected result
        return [
            '1. when shiftrepo.getwithformat return three data, then return that three data' => [
                $shiftRepoGetWithFormatReturnThreeData,
                $expectedResultWithCorrectData
            ],
            '2. when shiftrepo.getwithformat return empty data then return that empty data' => [
                $shiftRepoGetWithFormatReturnEmptyData,
                $expectedResultEmpty
            ],
        ];
    }


    /**
     * test getToday function
     * @dataProvider getTodayProvider
     * @return void
     */
    public function testGetToday($shiftRepoGetTodayWithFormat, $expectedResult)
    {
        //1. create mock for shiftRepository
        $shiftRepoMock = TestUtil::mockClass(ShiftRepositoryContract::class, [
            ['method' => 'getTodayWithFormat', 'returnOrThrow' => $shiftRepoGetTodayWithFormat],
        ]);
        
        //2. make object ShiftService for testing
        $shiftService = new ShiftServiceImplementation($shiftRepoMock, resolve(StatusNodeRepositoryContract::class), resolve(HistoryRepositoryContract::class));
        
        //3. call the function to be tested
        $thereIsException = false;
        try {
            $result = $shiftService->getToday();
        }
        catch (\Exception $e) //if there is an exception, then the exception should be same as expectedResult (because, expected result is a exeption)
        {
            dd($e);
            $this->assertEquals($e, $expectedResult);
            $thereIsException = true;
        }

        //4. assert result just only if there is no exception when calling method 
        if(!$thereIsException)
        {
            $this->assertSame($result, $expectedResult);
        }

    }

    public function getTodayProvider() {
        //input variable
        $shiftRepoGetTodayWithFormatReturnThreeData = [
            [
              "id" => 10,
              "user_id" => "2",
              "room_id" => "7",
              "time_id" => "1",
              "date" => "2021-02-12",
              "histories_count" => "0",
              "room" => [
                "id" => 7,
                "name" => "Ruangan Tari Oktaviani",
              ],
              "user" => [
                "id" => 2,
                "name" => "Chelsea Shania Yolanda",
              ],
              "time" => [
                "id" => 1,
                "start" => "00:00",
                "end" => "06:00",
              ],
            ],
            [
              "id" => 9,
              "user_id" => "3",
              "room_id" => "2",
              "time_id" => "3",
              "date" => "2021-02-12",
              "histories_count" => "2",
              "room" => [
                "id" => 2,
                "name" => "Ruangan Kania Widiastuti",
              ],
              "user" => [
                "id" => 3,
                "name" => "Maria Permata",
              ],
              "time" => [
                "id" => 3,
                "start" => "10:00",
                "end" => "14:00",
              ],
            ],
            [
              "id" => 8,
              "user_id" => "1",
              "room_id" => "2",
              "time_id" => "4",
              "date" => "2021-02-12",
              "histories_count" => "5",
              "room" => [
                "id" => 2,
                "name" => "Ruangan Kania Widiastuti",
              ],
              "user" => [
                "id" => 1,
                "name" => "Tari Ulva Hartati",
              ],
              "time" => [
                "id" => 4,
                "start" => "14:00",
                "end" => "18:00",
              ],
            ],
        ];

        $shiftRepoGetTodayWithFormatReturnEmptyData = [];
        

        //output
        $expectedResultWithCorrectData = [
            [
                'id' => 10,
                'date' => '2021-02-12',
                'room_name' => 'Ruangan Tari Oktaviani',
                'user_name' => 'Chelsea Shania Yolanda',
                'time_start_end' => '00:00 - 06:00',
                'total_histories' => 0
            ],
            [
                'id' => 9,
                'date' => '2021-02-12',
                'room_name' => 'Ruangan Kania Widiastuti',
                'user_name' => 'Maria Permata',
                'time_start_end' => '10:00 - 14:00',
                'total_histories' => 2
            ],
            [
                'id' => 8,
                'date' => '2021-02-12',
                'room_name' => 'Ruangan Kania Widiastuti',
                'user_name' => 'Tari Ulva Hartati',
                'time_start_end' => '14:00 - 18:00',
                'total_histories' => 5
            ],
        ];

        $expectedResultEmpty = [];

        //order : 
        //shiftrepo.getTodaywithformat
        //expected result
        return [
            '1. when shiftrepo.getTodaywithformat return three data, then return that three data' => [
                $shiftRepoGetTodayWithFormatReturnThreeData,
                $expectedResultWithCorrectData
            ],
            '2. when shiftrepo.getTodaywithformat return empty data then return that empty data' => [
                $shiftRepoGetTodayWithFormatReturnEmptyData,
                $expectedResultEmpty
            ],
        ];
    }

    /**
     * test removeAndBackup function
     * @dataProvider removeAndBackupProvider
     * @return void
     */
    public function testRemoveAndBackup($shiftRepoBackupRun, $shiftRepoRemoveShiftsExceptToday, $expectedResult)
    {
        //1. create mock for shiftRepository
        $shiftRepoMock = TestUtil::mockClass(ShiftRepositoryContract::class, [
            ['method' => 'backupRun', 'returnOrThrow' => $shiftRepoBackupRun],
            ['method' => 'removeShiftsExceptToday', 'returnOrThrow' => $shiftRepoRemoveShiftsExceptToday],
        ]);
        
        //2. make object ShiftService for testing
        $shiftService = new ShiftServiceImplementation($shiftRepoMock, resolve(StatusNodeRepositoryContract::class), resolve(HistoryRepositoryContract::class));
        
        //3. call the function to be tested
        $thereIsException = false;
        try {
            $result = $shiftService->removeAndBackup();
        }
        catch (\Exception $e) //if there is an exception, then the exception should be same as expectedResult (because, expected result is a exeption)
        {
            dd($e);
            $this->assertEquals($e, $expectedResult);
            $thereIsException = true;
        }

        //4. assert result just only if there is no exception when calling method 
        if(!$thereIsException)
        {
            $this->assertSame($result, $expectedResult);
        }

    }

    public function removeAndBackupProvider() {
        //input variable
        $shiftRepoBackupRunReturnTrue = true;
        $shiftRepoRemoveShiftsExceptTodayReturnTrue = true;
        

        //output
        $expectedResult = true;

        //order : 
        //shiftrepo.backuprun, shiftrepo.removeshiftsexepcttoday
        //expected result
        return [
            '1. when shiftrepo.backuprun return true and shiftrepo.removeshiftsexcepttoday return true, then return true' => [
                $shiftRepoBackupRunReturnTrue, $shiftRepoRemoveShiftsExceptTodayReturnTrue,
                $expectedResult
            ],
        ];
    }

    
}