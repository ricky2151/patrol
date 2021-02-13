<?php

namespace Test\Unit;

use Tests\Testcase;
use App\Repositories\Contracts\StatusNodeRepositoryContract;

use App\Services\Implementations\StatusNodeServiceImplementation;

use App\TestUtil;

class StatusNodeServiceTest extends TestCase {

    /**
     * test get function
     * @dataProvider getProvider
     * @return void
     */
    public function testGet($statusNodeRepoAllOrder, $expectedResult)
    {
        //1. create mock for statusNodeRepository
        $statusNodeRepoMock = TestUtil::mockClass(StatusNodeRepositoryContract::class, [['method' => 'allOrder', 'returnOrThrow' => $statusNodeRepoAllOrder]]);
        
        //2. make object StatusNodeService for testing
        $statusNodeService = new StatusNodeServiceImplementation($statusNodeRepoMock);
        
        //3. call the function to be tested
        $thereIsException = false;
        try {
            $result = $statusNodeService->get();
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
        $statusNodeRepoAllOrderReturnThreeData = [
            [
                'id' => 1,
                'name' => 'Aman',
            ],
            [
                'id' => 2,
                'name' => 'Tidak Aman',
            ],
            [
                'id' => 3,
                'name' => 'Mencurigakan',
            ],
        ];
        $statusNodeRepoAllOrderReturnEmptyData = [

        ];

        //output
        $expectedResultWithThreeData = $statusNodeRepoAllOrderReturnThreeData;
        $expectedResultEmpty = [];

        //order : 
        //statusNoderepoallorder
        //expected result
        return [
            '1. when statusNoderepo.allorder return three data, then return that three data' => [
                $statusNodeRepoAllOrderReturnThreeData, 
                $expectedResultWithThreeData
            ],
            '2. when statusNoderepo.allorder return empty data, then return that empty data' => [
                $statusNodeRepoAllOrderReturnEmptyData, 
                $expectedResultEmpty
            ],
        ];
    }

    /**
     * test store function
     * @dataProvider storeProvider
     * @return void
     */
    public function testStore($input, $statusNodeRepoStore, $expectedResult)
    {
        //1. create mock for statusNodeRepository
        $statusNodeRepoMock = TestUtil::mockClass(StatusNodeRepositoryContract::class, [['method' => 'store', 'returnOrThrow' => $statusNodeRepoStore]]);
        
        //2. make object StatusNodeService for testing
        $statusNodeService = new StatusNodeServiceImplementation($statusNodeRepoMock);
        
        //3. call the function to be tested
        $thereIsException = false;
        try {
            $result = $statusNodeService->store($input);
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
            'name' => 'Ada maling !'
        ];

        $statusNodeRepoStoreReturnNewData = [
            'id' => 6,
            'name' => 'Ada maling !',
        ];

        //output
        $expectedResultNewData = $statusNodeRepoStoreReturnNewData;

        //order : 
        //input, statusNoderepostore
        //expected result
        return [
            '1. when input is valid and statusNoderepo.store return new data, then return that new data' => [
                $validInput, $statusNodeRepoStoreReturnNewData,
                $expectedResultNewData
            ],
        ];
    }


    /**
     * test update function
     * @dataProvider updateProvider
     * @return void
     */
    public function testUpdate($id, $input, $statusNodeRepoUpdate, $expectedResult)
    {
        //1. create mock for statusNodeRepository
        $statusNodeRepoMock = TestUtil::mockClass(StatusNodeRepositoryContract::class, [['method' => 'update', 'returnOrThrow' => $statusNodeRepoUpdate]]);
        
        //2. make object StatusNodeService for testing
        $statusNodeService = new StatusNodeServiceImplementation($statusNodeRepoMock);
        
        //3. call the function to be tested
        $thereIsException = false;
        try {
            $result = $statusNodeService->update($input, $id);
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
            'name' => 'Ada maling !'
        ];

        $statusNodeRepoUpdateReturnUpdatedData = [
            'id' => 6,
            'name' => 'Ada maling !',
        ];

        //output
        $expectedResultUpdatedData = $statusNodeRepoUpdateReturnUpdatedData;

        //order : 
        //id, input, statusNoderepoupdate
        //expected result
        return [
            '1. when id,input is valid and statusNoderepo.update return updated data, then return that updated data' => [
                $validId, $validInput, $statusNodeRepoUpdateReturnUpdatedData,
                $expectedResultUpdatedData
            ],
        ];
    }


    /**
     * test delete function
     * @dataProvider deleteProvider
     * @return void
     */
    public function testDelete($id, $statusNodeRepoDelete, $expectedResult)
    {
        //1. create mock for statusNodeRepository
        $statusNodeRepoMock = TestUtil::mockClass(StatusNodeRepositoryContract::class, [['method' => 'delete', 'returnOrThrow' => $statusNodeRepoDelete]]);
        
        //2. make object StatusNodeService for testing
        $statusNodeService = new StatusNodeServiceImplementation($statusNodeRepoMock);
        
        //3. call the function to be tested
        $thereIsException = false;
        try {
            $result = $statusNodeService->delete($id);
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

        $statusNodeRepoDeleteReturnTrue = true;

        //output
        $expectedResultTrue = $statusNodeRepoDeleteReturnTrue;

        //order : 
        //id, statusNoderepodelete
        //expected result
        return [
            '1. when id is valid and statusNoderepo.delete return true, then return true' => [
                $validId, $statusNodeRepoDeleteReturnTrue,
                $expectedResultTrue
            ],
        ];
    }
}