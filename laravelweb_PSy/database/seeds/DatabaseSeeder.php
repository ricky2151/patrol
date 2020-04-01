<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(BuildingsTableSeeder::class);
        $this->call(FloorsTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(RoomsTableSeeder::class);
        $this->call(PhotosTableSeeder::class);
        $this->call(ShiftsTableSeeder::class);
        $this->call(StatusNodesTableSeeder::class);
        $this->call(TimesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(GatewaysTableSeeder::class);
        $this->call(HistoriesTableSeeder::class);
    }
}
