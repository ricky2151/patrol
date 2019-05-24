<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class BuildingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('id_ID');
 
    	for($i = 1; $i <= 5; $i++){    		 
    		DB::table('buildings')->insert([
    			'name' => "Gedung " . $faker->name,
    		]);
 
    	}
    }
}
