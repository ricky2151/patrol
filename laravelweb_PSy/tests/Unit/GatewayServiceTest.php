<?php

namespace Test\Unit;

use Tests\Testcase;
use App\Repositories\Contracts\GatewayRepositoryContract;

use App\Services\Implementations\GatewayServiceImplementation;

use App\TestUtil;

class GatewayServiceTest extends TestCase {

    /**
     * test get function
     * @dataProvider getProvider
     * @return void
     */
    public function testGet($gatewayRepoAllOrder, $expectedResult)
    {
        //1. create mock for gatewayRepository
        $gatewayRepoMock = TestUtil::mockClass(GatewayRepositoryContract::class, [['method' => 'allOrder', 'returnOrThrow' => $gatewayRepoAllOrder]]);
        
        //2. make object GatewayService for testing
        $gatewayService = new GatewayServiceImplementation($gatewayRepoMock);
        
        //3. call the function to be tested
        $thereIsException = false;
        try {
            $result = $gatewayService->get();
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
        $gatewayRepoAllOrderReturnThreeData = [
            [
                'id' => 1,
                'name' => 'Gateway Didaktos',
                'location' => 'di pojok biro 1'
            ],
            [
                'id' => 2,
                'name' => 'Gateway Hagios',
                'location' => 'di pojok biro 3'
            ],
            [
                'id' => 3,
                'name' => 'Gateway Agape',
                'location' => 'di pojok biro 5'
            ],
        ];
        $gatewayRepoAllOrderReturnEmptyData = [

        ];

        //output
        $expectedResultWithThreeData = $gatewayRepoAllOrderReturnThreeData;
        $expectedResultEmpty = [];

        //order : 
        //gatewayrepoallorder
        //expected result
        return [
            '1. when gatewayrepo.allorder return three data, then return that three data' => [
                $gatewayRepoAllOrderReturnThreeData, 
                $expectedResultWithThreeData
            ],
            '2. when gatewayrepo.allorder return empty data, then return that empty data' => [
                $gatewayRepoAllOrderReturnEmptyData, 
                $expectedResultEmpty
            ],
        ];
    }

    /**
     * test store function
     * @dataProvider storeProvider
     * @return void
     */
    public function testStore($input, $gatewayRepoStore, $expectedResult)
    {
        //1. create mock for gatewayRepository
        $gatewayRepoMock = TestUtil::mockClass(GatewayRepositoryContract::class, [['method' => 'store', 'returnOrThrow' => $gatewayRepoStore]]);
        
        //2. make object GatewayService for testing
        $gatewayService = new GatewayServiceImplementation($gatewayRepoMock);
        
        //3. call the function to be tested
        $thereIsException = false;
        try {
            $result = $gatewayService->store($input);
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
            'name' => 'Gateway Didaktos',
            'location' => 'pojok biro 1'
        ];

        $gatewayRepoStoreReturnNewData = [
            'id' => 6,
            'name' => 'Gateway Didaktos',
            'location' => 'pojok biro 1'
        ];

        //output
        $expectedResultNewData = $gatewayRepoStoreReturnNewData;

        //order : 
        //input, gatewayrepostore
        //expected result
        return [
            '1. when input is valid and gatewayrepo.store return new data, then return that new data' => [
                $validInput, $gatewayRepoStoreReturnNewData,
                $expectedResultNewData
            ],
        ];
    }


    /**
     * test update function
     * @dataProvider updateProvider
     * @return void
     */
    public function testUpdate($id, $input, $gatewayRepoUpdate, $expectedResult)
    {
        //1. create mock for gatewayRepository
        $gatewayRepoMock = TestUtil::mockClass(GatewayRepositoryContract::class, [['method' => 'update', 'returnOrThrow' => $gatewayRepoUpdate]]);
        
        //2. make object GatewayService for testing
        $gatewayService = new GatewayServiceImplementation($gatewayRepoMock);
        
        //3. call the function to be tested
        $thereIsException = false;
        try {
            $result = $gatewayService->update($input, $id);
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
            'name' => 'Gateway Didaktos',
            'location' => 'Pojok biro 1'
        ];

        $gatewayRepoUpdateReturnUpdatedData = [
            'id' => 6,
            'name' => 'Gateway Didaktos',
            'location' => 'Pojok biro 1'
        ];

        //output
        $expectedResultUpdatedData = $gatewayRepoUpdateReturnUpdatedData;

        //order : 
        //id, input, gatewayrepoupdate
        //expected result
        return [
            '1. when id,input is valid and gatewayrepo.update return updated data, then return that updated data' => [
                $validId, $validInput, $gatewayRepoUpdateReturnUpdatedData,
                $expectedResultUpdatedData
            ],
        ];
    }


    /**
     * test delete function
     * @dataProvider deleteProvider
     * @return void
     */
    public function testDelete($id, $gatewayRepoDelete, $expectedResult)
    {
        //1. create mock for gatewayRepository
        $gatewayRepoMock = TestUtil::mockClass(GatewayRepositoryContract::class, [['method' => 'delete', 'returnOrThrow' => $gatewayRepoDelete]]);
        
        //2. make object GatewayService for testing
        $gatewayService = new GatewayServiceImplementation($gatewayRepoMock);
        
        //3. call the function to be tested
        $thereIsException = false;
        try {
            $result = $gatewayService->delete($id);
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

        $gatewayRepoDeleteReturnTrue = true;

        //output
        $expectedResultTrue = $gatewayRepoDeleteReturnTrue;

        //order : 
        //id, gatewayrepodelete
        //expected result
        return [
            '1. when id is valid and gatewayrepo.delete return true, then return true' => [
                $validId, $gatewayRepoDeleteReturnTrue,
                $expectedResultTrue
            ],
        ];
    }
}