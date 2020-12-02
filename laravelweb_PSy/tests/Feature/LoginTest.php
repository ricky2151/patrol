<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    /**
     * A basic feature test login.
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
        return [
            'username is undefined' => [null,$passwordAny, null, 422, $usernameEmptyErrorMessage],
            'username is empty' => ['', $passwordAny, null, 422, $usernameEmptyErrorMessage],
            'username is invalid' => [$usernameAny, $passwordAny, null, 401, $authenticationErrorMessage],
            'password is undefined' => [$usernameAny, null, null, 422, $passwordEmptyErrorMessage],
            'password is empty' => [$usernameAny, '', null, 422, $passwordEmptyErrorMessage],
            'password is invalid' => ['test_admin', $passwordAny, null, 401, $authenticationErrorMessage],
            'username and password are valid' => ['test_admin', 'secret', null, 200, $loginSuccessMessage],

            'isAdmin is invalid' => [$usernameAny, $passwordAny, "hehe", 422, $isAdminInvalidErrorMessage],
            'valid guard login as admin' => ['test_guard', 'secret', true, 401, $authorizationAdminErrorMessage],
            'valid admin login as admin' => ['test_admin', 'secret', true, 200, $loginSuccessMessage],
            'valid superadmin login as admin' => ['test_superadmin', 'secret', true, 200, $loginSuccessMessage],
            'valid guard login as guard' => ['test_guard', 'secret', false, 200, $loginSuccessMessage],
            'valid admin login as guard' => ['test_admin', 'secret', false, 200, $loginSuccessMessage],
            'valid superadmin login as guard' => ['test_superadmin', 'secret', false, 200, $loginSuccessMessage],
        ];
    }
    
}
