<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use App\Models\Role;

class AuthTest extends TestCase
{
    protected function setUp(): void {
        parent::setUp();
        //1. clear database and migrate 
        Artisan::call('migrate:fresh');

        //2. run roletableseeder to make role data
        Artisan::call('db:seed', [
            '--class' => 'RolesTableSeeder'
        ]);

        //3. run usertableseeder to make user data
        Artisan::call('db:seed', [
            '--class' => 'UsersTableSeeder'
        ]);
        
    }
    
    //global function to run http request login as guard and make fixture
    //so this fixture can be use to test other API that requires login as guard
    public function testLoginAsGuardAndMakeFixture()
    {
        $body = ['username' => 'test_guard', 'password' => 'secret', 'isAdmin' => false];
        $response = $this->postJson('/api/auth/login', $body);
        $response
            ->assertStatus(200)
            ->assertJson([
                "error" => false,
                "authenticate" => true,
            ]);
        return $response;
    }

    //global function to run http request login as admin and make fixture
    //so this fixture can be use to test other API that requires login as admin
    public function testLoginAsAdminAndMakeFixture()
    {
        $body = ['username' => 'test_admin', 'password' => 'secret', 'isAdmin' => true];
        $response = $this->postJson('/api/auth/login', $body);
        $response
            ->assertStatus(200)
            ->assertJson([
                "error" => false,
                "authenticate" => true,
            ]);
        return $response;
    }

    //global function to run http request login as superadmin and make fixture
    //so this fixture can be use to test other API that requires login as superadmin
    public function testLoginAsSuperadminAndMakeFixture()
    {
        $body = ['username' => 'test_superadmin', 'password' => 'secret', 'isAdmin' => true];
        $response = $this->postJson('/api/auth/login', $body);
        $response
            ->assertStatus(200)
            ->assertJson([
                "error" => false,
                "authenticate" => true,
            ]);
        return $response;
    }

    /**
     * feature test login.
     * @dataProvider loginProvider
     * @return void
     */
    public function testLogin($username, $password, $isAdmin, $statusExpected, $jsonExpected)
    {
        $body = ['username' => $username, 'password' => $password];
        if($isAdmin)
        {
            $body['isAdmin'] = $isAdmin;
        }
        $response = $this->postJson('/api/auth/login', $body);

        $response
            ->assertStatus($statusExpected)
            ->assertJson($jsonExpected);
        
    }

    public function loginProvider()
    {
        //account
        $passwordAny = "ThisIsPassword";
        $usernameAny = "ThisIsUsername";

        //error messages
        $usernameEmptyErrorMessage = [
            "error" => true,
            "code" => "E-1001",
            "message" => [
                "username" => [
                    "The username field is required."
                ]
            ]
        ];
        $passwordEmptyErrorMessage = [
            "error" => true,
            "code" => "E-1001",
            "message" => [
                "password" => [
                    "The password field is required."
                ]
            ]
        ];
        $isAdminInvalidErrorMessage = [
            "error" => true,
            "code" => "E-1001",
            "message" => [
                "isAdmin" => [
                    "The is admin field must be true or false."
                ]
            ]
        ];
        $authenticationErrorMessage = [
            "error" => true,
            "code" => "E-0001",
            "message" => ["Wrong Credentials !"]
        ];
        $authorizationAdminErrorMessage = [
            "error" => true,
            "code" => "E-0001",
            "message" => ["You Are Not Admin !"]
        ];
        $loginSuccessMessage = [
            "error" => false,
            "authenticate" => true,
        ];
        //[username, password, isAdmin, status code, message]
        return [
            'when username is undefined, then return correct error' => [null,$passwordAny, null, 422, $usernameEmptyErrorMessage],
            'when username is empty, then return correct error' => ['', $passwordAny, null, 422, $usernameEmptyErrorMessage],
            'when username is invalid, then return correct error' => [$usernameAny, $passwordAny, null, 401, $authenticationErrorMessage],
            'when password is undefined, then return correct error' => [$usernameAny, null, null, 422, $passwordEmptyErrorMessage],
            'when password is empty, then return correct error' => [$usernameAny, '', null, 422, $passwordEmptyErrorMessage],
            'when password is invalid, then return correct error' => ['test_admin', $passwordAny, null, 401, $authenticationErrorMessage],
            'when username and password are valid, then return correct data' => ['test_admin', 'secret', null, 200, $loginSuccessMessage],

            'when isAdmin is invalid, then return correct error' => [$usernameAny, $passwordAny, "hehe", 401, $authenticationErrorMessage],
            'when valid guard login as admin, then return correct error authorization' => ['test_guard', 'secret', true, 401, $authorizationAdminErrorMessage],
            'when valid admin login as admin, then return correct data' => ['test_admin', 'secret', true, 200, $loginSuccessMessage],
            'when valid superadmin login as admin, then return correct data' => ['test_superadmin', 'secret', true, 200, $loginSuccessMessage],
            'when valid guard login as guard, then return correct data' => ['test_guard', 'secret', false, 200, $loginSuccessMessage],
            'when valid admin login as guard, then return correct data' => ['test_admin', 'secret', false, 200, $loginSuccessMessage],
            'when valid superadmin login as guard, then return correct data' => ['test_superadmin', 'secret', false, 200, $loginSuccessMessage],
        ];
    }


    /**
     * feature test isLogin API with valid guard token.
     * @depends testLoginAsGuardAndMakeFixture
     * @return void
     */
    public function testValidGuardTokenIsValid($responseLoginGuard)
    {
        $token = $responseLoginGuard->json()['access_token'];
        $body = ['token' => $token];
        $response = $this->postJson('/api/auth/isLogin', $body);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'user' => [
                    'name',
                ]
            ])
            ->assertJson([
                "error" => false
            ]);
    }

    /**
     * feature test isLogin API with valid admin token.
     * @depends testLoginAsAdminAndMakeFixture
     * @return void
     */
    public function testValidAdminTokenIsValid($responseLoginAdmin)
    {
        $token = $responseLoginAdmin->json()['access_token'];
        $body = ['token' => $token];
        $response = $this->postJson('/api/auth/isLogin', $body);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'user' => [
                    'name',
                ]
            ])
            ->assertJson([
                "error" => false
            ]);
    }

    /**
     * feature test isLogin API with valid superadmin token.
     * @depends testLoginAsSuperadminAndMakeFixture
     * @return void
     */
    public function testValidSuperadminTokenIsValid($responseLoginSuperadmin)
    {
        $token = $responseLoginSuperadmin->json()['access_token'];
        $body = ['token' => $token];
        $response = $this->postJson('/api/auth/isLogin', $body);


        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'user' => [
                    'name',
                ]
            ])
            ->assertJson([
                "error" => false
            ]);
    }


    /**
     * feature test isLogin API with invalid token.
     * @dataProvider isLoginProvider
     * @return void
     */
    public function testIsLogin($token, $statusExpected, $jsonExpected)
    {
        $body = [];
        if($token != null)
        {
            $body['token'] = $token;
        }
        $response = $this->postJson('/api/auth/isLogin', $body);

        //$response->dump();

        $response
            ->assertStatus($statusExpected)
            ->assertJson($jsonExpected);
        
    }

    public function isLoginProvider() {
        //[token, status code, jsonExpected]
        return [
            'when token is undefined, then return correct error' => [null, 400, [
                "error" => true,
                "code" => "E-0002",
                "message" => ["Error in Authentication !"]
            ]],
            'when token is empty, then return correct error' => ["", 400, [
                "error" => true,
                "code" => "E-0002",
                "message" => ["Error in Authentication !"]
            ]],
            'when token is invalid, then return correct error' => ["xxx", 400, [
                "error" => true,
                "code" => "E-0002",
                "message" => ['token' => ["Token is Invalid !"]]
            ]],
        ];
    }


    /**
     * feature test login as guard and access admin data
     * @depends testLoginAsGuardAndMakeFixture
     * @return void
     */
    public function testLoginAsGuardAndAccessAdminData($responseLoginGuard)
    {
        $token = $responseLoginGuard->json()['access_token'];
        $parameter = ['token' => $token];
        $response = $this->json('GET', '/api/admin/shifts', $parameter);


        $response
            ->assertStatus(401)
            ->assertJson([
                "error" => true,
                "message" => ["You Are Not Admin !"]
            ]);
        
    }

    /**
     * feature test login as admin and access admin data
     * @depends testLoginAsAdminAndMakeFixture
     * @return void
     */
    public function testLoginAsAdminAndAccessAdminData($responseLoginAdmin)
    {
        $token = $responseLoginAdmin->json()['access_token'];
        $parameter = ['token' => $token];
        
        $response = $this->json('GET', '/api/admin/shifts', $parameter);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data'
            ])
            ->assertJson([
                "error" => false
            ]);
        
    }

    /**
     * feature test login as superadmin and access admin data
     * @depends testLoginAsSuperadminAndMakeFixture
     * @return void
     */
    public function testLoginAsSuperadminAndAccessAdminData($responseLoginSuperadmin)
    {
        $token = $responseLoginSuperadmin->json()['access_token'];
        $parameter = ['token' => $token];
        $response = $this->json('GET', '/api/admin/shifts', $parameter);

        

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data'
            ])
            ->assertJson([
                "error" => false
            ]);
        
    }

    /**
     * feature test logout API as guard.
     * @depends testLoginAsGuardAndMakeFixture
     * @return void
     */
    public function testLogoutAsGuard($responseLoginGuard)
    {
        $token = $responseLoginGuard->json()['access_token'];
        $body = ['token' => $token];
        $response = $this->postJson('/api/auth/logout', $body);

        $response
            ->assertStatus(200)
            ->assertJson([
                "error" => false,
                'message' => 'Successfully logged out'
            ]);
        
        $this->testIsLogin($token, 400, 
        [
            "error" => true,
            "code" => "E-0002",
            "message" => ['token' => ["Token is Invalid !"]]
        ]);
    }

    /**
     * feature test logout API with invalid token.
     * @dataProvider logoutProvider
     * @return void
     */
    public function testLogout($token, $statusExpected, $jsonExpected)
    {
        $body = [];
        if($token != null)
        {
            $body['token'] = $token;
        }
        $response = $this->postJson('/api/auth/logout', $body);

        //$response->dump();

        $response
            ->assertStatus($statusExpected)
            ->assertJson($jsonExpected);
        
    }

    public function logoutProvider() {
        //[token, status code, jsonExpected]
        return [
            'when token is undefined, then return correct error' => [null, 400, [
                "error" => true,
                "code" => "E-0002",
                "message" => ["Error in Authentication !"]
            ]],
            'when token is empty, then return correct error' => ["", 400, [
                "error" => true,
                "code" => "E-0002",
                "message" => ["Error in Authentication !"]
            ]],
            'when token is invalid, then return correct error' => ["", 400, [
                "error" => true,
                "code" => "E-0002",
                "message" => ["Error in Authentication !"]
            ]],
        ];
    }

    




    
    
    
}
