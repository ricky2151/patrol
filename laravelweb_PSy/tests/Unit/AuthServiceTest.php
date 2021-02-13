<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Repositories\Contracts\AuthRepositoryContract;
use App\Exceptions\LoginFailedException;
use App\Services\Implementations\AuthServiceImplementation;
use App\TestUtil;

class AuthServiceTest extends TestCase {


    /**
     * test login function.
     * @dataProvider loginProvider
     * @return void
     */
    public function testLogin($request, $isAdmin, $authRepoLogin, $authRepoCanMePlayARole, $expectedResult) {

        //1. create mock for AuthRepository
        $authRepoMock = TestUtil::mockClass(AuthRepositoryContract::class, [
            ['method' => 'login', 'returnOrThrow' => $authRepoLogin],
            ['method' => 'canMePlayARole', 'usingParam' => 'Admin', 'returnOrThrow' => $authRepoCanMePlayARole, 'mockWhen' => $authRepoCanMePlayARole !== null]
        ]);
        
        
        //2. make object AuthService for testing
        $authService = new AuthServiceImplementation($authRepoMock);

        //3. call the function to be tested
        $thereIsException = false;
        try {
            $request['isAdmin'] = $isAdmin;
            $result = $authService->login($request);
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

    public function loginProvider() {
        //input variable
        $request = ['username' => 'any', 'password' => 'any'];
        $responseAuthRepoLoginSuccess = ['access_token' => 'xxx', 'user' => []];

        //input & expected output variable
        $loginFailedException = new LoginFailedException();

        //expected output variable
        $expectedResultSuccess = ['access_token' => 'xxx', 'user' => []];
        $loginFailedExceptionNotAdmin = new LoginFailedException('You Are Not Admin !');

        //order : 
        //request, isAdmin, authrepo.login, authrepo.canmeplayarole,
        //expectedresult

        return [
            '1. when login as admin is success, then return correct data' => 
                [
                    $request, true, $responseAuthRepoLoginSuccess, true,
                    $expectedResultSuccess
                ],
            '2. when login as admin failed to authorized, then return correct error' => 
                [
                    $request, true, $responseAuthRepoLoginSuccess, false, 
                    $loginFailedExceptionNotAdmin
                ],
            '3. when login as guard is success, then return correct data' => 
                [
                    $request, false, $responseAuthRepoLoginSuccess, null, 
                    $expectedResultSuccess
                ],
        ];
    }


    /**
     * test IsLogin function.
     * @dataProvider IsLoginProvider
     * @return void
     */
    public function testIsLogin($authRepoIsLogin, $expectedResult) {

        //1. create mock for AuthRepository
        $authRepoMock = TestUtil::mockClass(AuthRepositoryContract::class, [['method' => 'isLogin', 'returnOrThrow' => $authRepoIsLogin]]);
        
        //2. make object AuthService for testing
        $authService = new AuthServiceImplementation($authRepoMock);

        //3. call the function to be tested
        $thereIsException = false;
        try {
            $result = $authService->isLogin();
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

    public function IsLoginProvider() {
        
        //input variable
        $expectedResultSuccess = ['access_token' => 'xxx', 'user' => []];

        //order : 
        //authrepo.isLogin
        //expectedresult,

        return [
            '1. when token is valid, then return correct data' => 
                [
                    $expectedResultSuccess,
                    $expectedResultSuccess,
                ],
        ];
    }


    /**
     * test CanMePlayARoleAsAdmin function.
     * @dataProvider CanMePlayARoleAsAdminProvider
     * @return void
     */
    public function testCanMePlayARoleAsAdmin($authRepoCanMePlayARole, $expectedResult) {

        //1. create mock for AuthRepository
        $authRepoMock = TestUtil::mockClass(AuthRepositoryContract::class, [['method' => 'canMePlayARole', 'returnOrThrow' => $authRepoCanMePlayARole]]);
        
        //2. make object AuthService for testing
        $authService = new AuthServiceImplementation($authRepoMock);

        //3. call the function to be tested
        $result = $authService->canMePlayARoleAsAdmin();

        //4. assert result just only if there is no exception when calling method 
        $this->assertSame($result, $expectedResult);
    }

    public function CanMePlayARoleAsAdminProvider() {
        
        //order : 
        //authrepo.canMePlayArole
        //expectedresult,

        return [
            '1. when canmeplayarole function return true, then return true' => 
                [
                    true,
                    true, 
                ],
            '2. when canmeplayarole function return false, then return false' => 
            [
                false,
                false, 
            ],
            
        ];
    }


    /**
     * test CanMePlayARoleAsGuard function.
     * @dataProvider CanMePlayARoleAsGuardProvider
     * @return void
     */
    public function testCanMePlayARoleAsGuard($authRepoCanMePlayARole, $expectedResult) {

        //1. create mock for AuthRepository
        $authRepoMock = TestUtil::mockClass(AuthRepositoryContract::class, [['method' => 'canMePlayARole', 'returnOrThrow' => $authRepoCanMePlayARole]]);
        
        //2. make object AuthService for testing
        $authService = new AuthServiceImplementation($authRepoMock);

        //3. call the function to be tested
        $result = $authService->canMePlayARoleAsGuard();

        //4. assert result just only if there is no exception when calling method 
        $this->assertSame($result, $expectedResult);
        
        //5. verify that mocked method is called
        $authRepoMock->shouldReceive('canMePlayARole');
    }

    public function CanMePlayARoleAsGuardProvider() {
        
        //order : 
        //authrepo.canMePlayArole
        //expectedresult,

        return [
            '1. when canmeplayarole function return true, then return true' => 
                [
                    true,
                    true, 
                ],
            '2. when canmeplayarole function return false, then return false' => 
            [
                false,
                false, 
            ],
            
        ];
    }

    /**
     * test CanMePlayARoleAsSuperAdmin function.
     * @dataProvider CanMePlayARoleAsSuperAdminProvider
     * @return void
     */
    public function testCanMePlayARoleAsSuperAdmin($authRepoCanMePlayARole, $expectedResult) {

        //1. create mock for AuthRepository
        $authRepoMock = TestUtil::mockClass(AuthRepositoryContract::class, [['method' => 'canMePlayARole', 'returnOrThrow' => $authRepoCanMePlayARole]]);
        
        //2. make object AuthService for testing
        $authService = new AuthServiceImplementation($authRepoMock);

        //3. call the function to be tested
        $result = $authService->canMePlayARoleAsSuperAdmin();

        //4. assert result just only if there is no exception when calling method 
        $this->assertSame($result, $expectedResult);
        
        //5. verify that mocked method is called
        $authRepoMock->shouldReceive('canMePlayARole');
    }

    public function CanMePlayARoleAsSuperAdminProvider() {
        
        //order : 
        //authrepo.canMePlayArole
        //expectedresult,

        return [
            '1. when canmeplayarole function return true, then return true' => 
                [
                    true,
                    true, 
                ],
            '2. when canmeplayarole function return false, then return false' => 
            [
                false,
                false, 
            ],
            
        ];
    }

    /**
     * test Logout function.
     * @dataProvider LogoutProvider
     * @return void
     */
    public function testLogout($authRepoLogout, $expectedResult) {

        //1. create mock for AuthRepository
        $authRepoMock = TestUtil::mockClass(AuthRepositoryContract::class, [['method' => 'logout', 'returnOrThrow' => $authRepoLogout]]);
        
        //2. make object AuthService for testing
        $authService = new AuthServiceImplementation($authRepoMock);

        //3. call the function to be tested
        $thereIsException = false;
        try {
            $result = $authService->logout();
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
        $authRepoMock->shouldReceive('logout');
        
    }

    public function LogoutProvider() {
        //order : 
        //authrepo.logout
        //expectedresult,

        return [
            '1. when logout success, then return true' => 
            [
                true,
                true, 
            ],
        ];
    }

    



}
