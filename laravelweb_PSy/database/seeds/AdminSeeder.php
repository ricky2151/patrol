<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        //1. generate role
        DB::table('roles')->insert([
                'name' => "Guard",
            ]);
        DB::table('roles')->insert([
                'name' => "Admin", 
            ]);
        DB::table('roles')->insert([
                'name' => "Superadmin",
            ]);

        //2. generate user admin
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

        
    }
}
