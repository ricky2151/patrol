<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\Shift;

class ShiftsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $today = Carbon::today()->format('Y-m-d');
        $todayBeforeThisMonth = Carbon::today()->subDays(30)->format('Y-m-d');

        $shiftAdded = array();
        //random until room & time not already exist, and insert until 20 rows
        while(count($shiftAdded) != 10)
        {
            $temp_rand_room = mt_rand(1,10);
            $temp_rand_time = mt_rand(1,4);
            $temp_rand_user = mt_rand(1,5);
            $added = false;
            for($j = 0;$j<count($shiftAdded);$j++)
            {
                //room_id & time_id not allowed to repeat
                if($shiftAdded[$j][0] == $temp_rand_room && $shiftAdded[$j][1] == $temp_rand_time)
                {
                    $added = true;
                    break;
                }
                else if($shiftAdded[$j][1] == $temp_rand_time && $shiftAdded[$j][2] == $temp_rand_user)
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

                factory(Shift::class, 1)->create([
                    'user_id' => $temp_rand_user,
                    'room_id' => $temp_rand_room,
                    'time_id' => $temp_rand_time,
                ]);
            }
        }
 
    	
    }
}
