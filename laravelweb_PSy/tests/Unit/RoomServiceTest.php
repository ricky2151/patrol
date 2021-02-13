<?php

namespace Test\Unit;

use Tests\Testcase;
use App\Repositories\Contracts\RoomRepositoryContract;

use App\Services\Implementations\RoomServiceImplementation;

use App\TestUtil;

class RoomServiceTest extends TestCase {

    /**
     * test get function
     * @dataProvider getProvider
     * @return void
     */
    public function testGet($roomRepoAllOrder, $expectedResult)
    {
        //1. create mock for roomRepository
        $roomRepoMock = TestUtil::mockClass(RoomRepositoryContract::class, [
            ['method' => 'datatableWith->orderBy->get', 'returnOrThrow' => $roomRepoAllOrder],
        ]);
        
        //2. make object RoomService for testing
        $roomService = new RoomServiceImplementation($roomRepoMock);
        
        //3. call the function to be tested
        $thereIsException = false;
        try {
            $result = $roomService->get();
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
        $roomRepoAllOrderReturnThreeData = [
            [
                'id' => 1,
                'name' => 'Ruangan Didaktos',
                'floor_id' => 1,
                'building_id' => 1,
                'gateway_id' => 1,
                'floor' => [
                    'id' => 1,
                    'name' => 'Lantai 1'
                ],
                'building' => [
                    'id' => 1,
                    'name' => 'Gedung Didaktos'
                ],
                'gateway' => [
                    'id' => 1,
                    'name' => 'Gateway Didaktos'
                ],
            ],
            [
                'id' => 2,
                'name' => 'Ruangan Kamar Mandi',
                'floor_id' => 1,
                'building_id' => 1,
                'gateway_id' => 1,
                'floor' => [
                    'id' => 1,
                    'name' => 'Lantai 1'
                ],
                'building' => [
                    'id' => 1,
                    'name' => 'Gedung Didaktos'
                ],
                'gateway' => [
                    'id' => 1,
                    'name' => 'Gateway Didaktos'
                ],
            ],
            [
                'id' => 3,
                'name' => 'Ruangan Biro 1',
                'floor_id' => 1,
                'building_id' => 1,
                'gateway_id' => 1,
                'floor' => [
                    'id' => 1,
                    'name' => 'Lantai 1'
                ],
                'building' => [
                    'id' => 1,
                    'name' => 'Gedung Didaktos'
                ],
                'gateway' => [
                    'id' => 1,
                    'name' => 'Gateway Didaktos'
                ],
            ],
        ];
        $roomRepoAllOrderReturnEmptyData = [

        ];

        //output
        $expectedResultWithThreeData = [
            [
                'id' => 1,
                'name' => 'Ruangan Didaktos',
                'floor_id' => 1,
                'building_id' => 1,
                'gateway_id' => 1,
                'floor_name' => 'Lantai 1',
                'building_name' => 'Gedung Didaktos',
                'gateway_name' => 'Gateway Didaktos'
            ],
            [
                'id' => 2,
                'name' => 'Ruangan Kamar Mandi',
                'floor_id' => 1,
                'building_id' => 1,
                'gateway_id' => 1,
                'floor_name' => 'Lantai 1',
                'building_name' => 'Gedung Didaktos',
                'gateway_name' => 'Gateway Didaktos'
            ],
            [
                'id' => 3,
                'name' => 'Ruangan Biro 1',
                'floor_id' => 1,
                'building_id' => 1,
                'gateway_id' => 1,
                'floor_name' => 'Lantai 1',
                'building_name' => 'Gedung Didaktos',
                'gateway_name' => 'Gateway Didaktos'
            ],
        ];
        $expectedResultEmpty = [];

        //order : 
        //roomrepoallorder
        //expected result
        return [
            '1. when roomrepo.allorder return three data, then return that three data' => [
                $roomRepoAllOrderReturnThreeData, 
                $expectedResultWithThreeData
            ],
            '2. when roomrepo.allorder return empty data, then return that empty data' => [
                $roomRepoAllOrderReturnEmptyData, 
                $expectedResultEmpty
            ],
        ];
    }

    /**
     * test store function
     * @dataProvider storeProvider
     * @return void
     */
    public function testStore($input, $roomRepoStore, $expectedResult)
    {
        //1. create mock for roomRepository
        $roomRepoMock = TestUtil::mockClass(RoomRepositoryContract::class, [['method' => 'store', 'returnOrThrow' => $roomRepoStore]]);
        
        //2. make object RoomService for testing
        $roomService = new RoomServiceImplementation($roomRepoMock);
        
        //3. call the function to be tested
        $thereIsException = false;
        try {
            $result = $roomService->store($input);
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

    public function storeProvider() {
        //input variable
        $validInput = [
            'name' => 'Ruangan Didaktos',
            'floor_id' => 1,
            'building_id' => 1,
            'gateway_id' => 1,
        ];

        $roomRepoStoreReturnNewData = [
            'id' => 6,
            'name' => 'Ruangan Didaktos',
            'floor_id' => 1,
            'building_id' => 1,
            'gateway_id' => 1,
        ];

        //output
        $expectedResultNewData = $roomRepoStoreReturnNewData;

        //order : 
        //input, roomrepostore
        //expected result
        return [
            '1. when input is valid and roomrepo.store return new data, then return that new data' => [
                $validInput, $roomRepoStoreReturnNewData,
                $expectedResultNewData
            ],
        ];
    }


    /**
     * test update function
     * @dataProvider updateProvider
     * @return void
     */
    public function testUpdate($id, $input, $roomRepoUpdate, $expectedResult)
    {
        //1. create mock for roomRepository
        $roomRepoMock = TestUtil::mockClass(RoomRepositoryContract::class, [['method' => 'update', 'returnOrThrow' => $roomRepoUpdate]]);
        
        //2. make object RoomService for testing
        $roomService = new RoomServiceImplementation($roomRepoMock);
        
        //3. call the function to be tested
        $thereIsException = false;
        try {
            $result = $roomService->update($input, $id);
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

    public function updateProvider() {
        //input variable
        $validId = 1;

        $validInput = [
            'name' => 'Ruangan Didaktos',
            'floor_id' => 1,
            'building_id' => 1,
            'gateway_id' => 1,
        ];

        $roomRepoUpdateReturnUpdatedData = [
            'id' => 6,
            'name' => 'Ruangan Didaktos',
            'floor_id' => 1,
            'building_id' => 1,
            'gateway_id' => 1,
        ];

        //output
        $expectedResultUpdatedData = $roomRepoUpdateReturnUpdatedData;

        //order : 
        //id, input, roomrepoupdate
        //expected result
        return [
            '1. when id,input is valid and roomrepo.update return updated data, then return that updated data' => [
                $validId, $validInput, $roomRepoUpdateReturnUpdatedData,
                $expectedResultUpdatedData
            ],
        ];
    }


    /**
     * test delete function
     * @dataProvider deleteProvider
     * @return void
     */
    public function testDelete($id, $roomRepoDelete, $expectedResult)
    {
        //1. create mock for roomRepository
        $roomRepoMock = TestUtil::mockClass(RoomRepositoryContract::class, [['method' => 'delete', 'returnOrThrow' => $roomRepoDelete]]);
        
        //2. make object RoomService for testing
        $roomService = new RoomServiceImplementation($roomRepoMock);
        
        //3. call the function to be tested
        $thereIsException = false;
        try {
            $result = $roomService->delete($id);
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

        $roomRepoDeleteReturnTrue = true;

        //output
        $expectedResultTrue = $roomRepoDeleteReturnTrue;

        //order : 
        //id, roomrepodelete
        //expected result
        return [
            '1. when id is valid and roomrepo.delete return true, then return true' => [
                $validId, $roomRepoDeleteReturnTrue,
                $expectedResultTrue
            ],
        ];
    }


    /**
     * test get without format function
     * @dataProvider getWithoutFormatProvider
     * @return void
     */
    public function testGetWithoutFormat($roomRepoAllOrder, $expectedResult)
    {
        //1. create mock for roomRepository
        $roomRepoMock = TestUtil::mockClass(RoomRepositoryContract::class, [
            ['method' => 'datatableWith->orderBy->get', 'returnOrThrow' => $roomRepoAllOrder],
        ]);
        
        //2. make object RoomService for testing
        $roomService = new RoomServiceImplementation($roomRepoMock);
        
        //3. call the function to be tested
        $thereIsException = false;
        try {
            $result = $roomService->getWithoutFormat();
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

    public function getWithoutFormatProvider() {
        //input variable
        $roomRepoAllOrderReturnThreeData = [
            [
                'id' => 1,
                'name' => 'Ruangan Didaktos',
                'floor_id' => 1,
                'building_id' => 1,
                'gateway_id' => 1,
                'floor' => [
                    'id' => 1,
                    'name' => 'Lantai 1'
                ],
                'building' => [
                    'id' => 1,
                    'name' => 'Gedung Didaktos'
                ],
                'gateway' => [
                    'id' => 1,
                    'name' => 'Gateway Didaktos'
                ],
            ],
            [
                'id' => 2,
                'name' => 'Ruangan Kamar Mandi',
                'floor_id' => 1,
                'building_id' => 1,
                'gateway_id' => 1,
                'floor' => [
                    'id' => 1,
                    'name' => 'Lantai 1'
                ],
                'building' => [
                    'id' => 1,
                    'name' => 'Gedung Didaktos'
                ],
                'gateway' => [
                    'id' => 1,
                    'name' => 'Gateway Didaktos'
                ],
            ],
            [
                'id' => 3,
                'name' => 'Ruangan Biro 1',
                'floor_id' => 1,
                'building_id' => 1,
                'gateway_id' => 1,
                'floor' => [
                    'id' => 1,
                    'name' => 'Lantai 1'
                ],
                'building' => [
                    'id' => 1,
                    'name' => 'Gedung Didaktos'
                ],
                'gateway' => [
                    'id' => 1,
                    'name' => 'Gateway Didaktos'
                ],
            ],
        ];
        $roomRepoAllOrderReturnEmptyData = [

        ];

        //output
        $expectedResultWithThreeData = $roomRepoAllOrderReturnThreeData;
        $expectedResultEmpty = [];

        //order : 
        //roomrepoallorder
        //expected result
        return [
            '1. when roomrepo.allorder return three data, then return that three data' => [
                $roomRepoAllOrderReturnThreeData, 
                $expectedResultWithThreeData
            ],
            '2. when roomrepo.allorder return empty data, then return that empty data' => [
                $roomRepoAllOrderReturnEmptyData, 
                $expectedResultEmpty
            ],
        ];
    }

    /**
     * test find function
     * @dataProvider findProvider
     * @return void
     */
    public function testFind($id, $roomRepoFind, $expectedResult)
    {
        //1. create mock for roomRepository
        $roomRepoMock = TestUtil::mockClass(RoomRepositoryContract::class, [['method' => 'find', 'returnOrThrow' => $roomRepoFind]]);
        
        //2. make object RoomService for testing
        $roomService = new RoomServiceImplementation($roomRepoMock);
        
        //3. call the function to be tested
        $thereIsException = false;
        try {
            $result = $roomService->find($id);
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

    public function findProvider() {
        //input variable
        $validId = 1;

        $roomRepoFindReturnCorrectData = [
            "id" => 1,
            "name" => "Ruangan Natalia Padmasari S.Farm",
            "floor_id" => "3",
            "building_id" => "4",
            "gateway_id" => "5",
        ];

        //output
        $expectedResultTrue = $roomRepoFindReturnCorrectData;

        //order : 
        //id, roomrepofind
        //expected result
        return [
            '1. when id is valid and roomrepo.find return correct data, then return correct data' => [
                $validId, $roomRepoFindReturnCorrectData,
                $expectedResultTrue
            ],
        ];
    }
}