<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        
        //create user with role guard
        factory(User::class, 1)->create([
            'username' => 'test_guard',
            'role_id' => 1
        ]);
        //create user with role admin
        factory(User::class, 1)->create([
            'username' => 'test_admin',
            'role_id' => 2
        ]);
        //create user with role superadmin
        factory(User::class, 1)->create([
            'username' => 'test_superadmin',
            'role_id' => 3
        ]);
        //create 30 user with role guard
        factory(User::class, 30)->create();
    }
}
