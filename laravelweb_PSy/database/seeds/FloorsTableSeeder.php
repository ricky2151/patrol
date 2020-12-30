<?php

use Illuminate\Database\Seeder;
use App\Models\Floor;

class FloorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Floor::class, 5)->create();
        
    }
}
