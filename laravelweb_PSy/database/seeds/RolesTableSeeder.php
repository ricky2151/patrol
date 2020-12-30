<?php

use Illuminate\Database\Seeder;
use App\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		factory(Role::class, 1)->create([
			'name' => 'Guard'
		]);
		factory(Role::class, 1)->create([
			'name' => 'Admin'
		]);
		factory(Role::class, 1)->create([
			'name' => 'Superadmin'
		]);
    }
}
