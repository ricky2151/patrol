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
    public function testLogin($username, $password, $statusExpected, $jsonExpected)
    {
        $response = $this->postJson('/api/auth/login', ['username' => $username, 'password' => $password]);

        $response
            ->assertStatus($statusExpected)
            ->assertJson($jsonExpected);
    }

    public function loginProvider()
    {
        $passwordAny = "ThisIsPassword";
        $usernameAny = "ThisIsUsername";
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
        $loginFailedErrorMessage = [
            "error" => true,
            "code" => "E-0001",
            "message" => ["Wrong Credentials !"]
        ];
        $loginSuccessMessage = [
            "error" => false,
            "authenticate" => true,
        ];
        return [
            'username is undefined' => [null,$passwordAny, 422, $usernameEmptyErrorMessage],
            'username is empty' => ['', $passwordAny, 422, $usernameEmptyErrorMessage],
            'username is invalid' => [$usernameAny, $passwordAny, 401, $loginFailedErrorMessage],
            'password is undefined' => [$usernameAny, null, 422, $passwordEmptyErrorMessage],
            'password is empty' => [$usernameAny, '', 422, $passwordEmptyErrorMessage],
            'password is invalid' => ['test_admin', $passwordAny, 401, $loginFailedErrorMessage],
            'username and password are valid' => ['test_admin', 'secret', 200, $loginSuccessMessage],
        ];
    }
    
}
