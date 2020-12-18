<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Repositories\Contracts\AuthRepositoryContract;

class AuthServiceTest extends TestCase {

    /**
     * A basic feature test login function.
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
     * A basic feature test me function.
     * @dataProvider meProvider
     * @return void
     */
    public function testMe($authRepoMe, 
    $expectedResult, $verifyAuthRepoMeIsCalled) {

        //1. create mock for AuthRepository
        $authRepoMock = $this->mock(AuthRepositoryContract::class, function ($authRepoMock) use ($authRepoMe){
            if(is_array($authRepoMe))
            {
                $authRepoMock->shouldReceive('login')->andReturn($authRepoMock);
            }
            else
            {
                $authRepoMock->shouldReceive('login')->andThrow($authRepoMock);
            }
        });
        
        //2. make object AuthService for testing
        $authService = new \App\Services\Implementations\AuthServiceImplementation($authRepoMock);

        //3. call the function to be tested
        $thereIsException = false;
        try {
            $result = $authService->me();
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
        if($verifyAuthRepoMeIsCalled)
        {
            $authRepoMock->shouldReceive('me');
        }
        else
        {
            $authRepoMock->shouldNotReceive('me');
        }
        
    }

    public function meProvider() {
        
        $request = ['username' => 'any', 'password' => 'any'];
        $responseAuthRepoLoginSuccess = ['access_token' => 'xxx', 'user' => []];

        $expectedResultSuccess = ['access_token' => 'xxx', 'user' => []];

        $meFailedExceptionWrongCredentials = new \App\Exceptions\LoginFailedException('Wrong Credentials !');
        $meFailedExceptionNotAdmin = new \App\Exceptions\LoginFailedException('You Are Not Admin !');
        $loginFailedErrorServer = new \App\Exceptions\LoginFailedException('There is problem in authentication server !');

        //order : 
        //authrepo.me
        //expectedresult, verifyAuthRepoMeIsCalled

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



}
