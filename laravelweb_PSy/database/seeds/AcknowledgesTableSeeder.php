<?php

use Illuminate\Database\Seeder;

class AcknowledgesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('acknowledges')->insert([
			'room_id' => '1',
            'sent' => true,
            'time' => 1023
		]);
        DB::table('acknowledges')->insert([
			'room_id' => '1',
            'sent' => true,
            'time' => 1099
		]);
		DB::table('acknowledges')->insert([
			'room_id' => '2',
            'sent' => true,
            'time' => 2001
		]);
		DB::table('acknowledges')->insert([
			'room_id' => '2',
            'sent' => true,
            'time' => 2020
		]);
		DB::table('acknowledges')->insert([
			'room_id' => '3',
            'sent' => true,
            'time' => 1023
		]);
    }
}
