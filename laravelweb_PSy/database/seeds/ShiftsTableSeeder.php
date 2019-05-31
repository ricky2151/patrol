<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ShiftsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('id_ID');
 
    	for($i = 1; $i <= 50; $i++){    		
    		DB::table('shifts')->insert([
    			'user_id' => mt_rand(1,25),
    			'room_id' => mt_rand(1,10),
    			'time_id' => mt_rand(1,4),
    			'date' => $faker->date($format = 'Y-m-d', $max = 'now'),
    			'status_node_id' => mt_rand(1,3),
    			'message' => 'Aman pak !', 
    			'token_shift' => ' '
    		]);
 
    	}
    }
}
