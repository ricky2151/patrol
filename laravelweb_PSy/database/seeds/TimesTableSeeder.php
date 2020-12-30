<?php

use Illuminate\Database\Seeder;
use App\Models\Time;

class TimesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		factory(Time::class, 1)->create([
			'start' => '00:00',
			'end' => '06:00'
		]);
        factory(Time::class, 1)->create([
			'start' => '06:00',
			'end' => '10:00'
		]);
		factory(Time::class, 1)->create([
			'start' => '10:00',
			'end' => '14:00'
		]);
		factory(Time::class, 1)->create([
			'start' => '14:00',
			'end' => '18:00'
		]);
		factory(Time::class, 1)->create([
			'start' => '18:00',
			'end' => '22:00'
		]);

    }
}
