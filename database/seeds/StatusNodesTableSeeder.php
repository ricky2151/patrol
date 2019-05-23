<?php

use Illuminate\Database\Seeder;


class StatusNodesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
           		
		DB::table('status_nodes')->insert([
			'name' => 'Aman'
		]);
		DB::table('status_nodes')->insert([
			'name' => 'Mencurigakan'
		]);
		DB::table('status_nodes')->insert([
			'name' => 'Tidak Aman'
		]);
 
    	
    }
}
