<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Repositories\Contracts\AuthRepositoryContract;

class AuthServiceTest extends TestCase {

    /**
     * test login function.
     * @dataProvider loginProvider
     * @return void
     */
    public function testLogin($request, $isAdmin, $authRepoLogin, 
    $authRepoCanMePlayARole, $expectedResult, $verifyAuthRepoCanMePlayARoleIsCalled) {

        //1. create mock for AuthRepository
        $authRepoMock = $this->mock(AuthRepositoryContract::class, function ($authRepoMock) use ($authRepoLogin, $authRepoCanMePlayARole, $verifyAuthRepoCanMePlayARoleIsCalled){
            if(is_array($authRepoLogin))
            {
                $authRepoMock->shouldReceive('login')->andReturn($authRepoLogin);
            }
            else
            {
                $authRepoMock->shouldReceive('login')->andThrow($authRepoLogin);
            }

            if($verifyAuthRepoCanMePlayARoleIsCalled)
            {
                $authRepoMock->shouldReceive('canMePlayARole')->with('Admin')->andReturn($authRepoCanMePlayARole);
            }
            
        });
        
        //2. make object AuthService for testing
        $authService = new \App\Services\Implementations\AuthServiceImplementation($authRepoMock);

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
        
        //5. verify that mocked method is called
        if($verifyAuthRepoCanMePlayARoleIsCalled)
        {
            $authRepoMock->shouldReceive('canMePlayARole');
        }
        else
        {
            $authRepoMock->shouldNotReceive('canMePlayARole');
        }
        
    }

    public function loginProvider() {
        
        $request = ['username' => 'any', 'password' => 'any'];
        $responseAuthRepoLoginSuccess = ['access_token' => 'xxx', 'user' => []];

        $expectedResultSuccess = ['access_token' => 'xxx', 'user' => []];

        $loginFailedExceptionWrongCredentials = new \App\Exceptions\LoginFailedException('Wrong Credentials !');
        $loginFailedExceptionNotAdmin = new \App\Exceptions\LoginFailedException('You Are Not Admin !');
        $loginFailedErrorServer = new \App\Exceptions\LoginFailedException('There is problem in authentication server !');

        //order : 
        //request, isAdmin, authrepo.login, authrepo.canmeplayarole,
        //expectedresult, verifyAuthRepoCanMePlayARoleIsCalled

        return [
            'login with correct admin account' => 
                [
                    $request, true, $responseAuthRepoLoginSuccess, true,
                    $expectedResultSuccess, true
                ],
            'login with incorrect admin role account' => 
                [
                    $request, true, $responseAuthRepoLoginSuccess, false, 
                    $loginFailedExceptionNotAdmin, true
                ],
            'login with incorrect account' => 
                [
                    $request, true, $loginFailedExceptionWrongCredentials, null, 
                    $loginFailedExceptionWrongCredentials, false
                ],
            'there is error in authentication server' => 
                [
                    $request, true, $loginFailedErrorServer, null, 
                    $loginFailedErrorServer, false
                ],
            'login with correct guard account' => 
                [
                    $request, false, $responseAuthRepoLoginSuccess, null, 
                    $expectedResultSuccess, false
                ],
            //'login with incorrect admin credentials account' => ['any', 'any', true, new \App\Exceptions\LoginFailedException("Wrong Credentials !"), null],
        ];
    }


    /**
     * test IsLogin function.
     * @dataProvider IsLoginProvider
     * @return void
     */
    public function testIsLogin($authRepoIsLogin, 
    $expectedResult, $verifyAuthRepoIsLoginIsCalled) {

        //1. create mock for AuthRepository
        $authRepoMock = $this->mock(AuthRepositoryContract::class, function ($authRepoMock) use ($authRepoIsLogin){
            if(is_array($authRepoIsLogin))
            {
                $authRepoMock->shouldReceive('isLogin')->andReturn($authRepoIsLogin);
            }
            else
            {
                $authRepoMock->shouldReceive('isLogin')->andThrow($authRepoIsLogin);
            }
        });
        
        //2. make object AuthService for testing
        $authService = new \App\Services\Implementations\AuthServiceImplementation($authRepoMock);

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
        
        //5. verify that mocked method is called
        if($verifyAuthRepoIsLoginIsCalled)
        {
            $authRepoMock->shouldReceive('isLogin');
        }
        else
        {
            $authRepoMock->shouldNotReceive('isLogin');
        }
        
    }

    public function IsLoginProvider() {
        
        $expectedResultSuccess = ['access_token' => 'xxx', 'user' => []];

        $loginFailedExceptionToken = new \App\Exceptions\LoginFailedException('Token expired !');
        $loginFailedExceptionServer = new \App\Exceptions\LoginFailedException('There is problem in authentication server !');
        

        //order : 
        //authrepo.isLogin
        //expectedresult, verifyAuthRepoIsLoginIsCalled

        return [
            'check status when token is valid' => 
                [
                    $expectedResultSuccess,
                    $expectedResultSuccess, true
                ],
            'check status when token was expired' => 
                [
                    $loginFailedExceptionToken,
                    $loginFailedExceptionToken, true
                ],
            'check status when there is problem in authentication server' => 
                [
                    $loginFailedExceptionServer,
                    $loginFailedExceptionServer, true
                ],
            
        ];
    }


    /**
     * test CanMePlayARoleAsAdmin function.
     * @dataProvider CanMePlayARoleAsAdminProvider
     * @return void
     */
    public function testCanMePlayARoleAsAdmin($authRepoCanMePlayARole, 
    $expectedResult) {

        //1. create mock for AuthRepository
        $authRepoMock = $this->mock(AuthRepositoryContract::class, function ($authRepoMock) use ($authRepoCanMePlayARole){
            $authRepoMock->shouldReceive('canMePlayARole')->andReturn($authRepoCanMePlayARole);
        });
        
        //2. make object AuthService for testing
        $authService = new \App\Services\Implementations\AuthServiceImplementation($authRepoMock);

        //3. call the function to be tested
        $result = $authService->canMePlayARoleAsAdmin();

        //4. assert result just only if there is no exception when calling method 
        $this->assertSame($result, $expectedResult);
        
        //5. verify that mocked method is called
        $authRepoMock->shouldReceive('canMePlayARole');
    }

    public function CanMePlayARoleAsAdminProvider() {
        
        //order : 
        //authrepo.canMePlayArole
        //expectedresult,

        return [
            'test when auth.canmeplayarole return true' => 
                [
                    true,
                    true, 
                ],
            'test when auth.canmeplayarole return false' => 
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
    public function testLogout($authRepoLogout, 
    $expectedResult) {

        //1. create mock for AuthRepository
        $authRepoMock = $this->mock(AuthRepositoryContract::class, function ($authRepoMock) use ($authRepoLogout){
            if(is_bool($authRepoLogout))
            {
                $authRepoMock->shouldReceive('logout')->andReturn($authRepoLogout);
            }
            else
            {
                $authRepoMock->shouldReceive('logout')->andThrow($authRepoLogout);
            }
        });
        
        //2. make object AuthService for testing
        $authService = new \App\Services\Implementations\AuthServiceImplementation($authRepoMock);

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
        
        $tokenInvalidException = new \Tymon\JWTAuth\Exceptions\TokenInvalidException();

        //order : 
        //authrepo.logout
        //expectedresult,

        return [
            'test when auth.logout throw exception TokenInvalidException' => 
                [
                    $tokenInvalidException,
                    $tokenInvalidException, 
                ],
            'test when auth.logout return true' => 
                [
                    true,
                    true, 
                ],
            
        ];
    }

    



}
