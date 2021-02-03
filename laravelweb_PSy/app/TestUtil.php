<?php

namespace App;

use Tests\TestCase;
use Mockery;

class TestUtil{
    /**
     * this function is used to mock class
     * @return mockedClass
     */
    public static function mockClass($class, $methods) {
        $resultClassMock = Mockery::mock($class, function ($mock) use ($methods){
            for($i = 0;$i<count($methods);$i++) {
                $mockThisMethod = true;
                if(isset($methods[$i]['mockWhen']) && $methods[$i]['mockWhen'] == false) {
                    $mockThisMethod = false;
                }
                if($mockThisMethod) {
                    if(!($methods[$i]['returnOrThrow'] instanceof \Exception))
                    {
                        if(isset($methods[$i]['usingParam'])) {
                            $mock->shouldReceive($methods[$i]['method'])->with($methods[$i]['usingParam'])->andReturn($methods[$i]['returnOrThrow']);
                        }
                        else {
                            $mock->shouldReceive($methods[$i]['method'])->andReturn($methods[$i]['returnOrThrow']);
                        }
                    }
                    else
                    {
                        if(isset($methods[$i]['usingParam'])) {
                            $mock->shouldReceive($methods[$i]['method'])->with($methods[$i]['usingParam'])->andThrow($methods[$i]['returnOrThrow']);
                        }
                        else {
                            $mock->shouldReceive($methods[$i]['method'])->andThrow($methods[$i]['returnOrThrow']);
                        }
                        
                    } 
                }
            }
        });
        return $resultClassMock;
    }

}