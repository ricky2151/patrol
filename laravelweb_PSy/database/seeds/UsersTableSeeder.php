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
        //make random string
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 16; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        //======
        //tester
        DB::table('users')->insert([
                'name' => 'Guard',
                'age' => 20,
                'role_id' => 1,
                'username' => 'test_guard',
                'password' => bcrypt('secret'),
                'phone' => '085727322755',
                'email' => 'samuel.ricky@ti.ukdw.ac.id', 
                'master_key' => $randomString,
            ]);

        $randomString = '';
        for ($i = 0; $i < 16; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        DB::table('users')->insert([
                'name' => 'Admin',
                'age' => 20,
                'role_id' => 2,
                'username' => 'test_admin',
                'password' => bcrypt('secret'),
                'phone' => '085727322755',
                'email' => 'samuel.ricky@ti.ukdw.ac.id', 
                'master_key' => $randomString,
            ]);

        $randomString = '';
        for ($i = 0; $i < 16; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        DB::table('users')->insert([
                'name' => 'Super Admin',
                'age' => 20,
                'role_id' => 3,
                'username' => 'test_superadmin',
                'password' => bcrypt('secret'),
                'phone' => '085727322755',
                'email' => 'samuel.ricky@ti.ukdw.ac.id', 
                'master_key' => $randomString,
            ]);

        //random generate
        $faker = Faker::create('id_ID');
        
    	for($i = 1; $i <= 25; $i++){    	
            $randomString = '';
            for ($j = 0; $j < 16; $j++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }

    		DB::table('users')->insert([
    			'name' => $faker->name,
    			'age' => mt_rand(30,60),
    			'role_id' => mt_rand(1,3),
    			'username' => $faker->userName,
    			'password' => bcrypt('secret'),
    			'phone' => $faker->PhoneNumber,
    			'email' => $faker->email, 
    			'master_key' => $randomString
    		]);
 
    	}
    }
}
