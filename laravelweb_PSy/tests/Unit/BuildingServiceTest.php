<?php

namespace Test\Unit;

use Tests\Testcase;
use App\Repositories\Contracts\BuildingRepositoryContract;

use App\Services\Implementations\BuildingServiceImplementation;

use App\TestUtil;

class BuildingServiceTest extends TestCase {

    /**
     * test get function
     * @dataProvider getProvider
     * @return void
     */
    public function testGet($buildingRepoAllOrder, $expectedResult)
    {
        //1. create mock for buildingRepository
        $buildingRepoMock = TestUtil::mockClass(BuildingRepositoryContract::class, [['method' => 'allOrder', 'returnOrThrow' => $buildingRepoAllOrder]]);
        
        //2. make object BuildingService for testing
        $buildingService = new BuildingServiceImplementation($buildingRepoMock);
        
        //3. call the function to be tested
        $thereIsException = false;
        try {
            $result = $buildingService->get();
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

    public function getProvider() {
        //input variable
        $buildingRepoAllOrderReturnThreeData = [
            [
                'id' => 1,
                'name' => 'Gedung Agape',
            ],
            [
                'id' => 2,
                'name' => 'Gedung Biblos',
            ],
            [
                'id' => 3,
                'name' => 'Gedung Didaktos',
            ],
        ];
        $buildingRepoAllOrderReturnEmptyData = [

        ];

        //output
        $expectedResultWithThreeData = $buildingRepoAllOrderReturnThreeData;
        $expectedResultEmpty = [];

        //order : 
        //buildingrepoallorder
        //expected result
        return [
            '1. when buildingrepo.allorder return three data, then return that three data' => [
                $buildingRepoAllOrderReturnThreeData, 
                $expectedResultWithThreeData
            ],
            '2. when buildingrepo.allorder return empty data, then return that empty data' => [
                $buildingRepoAllOrderReturnEmptyData, 
                $expectedResultEmpty
            ],
        ];
    }

    /**
     * test store function
     * @dataProvider storeProvider
     * @return void
     */
    public function testStore($input, $buildingRepoStore, $expectedResult)
    {
        //1. create mock for buildingRepository
        $buildingRepoMock = TestUtil::mockClass(BuildingRepositoryContract::class, [['method' => 'store', 'returnOrThrow' => $buildingRepoStore]]);
        
        //2. make object BuildingService for testing
        $buildingService = new BuildingServiceImplementation($buildingRepoMock);
        
        //3. call the function to be tested
        $thereIsException = false;
        try {
            $result = $buildingService->store($input);
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
            'name' => 'Gedung Makarios'
        ];

        $buildingRepoStoreReturnNewData = [
            'id' => 6,
            'name' => 'Gedung Makarios',
        ];

        //output
        $expectedResultNewData = $buildingRepoStoreReturnNewData;

        //order : 
        //input, buildingrepostore
        //expected result
        return [
            '1. when input is valid and buildingrepo.store return new data, then return that new data' => [
                $validInput, $buildingRepoStoreReturnNewData,
                $expectedResultNewData
            ],
        ];
    }


    /**
     * test update function
     * @dataProvider updateProvider
     * @return void
     */
    public function testUpdate($id, $input, $buildingRepoUpdate, $expectedResult)
    {
        //1. create mock for buildingRepository
        $buildingRepoMock = TestUtil::mockClass(BuildingRepositoryContract::class, [['method' => 'update', 'returnOrThrow' => $buildingRepoUpdate]]);
        
        //2. make object BuildingService for testing
        $buildingService = new BuildingServiceImplementation($buildingRepoMock);
        
        //3. call the function to be tested
        $thereIsException = false;
        try {
            $result = $buildingService->update($input, $id);
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
            'name' => 'Gedung Makarios Baru'
        ];

        $buildingRepoUpdateReturnUpdatedData = [
            'id' => 6,
            'name' => 'Gedung Makarios Baru',
        ];

        //output
        $expectedResultUpdatedData = $buildingRepoUpdateReturnUpdatedData;

        //order : 
        //id, input, buildingrepoupdate
        //expected result
        return [
            '1. when id,input is valid and buildingrepo.update return updated data, then return that updated data' => [
                $validId, $validInput, $buildingRepoUpdateReturnUpdatedData,
                $expectedResultUpdatedData
            ],
        ];
    }


    /**
     * test delete function
     * @dataProvider deleteProvider
     * @return void
     */
    public function testDelete($id, $buildingRepoDelete, $expectedResult)
    {
        //1. create mock for buildingRepository
        $buildingRepoMock = TestUtil::mockClass(BuildingRepositoryContract::class, [['method' => 'delete', 'returnOrThrow' => $buildingRepoDelete]]);
        
        //2. make object BuildingService for testing
        $buildingService = new BuildingServiceImplementation($buildingRepoMock);
        
        //3. call the function to be tested
        $thereIsException = false;
        try {
            $result = $buildingService->delete($id);
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

        $buildingRepoDeleteReturnTrue = true;

        //output
        $expectedResultTrue = $buildingRepoDeleteReturnTrue;

        //order : 
        //id, buildingrepodelete
        //expected result
        return [
            '1. when id is valid and buildingrepo.delete return true, then return true' => [
                $validId, $buildingRepoDeleteReturnTrue,
                $expectedResultTrue
            ],
        ];
    }
}