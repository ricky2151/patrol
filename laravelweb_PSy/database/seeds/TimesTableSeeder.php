<?php

use Illuminate\Database\Seeder;

class TimesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('times')->insert([
			'start' => '00:00:00',
			'end' => '06:00:00'
		]);
        DB::table('times')->insert([
			'start' => '06:00:00',
			'end' => '10:00:00'
		]);
		DB::table('times')->insert([
			'start' => '10:00:00',
			'end' => '14:00:00'
		]);
		DB::table('times')->insert([
			'start' => '14:00:00',
			'end' => '18:00:00'
		]);
		DB::table('times')->insert([
			'start' => '18:00:00',
			'end' => '22:00:00'
		]);

    }
}
