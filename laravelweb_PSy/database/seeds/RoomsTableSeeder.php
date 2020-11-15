<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class RoomsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('id_ID');
 
    	for($i = 1; $i <= 10; $i++){    		
    		DB::table('rooms')->insert([
    			'name' => "Ruangan " . $faker->name,
    			'floor_id' => mt_rand(1, 5),
    			'building_id' => mt_rand(1, 5), 
                'gateway_id' => mt_rand(1,5),
    		]);
 
    	}
    }
}
