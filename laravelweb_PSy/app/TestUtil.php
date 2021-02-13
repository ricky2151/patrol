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
                    
                    $times = 1;
                    if($methods[$i]['returnOrThrow'] === null) {
                        $times = 0;
                    } else {
                        if(isset($methods[$i]['times'])) {
                            $times = $methods[$i]['times'];
                        } 
                    }
                    

                    if(!($methods[$i]['returnOrThrow'] instanceof \Exception))
                    {
                        if(isset($methods[$i]['usingParam'])) {
                            $mock->shouldReceive($methods[$i]['method'])->with($methods[$i]['usingParam'])
                            ->andReturn($methods[$i]['returnOrThrow'])
                            ->times($times);
                        }
                        else {
                            $mock->shouldReceive($methods[$i]['method'])
                            ->andReturn($methods[$i]['returnOrThrow'])
                            ->times($times);
                        }
                    }
                    else
                    {
                        if(isset($methods[$i]['usingParam'])) {
                            $mock->shouldReceive($methods[$i]['method'])->with($methods[$i]['usingParam'])
                            ->andThrow($methods[$i]['returnOrThrow'])
                            ->times($times);
                        }
                        else {
                            $mock->shouldReceive($methods[$i]['method'])
                            ->andThrow($methods[$i]['returnOrThrow'])
                            ->times($times);
                        }
                        
                    } 
                }
            }
        });
        return $resultClassMock;
    }

}