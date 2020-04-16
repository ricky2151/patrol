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
        $todayBeforeThisMonth = Carbon::today()->subDays(30)->format('Y-m-d');

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
            ]);

        DB::table('shifts')->insert([
                'user_id' => 1,
                'room_id' => 2,
                'time_id' => 1,
                'date' => $today,
            ]);

        DB::table('shifts')->insert([
                'user_id' => 1,
                'room_id' => 2,
                'time_id' => 3,
                'date' => $today,
            ]);
        DB::table('shifts')->insert([
                'user_id' => 1,
                'room_id' => 1,
                'time_id' => 1,
                'date' => $todayBeforeThisMonth,
            ]);

        DB::table('shifts')->insert([
                'user_id' => 1,
                'room_id' => 2,
                'time_id' => 1,
                'date' => $todayBeforeThisMonth, 
            ]);

        DB::table('shifts')->insert([
                'user_id' => 1,
                'room_id' => 2,
                'time_id' => 3,
                'date' => $todayBeforeThisMonth,
            ]);

        $shiftAdded = array();
        //random until room & time not already exist, and insert until 20 rows
        while(count($shiftAdded) != 60)
        {
            $temp_rand_room = mt_rand(1,10);
            $temp_rand_time = mt_rand(1,4);
            $temp_rand_user = mt_rand(1,5);
            $added = false;
            for($j = 0;$j<count($shiftAdded);$j++)
            {
                if($shiftAdded[0] == $temp_rand_room && $shiftAdded[1] == $temp_rand_time && $shiftAdded[2] == $temp_rand_user)
                {
                    $added = true;
                    break;
                }
            }
            if($added == false)
            {
                $roomTimeUserArray = array();
                array_push($roomTimeUserArray, $temp_rand_room);
                array_push($roomTimeUserArray, $temp_rand_time);
                array_push($roomTimeUserArray, $temp_rand_user);
                array_push($shiftAdded,$roomTimeUserArray);

                DB::table('shifts')->insert([
                    'user_id' => $temp_rand_user,
                    'room_id' => $temp_rand_room,
                    'time_id' => $temp_rand_time,
                    //'date' => $faker->date($format = 'Y-m-d', $max = 'now'),
                    'date' => $today,
                ]);
            }
        }
            
            
            

    		
            
            
 
    	
    }
}
