<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Faker\Factory as Faker;

class HistoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('histories')->insert([
            'shift_id' => 1,
            'status_node_id' => 1,
            'message' => 'Aman pak !',
            'scan_time' => '05:00:00',
        ]);

    DB::table('histories')->insert([
            'shift_id' => 1,
            'status_node_id' => 2,
            'message' => 'Waduh Mencurigakan !',
            'scan_time' => '05:30:00',
        ]);

    DB::table('histories')->insert([
            'shift_id' => 1,
            'status_node_id' => 3,
            'message' => 'Ada maling !!',
            'scan_time' => '05:45:00',
        ]);
    DB::table('histories')->insert([
            'shift_id' => 2,
            'status_node_id' => 1,
            'message' => 'Aman pak !',
            'scan_time' => '05:00:00',
        ]);

    DB::table('histories')->insert([
            'shift_id' => 2,
            'status_node_id' => 2,
            'message' => 'Waduh Mencurigakan !',
            'scan_time' => '05:30:00', 
        ]);

    DB::table('histories')->insert([
            'shift_id' => 2,
            'status_node_id' => 3,
            'message' => 'Ada maling !!',
            'scan_time' => '05:45:00',
        ]);
    }
}
