<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Salman\Mqtt\MqttClass\Mqtt;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use App\Models\Shift;

class RealTimeShift extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'RealTimeShift:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Real Time Shift is Running';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //dd("halo masuk");
        $data = Shift::skip(0)->take(3)->get();
        

        $data = $data->map(function ($data) { 
            $data = Arr::add($data, 'room_name', $data['room']['name']);
            $data = Arr::add($data, 'status_node_name', $data['status_node']['name']);
            return Arr::except($data, ['room', 'status_node']);
        });

        $mqtt = new Mqtt();
        //dd(response()->json(['error'=> false, 'data' => $data]));
        
        $output = $mqtt->ConnectAndPublish('shift', $data);
        if ($output === true)
        {
            dd("success");
        }

        dd("failed");

         
        

    }
}
