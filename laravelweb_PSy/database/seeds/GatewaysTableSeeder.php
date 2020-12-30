<?php

use Illuminate\Database\Seeder;
use App\Models\Gateway;

class GatewaysTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Gateway::class, 10)->create();
    }
}
