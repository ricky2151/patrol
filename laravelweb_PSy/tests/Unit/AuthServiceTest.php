<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Repositories\Contracts\AuthRepositoryContract;
use App\Exceptions\LoginFailedException;
use App\Services\Implementations\AuthServiceImplementation;

class AuthServiceTest extends TestCase {

    /**
     * test login function.
     * @dataProvider loginProvider
     * @return void
     */
    public function testLogin($request, $isAdmin, $authRepoLogin, $authRepoCanMePlayARole, $expectedResult, $verifyAuthRepoCanMePlayARoleIsCalled) {

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

        $loginFailedExceptionWrongCredentials = new LoginFailedException('Wrong Credentials !');
        $loginFailedExceptionNotAdmin = new LoginFailedException('You Are Not Admin !');
        $loginFailedErrorServer = new LoginFailedException('There is problem in authentication server !');

        //order : 
        //request, isAdmin, authrepo.login, authrepo.canmeplayarole,
        //expectedresult, verifyAuthRepoCanMePlayARoleIsCalled

        return [
            'when login with correct admin account, then return correct data' => 
                [
                    $request, true, $responseAuthRepoLoginSuccess, true,
                    $expectedResultSuccess, true
                ],
            'when login with incorrect admin role account, then return correct error' => 
                [
                    $request, true, $responseAuthRepoLoginSuccess, false, 
                    $loginFailedExceptionNotAdmin, true
                ],
            'when login with incorrect account, then return correct error' => 
                [
                    $request, true, $loginFailedExceptionWrongCredentials, null, 
                    $loginFailedExceptionWrongCredentials, false
                ],
            'when there is error in authentication server, then return correct error' => 
                [
                    $request, true, $loginFailedErrorServer, null, 
                    $loginFailedErrorServer, false
                ],
            'when login with correct guard account, then return correct data' => 
                [
                    $request, false, $responseAuthRepoLoginSuccess, null, 
                    $expectedResultSuccess, false
                ],
        ];
    }


    /**
     * test IsLogin function.
     * @dataProvider IsLoginProvider
     * @return void
     */
    public function testIsLogin($authRepoIsLogin, $expectedResult, $verifyAuthRepoIsLoginIsCalled) {

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

        $loginFailedExceptionToken = new LoginFailedException('Token expired !');
        $loginFailedExceptionServer = new LoginFailedException('There is problem in authentication server !');
        

        //order : 
        //authrepo.isLogin
        //expectedresult, verifyAuthRepoIsLoginIsCalled

        return [
            'when token is valid, then return correct data' => 
                [
                    $expectedResultSuccess,
                    $expectedResultSuccess, true
                ],
            'when token is expired, then return correct error' => 
                [
                    $loginFailedExceptionToken,
                    $loginFailedExceptionToken, true
                ],
            'when there is error when checking token, then return correct error' => 
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
    public function testCanMePlayARoleAsAdmin($authRepoCanMePlayARole, $expectedResult) {

        //1. create mock for AuthRepository
        $authRepoMock = $this->mock(AuthRepositoryContract::class, function ($authRepoMock) use ($authRepoCanMePlayARole){
            $authRepoMock->shouldReceive('canMePlayARole')->andReturn($authRepoCanMePlayARole);
        });
        
        //2. make object AuthService for testing
        $authService = new AuthServiceImplementation($authRepoMock);

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
            'when canmeplayarole function return true, then return true' => 
                [
                    true,
                    true, 
                ],
                'when canmeplayarole function return false, then return false' => 
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
        
        $tokenInvalidException = new \Tymon\JWTAuth\Exceptions\TokenInvalidException();

        //order : 
        //authrepo.logout
        //expectedresult,

        return [
            'when logout failed, then throw exception TokenInvalidException' => 
                [
                    $tokenInvalidException,
                    $tokenInvalidException, 
                ],
            'when logout success, then return true' => 
                [
                    true,
                    true, 
                ],
            
        ];
    }

    



}
