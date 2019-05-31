<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //tester
        DB::table('users')->insert([
                'name' => 'Samuel Ricky Saputro',
                'age' => 20,
                'role_id' => 1,
                'username' => 'ricky2151',
                'password' => bcrypt('secret'),
                'phone' => '085727322755',
                'email' => 'samuel.ricky@ti.ukdw.ac.id', 
                'master_key' => ' '
            ]);

        //random generate
        $faker = Faker::create('id_ID');
    
    	for($i = 1; $i <= 25; $i++){    		
    		DB::table('users')->insert([
    			'name' => $faker->name,
    			'age' => mt_rand(30,60),
    			'role_id' => mt_rand(1,3),
    			'username' => $faker->userName,
    			'password' => bcrypt('secret'),
    			'phone' => $faker->PhoneNumber,
    			'email' => $faker->email, 
    			'master_key' => ' '
    		]);
 
    	}
    }
}
