<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Repositories\Contracts\AuthRepositoryContract;
use App\Repositories\Implementations\AuthRepositoryImplementation;


class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $mock = $this->mock(AuthRepositoryContract::class, function ($mock) {
            $mock->shouldReceive('login')->andReturn(true);
        });

        $this->assertSame(true, $mock->login("wkwk"));
    }
}
