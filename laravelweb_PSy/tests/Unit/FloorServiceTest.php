<?php

namespace Test\Unit;

use Tests\Testcase;
use App\Repositories\Contracts\FloorRepositoryContract;

use App\Services\Implementations\FloorServiceImplementation;

use App\TestUtil;

class FloorServiceTest extends TestCase {

    /**
     * test get function
     * @dataProvider getProvider
     * @return void
     */
    public function testGet($floorRepoAllOrder, $expectedResult)
    {
        //1. create mock for floorRepository
        $floorRepoMock = TestUtil::mockClass(FloorRepositoryContract::class, [['method' => 'allOrder', 'returnOrThrow' => $floorRepoAllOrder]]);
        
        //2. make object FloorService for testing
        $floorService = new FloorServiceImplementation($floorRepoMock);
        
        //3. call the function to be tested
        $thereIsException = false;
        try {
            $result = $floorService->get();
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
        $floorRepoAllOrderReturnThreeData = [
            [
                'id' => 1,
                'name' => 'Lantai 1',
            ],
            [
                'id' => 2,
                'name' => 'Lantai 2',
            ],
            [
                'id' => 3,
                'name' => 'Lantai 3',
            ],
        ];
        $floorRepoAllOrderReturnEmptyData = [

        ];

        //output
        $expectedResultWithThreeData = $floorRepoAllOrderReturnThreeData;
        $expectedResultEmpty = [];

        //order : 
        //floorrepoallorder
        //expected result
        return [
            '1. when floorrepo.allorder return three data, then return that three data' => [
                $floorRepoAllOrderReturnThreeData, 
                $expectedResultWithThreeData
            ],
            '2. when floorrepo.allorder return empty data, then return that empty data' => [
                $floorRepoAllOrderReturnEmptyData, 
                $expectedResultEmpty
            ],
        ];
    }

    /**
     * test store function
     * @dataProvider storeProvider
     * @return void
     */
    public function testStore($input, $floorRepoStore, $expectedResult)
    {
        //1. create mock for floorRepository
        $floorRepoMock = TestUtil::mockClass(FloorRepositoryContract::class, [['method' => 'store', 'returnOrThrow' => $floorRepoStore]]);
        
        //2. make object FloorService for testing
        $floorService = new FloorServiceImplementation($floorRepoMock);
        
        //3. call the function to be tested
        $thereIsException = false;
        try {
            $result = $floorService->store($input);
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
            'name' => 'Lantai 10'
        ];

        $floorRepoStoreReturnNewData = [
            'id' => 6,
            'name' => 'Lantai 10',
        ];

        //output
        $expectedResultNewData = $floorRepoStoreReturnNewData;

        //order : 
        //input, floorrepostore
        //expected result
        return [
            '1. when input is valid and floorrepo.store return new data, then return that new data' => [
                $validInput, $floorRepoStoreReturnNewData,
                $expectedResultNewData
            ],
        ];
    }


    /**
     * test update function
     * @dataProvider updateProvider
     * @return void
     */
    public function testUpdate($id, $input, $floorRepoUpdate, $expectedResult)
    {
        //1. create mock for floorRepository
        $floorRepoMock = TestUtil::mockClass(FloorRepositoryContract::class, [['method' => 'update', 'returnOrThrow' => $floorRepoUpdate]]);
        
        //2. make object FloorService for testing
        $floorService = new FloorServiceImplementation($floorRepoMock);
        
        //3. call the function to be tested
        $thereIsException = false;
        try {
            $result = $floorService->update($input, $id);
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
            'name' => 'Lantai 5'
        ];

        $floorRepoUpdateReturnUpdatedData = [
            'id' => 6,
            'name' => 'Lantai 5',
        ];

        //output
        $expectedResultUpdatedData = $floorRepoUpdateReturnUpdatedData;

        //order : 
        //id, input, floorrepoupdate
        //expected result
        return [
            '1. when id,input is valid and floorrepo.update return updated data, then return that updated data' => [
                $validId, $validInput, $floorRepoUpdateReturnUpdatedData,
                $expectedResultUpdatedData
            ],
        ];
    }


    /**
     * test delete function
     * @dataProvider deleteProvider
     * @return void
     */
    public function testDelete($id, $floorRepoDelete, $expectedResult)
    {
        //1. create mock for floorRepository
        $floorRepoMock = TestUtil::mockClass(FloorRepositoryContract::class, [['method' => 'delete', 'returnOrThrow' => $floorRepoDelete]]);
        
        //2. make object FloorService for testing
        $floorService = new FloorServiceImplementation($floorRepoMock);
        
        //3. call the function to be tested
        $thereIsException = false;
        try {
            $result = $floorService->delete($id);
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

        $floorRepoDeleteReturnTrue = true;

        //output
        $expectedResultTrue = $floorRepoDeleteReturnTrue;

        //order : 
        //id, floorrepodelete
        //expected result
        return [
            '1. when id is valid and floorrepo.delete return true, then return true' => [
                $validId, $floorRepoDeleteReturnTrue,
                $expectedResultTrue
            ],
        ];
    }
}