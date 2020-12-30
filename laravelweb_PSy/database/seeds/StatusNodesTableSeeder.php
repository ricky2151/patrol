<?php

use Illuminate\Database\Seeder;
use App\Models\StatusNode;


class StatusNodesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
           		
		factory(StatusNode::class, 1)->create([
			'name' => 'Aman'
		]);
		factory(StatusNode::class, 1)->create([
			'name' => 'Mencurigakan'
		]);
		factory(StatusNode::class, 1)->create([
			'name' => 'Tidak Aman'
		]);
 
    	
    }
}
