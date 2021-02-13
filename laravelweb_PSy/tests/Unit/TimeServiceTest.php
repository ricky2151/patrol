<?php

namespace Test\Unit;

use Tests\Testcase;
use App\Repositories\Contracts\TimeRepositoryContract;

use App\Services\Implementations\TimeServiceImplementation;

use App\TestUtil;

class TimeServiceTest extends TestCase {

    /**
     * test get function
     * @dataProvider getProvider
     * @return void
     */
    public function testGet($timeRepoAllOrder, $expectedResult)
    {
        //1. create mock for timeRepository
        $timeRepoMock = TestUtil::mockClass(TimeRepositoryContract::class, [['method' => 'allOrder', 'returnOrThrow' => $timeRepoAllOrder]]);
        
        //2. make object TimeService for testing
        $timeService = new TimeServiceImplementation($timeRepoMock);
        
        //3. call the function to be tested
        $thereIsException = false;
        try {
            $result = $timeService->get();
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
        $timeRepoAllOrderReturnThreeData = [
            [
                'id' => 1,
                'start' => '10:00',
                'end' => '12:00'
            ],
            [
                'id' => 2,
                'start' => '12:00',
                'end' => '14:00'
            ],
            [
                'id' => 3,
                'start' => '14:00',
                'end' => '16:00'
            ],
        ];
        $timeRepoAllOrderReturnEmptyData = [

        ];

        //output
        $expectedResultWithThreeData = [
            [
                'id' => 1,
                'start' => '10:00',
                'end' => '12:00',
                'name' => '10:00 - 12:00',
            ],
            [
                'id' => 2,
                'start' => '12:00',
                'end' => '14:00',
                'name' => '12:00 - 14:00',
            ],
            [
                'id' => 3,
                'start' => '14:00',
                'end' => '16:00',
                'name' => '14:00 - 16:00',
            ],
        ];
        $expectedResultEmpty = [];

        //order : 
        //timerepoallorder
        //expected result
        return [
            '1. when timerepo.allorder return three data, then return that three data' => [
                $timeRepoAllOrderReturnThreeData, 
                $expectedResultWithThreeData
            ],
            '2. when timerepo.allorder return empty data, then return that empty data' => [
                $timeRepoAllOrderReturnEmptyData, 
                $expectedResultEmpty
            ],
        ];
    }

    /**
     * test store function
     * @dataProvider storeProvider
     * @return void
     */
    public function testStore($input, $timeRepoStore, $expectedResult)
    {
        //1. create mock for timeRepository
        $timeRepoMock = TestUtil::mockClass(TimeRepositoryContract::class, [['method' => 'store', 'returnOrThrow' => $timeRepoStore]]);
        
        //2. make object TimeService for testing
        $timeService = new TimeServiceImplementation($timeRepoMock);
        
        //3. call the function to be tested
        $thereIsException = false;
        try {
            $result = $timeService->store($input);
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
            'start' => '18:00',
            'end' => '20:00'
        ];

        $timeRepoStoreReturnNewData = [
            'id' => 6,
            'start' => '18:00',
            'end' => '20:00'
        ];

        //output
        $expectedResultNewData = $timeRepoStoreReturnNewData;

        //order : 
        //input, timerepostore
        //expected result
        return [
            '1. when input is valid and timerepo.store return new data, then return that new data' => [
                $validInput, $timeRepoStoreReturnNewData,
                $expectedResultNewData
            ],
        ];
    }


    /**
     * test update function
     * @dataProvider updateProvider
     * @return void
     */
    public function testUpdate($id, $input, $timeRepoUpdate, $expectedResult)
    {
        //1. create mock for timeRepository
        $timeRepoMock = TestUtil::mockClass(TimeRepositoryContract::class, [['method' => 'update', 'returnOrThrow' => $timeRepoUpdate]]);
        
        //2. make object TimeService for testing
        $timeService = new TimeServiceImplementation($timeRepoMock);
        
        //3. call the function to be tested
        $thereIsException = false;
        try {
            $result = $timeService->update($input, $id);
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
            'start' => '20:00',
            'end' => '22:00'
        ];

        $timeRepoUpdateReturnUpdatedData = [
            'id' => 6,
            'start' => '20:00',
            'end' => '22:00'
        ];

        //output
        $expectedResultUpdatedData = $timeRepoUpdateReturnUpdatedData;

        //order : 
        //id, input, timerepoupdate
        //expected result
        return [
            '1. when id,input is valid and timerepo.update return updated data, then return that updated data' => [
                $validId, $validInput, $timeRepoUpdateReturnUpdatedData,
                $expectedResultUpdatedData
            ],
        ];
    }


    /**
     * test delete function
     * @dataProvider deleteProvider
     * @return void
     */
    public function testDelete($id, $timeRepoDelete, $expectedResult)
    {
        //1. create mock for timeRepository
        $timeRepoMock = TestUtil::mockClass(TimeRepositoryContract::class, [['method' => 'delete', 'returnOrThrow' => $timeRepoDelete]]);
        
        //2. make object TimeService for testing
        $timeService = new TimeServiceImplementation($timeRepoMock);
        
        //3. call the function to be tested
        $thereIsException = false;
        try {
            $result = $timeService->delete($id);
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

        $timeRepoDeleteReturnTrue = true;

        //output
        $expectedResultTrue = $timeRepoDeleteReturnTrue;

        //order : 
        //id, timerepodelete
        //expected result
        return [
            '1. when id is valid and timerepo.delete return true, then return true' => [
                $validId, $timeRepoDeleteReturnTrue,
                $expectedResultTrue
            ],
        ];
    }
}