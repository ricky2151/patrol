<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\Photo;

class PhotosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Photo::class, 3)->create();
    }
}
