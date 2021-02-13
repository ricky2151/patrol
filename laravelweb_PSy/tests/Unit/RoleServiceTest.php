<?php

namespace Test\Unit;

use Tests\Testcase;
use App\Repositories\Contracts\RoleRepositoryContract;

use App\Services\Implementations\RoleServiceImplementation;

use App\TestUtil;

class RoleServiceTest extends TestCase {

    /**
     * test get function
     * @dataProvider getProvider
     * @return void
     */
    public function testGet($roleRepoAllOrder, $expectedResult)
    {
        //1. create mock for roleRepository
        $roleRepoMock = TestUtil::mockClass(RoleRepositoryContract::class, [['method' => 'allOrder', 'returnOrThrow' => $roleRepoAllOrder]]);
        
        //2. make object RoleService for testing
        $roleService = new RoleServiceImplementation($roleRepoMock);
        
        //3. call the function to be tested
        $thereIsException = false;
        try {
            $result = $roleService->get();
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
        $roleRepoAllOrderReturnThreeData = [
            [
                'id' => 1,
                'name' => 'Guard',
            ],
            [
                'id' => 2,
                'name' => 'Admin',
            ],
            [
                'id' => 3,
                'name' => 'Superadmin',
            ],
        ];
        $roleRepoAllOrderReturnEmptyData = [

        ];

        //output
        $expectedResultWithThreeData = $roleRepoAllOrderReturnThreeData;
        $expectedResultEmpty = [];

        //order : 
        //rolerepoallorder
        //expected result
        return [
            '1. when rolerepo.allorder return three data, then return that three data' => [
                $roleRepoAllOrderReturnThreeData, 
                $expectedResultWithThreeData
            ],
            '2. when rolerepo.allorder return empty data, then return that empty data' => [
                $roleRepoAllOrderReturnEmptyData, 
                $expectedResultEmpty
            ],
        ];
    }

    
}