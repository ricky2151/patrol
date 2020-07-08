<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Faker\Factory as Faker;

class PhotosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        DB::table('photos')->insert([
            'url' => 'www.google1.com',
            'history_id' => 1,
            'photo_time' => '10:10:10',
        ]);
        DB::table('photos')->insert([
            'url' => 'www.google2.com',
            'history_id' => 1,
            'photo_time' => '10:10:10',
        ]);
        DB::table('photos')->insert([
            'url' => 'www.google3.com',
            'history_id' => 1,
            'photo_time' => '10:10:10',
        ]);
    	
    }
}
