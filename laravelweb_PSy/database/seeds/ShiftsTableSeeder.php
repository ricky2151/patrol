<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
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
        $today = Carbon::today()->format('Y-m-d');

        DB::table('shifts')->insert([
                'user_id' => 1,
                'room_id' => 1,
                'time_id' => 1,
                'date' => $today,
                'status_node_id' => mt_rand(1,3),
                'message' => 'Aman pak !', 
                'token_shift' => ' '
            ]);

        DB::table('shifts')->insert([
                'user_id' => 1,
                'room_id' => 2,
                'time_id' => 1,
                'date' => $today,
                'status_node_id' => mt_rand(1,3),
                'message' => 'Sepertinya ada pencuri !', 
                'token_shift' => ' '
            ]);

        DB::table('shifts')->insert([
                'user_id' => 1,
                'room_id' => 2,
                'time_id' => 3,
                'date' => $today,
                'status_node_id' => mt_rand(1,3),
                'message' => 'Mencurigakan pak !', 
                'token_shift' => ' '
            ]);


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
