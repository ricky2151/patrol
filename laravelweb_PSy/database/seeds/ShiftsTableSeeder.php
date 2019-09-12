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

        // for($i = 0;$i < 5;$i++) //disetiap waktu
        // {
        //     for($j = 4;$j<=7;$j++) //disetiap user
        //     {
        //         $today = Carbon::today();
        //         for($k = 1;$k<=3;$k++) //disetiap room
        //         {
                    
        //             $temp = $today->format('Y-m-d');
        //             DB::table('shifts')->insert([
        //                 'user_id' => $j,
        //                 'time_id' => $i,
        //                 'room_id' => $k,
        //                 'date' => $temp,
        //                 'status_node_id' => mt_rand(1,3),
        //                 'message' => 'Aman Pak !',
        //                 'scan_time' => '',
        //             ]);

        //             $today = $today->add(1, 'day');


        //         }

        //     }

        // }

        DB::table('shifts')->insert([
                'user_id' => 1,
                'room_id' => 1,
                'time_id' => 1,
                'date' => $today,
                'status_node_id' => mt_rand(1,3),
                'message' => 'Aman pak !', 
                'scan_time' => '',
            ]);

        DB::table('shifts')->insert([
                'user_id' => 1,
                'room_id' => 2,
                'time_id' => 1,
                'date' => $today,
                'status_node_id' => mt_rand(1,3),
                'message' => 'Sepertinya ada pencuri !', 
                'scan_time' => '',
            ]);

        DB::table('shifts')->insert([
                'user_id' => 1,
                'room_id' => 2,
                'time_id' => 3,
                'date' => $today,
                'status_node_id' => mt_rand(1,3),
                'message' => 'Mencurigakan pak !', 
                'scan_time' => '',
            ]);


    	for($i = 1; $i <= 50; $i++){    		
    		DB::table('shifts')->insert([
    			'user_id' => mt_rand(1,25),
    			'room_id' => mt_rand(1,10),
    			'time_id' => mt_rand(1,4),
    			'date' => $faker->date($format = 'Y-m-d', $max = 'now'),
    			'status_node_id' => mt_rand(1,3),
    			'message' => 'Aman pak !', 
    			'scan_time' => '',
    		]);
 
    	}
    }
}
