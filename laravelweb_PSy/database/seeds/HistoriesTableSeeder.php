<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\History;

class HistoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(History::class, 3)->create([
            'shift_id' => 1
        ]);
        factory(History::class, 30)->create();
    }
}
