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

    /**
     * feature test login.
     * @dataProvider loginProvider
     * @return void
     */
    public function testLogin($username, $password, $isAdmin, $statusExpected, $jsonExpected)
    {
        //1. make body request
        $body = ['username' => $username, 'password' => $password];
        if($isAdmin)
        {
            $body['isAdmin'] = $isAdmin;
        }

        //2. hit API
        $response = $this->postJson('/api/auth/login', $body);

        //3. assert response
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
            "code" => "E-0031",
            "message" => [
                "username" => [
                    "The username field is required."
                ]
            ]
        ];
        $passwordEmptyErrorMessage = [
            "error" => true,
            "code" => "E-0031",
            "message" => [
                "password" => [
                    "The password field is required."
                ]
            ]
        ];
        $isAdminInvalidErrorMessage = [
            "error" => true,
            "code" => "E-0031",
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
        //[username, password, isAdmin, status code, response message]
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
     * @return void
     */
    public function testValidGuardTokenIsValid()
    {
        //1. find user to use as an actor
        $user = User::where('username', 'test_guard')->firstOrFail();

        //2. hit API
        $response = $this->actingAs($user)->postJson('/api/auth/isLogin');

        //3. assert response
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
     * @return void
     */
    public function testValidAdminTokenIsValid()
    {
        //1. find user to use as an actor
        $user = User::where('username', 'test_admin')->firstOrFail();

        //2. hit API
        $response = $this->actingAs($user)->postJson('/api/auth/isLogin');

        //3. assert response
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
     * @return void
     */
    public function testValidSuperadminTokenIsValid()
    {
        //1. find user to use as an actor
        $user = User::where('username', 'test_superadmin')->firstOrFail();

        //2. hit API
        $response = $this->actingAs($user)->postJson('/api/auth/isLogin');

        //3. assert response
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
     * so, this test is not use "actingAs" method, because it test the given token from user
     * @dataProvider isLoginProvider
     * @return void
     */
    public function testIsLogin($token, $statusExpected, $jsonExpected)
    {
        //1. make body request
        $body = [];
        if($token != null)
        {
            $body['token'] = $token;
        }

        //2. hit API
        $response = $this->postJson('/api/auth/isLogin', $body);

        //3. assert response
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
     * feature test login as guard and hit admin page api
     * @return void
     */
    public function testLoginAsGuardAndHitAdminPageApi()
    {
        //1. find user to use as an actor
        $user = User::where('username', 'test_guard')->firstOrFail();

        //2. hit API
        $response = $this->actingAs($user)->json('GET', '/api/admin/shifts');

        //3. assert response
        $response
            ->assertStatus(401)
            ->assertJson([
                "error" => true,
                "message" => ["You Are Not Admin !"]
            ]);
        
    }

    /**
     * feature test login as admin and hit admin page api
     * @return void
     */
    public function testLoginAsAdminAndHitAdminPageApi()
    {
        //1. find user to use as an actor
        $user = User::where('username', 'test_admin')->firstOrFail();

        //2. hit API
        $response = $this->actingAs($user)->json('GET', '/api/admin/shifts');

        //3. assert response
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
     * feature test login as superadmin and hit admin page api
     * @return void
     */
    public function testLoginAsSuperadminAndHitAdminPageApi()
    {
        //1. find user to use as an actor
        $user = User::where('username', 'test_superadmin')->firstOrFail();

        //2. hit API
        $response = $this->actingAs($user)->json('GET', '/api/admin/shifts');

        //3. assert response
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
     * feature test login as guard and hit android api
     * @return void
     */
    public function testLoginAsGuardAndHitAndroidApi()
    {
        //1. find user to use as an actor
        $user = User::where('username', 'test_guard')->firstOrFail();

        //2. hit API
        $response = $this->actingAs($user)->json('GET', '/api/guard/users/shifts');

        //3. assert response
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
     * feature test login as admin and hit android api
     * @return void
     */
    public function testLoginAsAdminAndHitAndroidApi()
    {
        //1. find user to use as an actor
        $user = User::where('username', 'test_admin')->firstOrFail();

        //2. hit API
        $response = $this->actingAs($user)->json('GET', '/api/guard/users/shifts');

        //3. assert response
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
     * feature test login as superadmin and hit android api
     * @return void
     */
    public function testLoginAsSuperadminAndHitAndroidApi()
    {
        //1. find user to use as an actor
        $user = User::where('username', 'test_superadmin')->firstOrFail();

        //2. hit API
        $response = $this->actingAs($user)->json('GET', '/api/guard/users/shifts');

        //3. assert response
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data'
            ])
            ->assertJson([
                "error" => false
            ]);
        
    }

    

    //function to run http request login as guard and make fixture
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

    /**
     * feature test logout API as guard.
     * This test is not use method "actingAs", because we need token to test it again with login API
     * @depends testLoginAsGuardAndMakeFixture
     * @return void
     */
    public function testLogoutAsGuard($responseLoginGuard)
    {
        //1. make body request
        $token = $responseLoginGuard->json()['access_token'];
        $body = ['token' => $token];

        //2. hit API
        $response = $this->postJson('/api/auth/logout', $body);

        //3. assert response
        $response
            ->assertStatus(200)
            ->assertJson([
                "error" => false,
                'message' => 'Successfully logged out'
            ]);
        
        //4. test again with login API 
        $this->testIsLogin($token, 400, 
        [
            "error" => true,
            "code" => "E-0002",
            "message" => ['token' => ["Token is Invalid !"]]
        ]);
    }

    /**
     * feature test logout API with invalid token.
     * this test is not use "actingAs" method, because we test given token from user.
     * @dataProvider logoutProvider
     * @return void
     */
    public function testLogout($token, $statusExpected, $jsonExpected)
    {
        //1. make body request
        $body = [];
        if($token != null)
        {
            $body['token'] = $token;
        }

        //2. hit API
        $response = $this->postJson('/api/auth/logout', $body);

        //3. assert response
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
