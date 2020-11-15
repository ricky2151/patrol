<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Salman\Mqtt\MqttClass\Mqtt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Carbon\Carbon;
use App\Models\Room;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class IotController extends Controller
{
    public function runPython()
    {
        //masih belum bisa ndeteksi error saat jalanin python 
        // $pool = Pool::create();
        // $pool->add(function () use ($thing) {
        //     $command = escapeshellcmd('start /MIN "" py storage/python/dbintegrate.py > NUL 2> NUL');
        //     $output = exec($command);
        // })->then(function ($output) {
        //     // Handle success
        // })->catch(function (Throwable $exception) {
        //     // Handle exception
        // });
        // return response()->json(['error' => false, 'message'=>"python running"]);

        //=== coba windows ===
        // try {
        //     $command = escapeshellcmd('start py storage/python/dbintegrate.py');
        //     //$output = exec($command);
        //     pclose(popen("start /B ". $command, "r")); 
        //     return response()->json(['error' => false, 'message'=>"python running"]);
        // } catch (\Throwable $th) {
        //     return response()->json(['error' => true, 'message'=>"python not running"]);
        // }
        
        //=== coba linux ===
        try {
            $commandKillPython = escapeshellcmd('killall -9 python');
            $commandRunPython = escapeshellcmd('gnome-terminal -e \'python storage/python/dbintegrate.py 2>&1 &\' --title=RickyPython');
            //passthru('python storage/python/dbintegrate.py 2>&1 &');process
            //$res;
            shell_exec($commandKillPython);
            shell_exec($commandRunPython);
            //return $res;
            
            //echo $output;
            return response()->json(['error' => false, 'message'=>"python running"]);
        } catch (\Throwable $th) {
            return response()->json(['error' => true, 'message'=>"python not running"]);
        }
        
        
        //     dd("z");
        // $process = new Process(['py', 'storage/python/jalankanpython.py']);
        // $process->run();

        // // executes after the command finishes
        // if (!$process->isSuccessful()) {
        //     //return "false";
        //     throw new ProcessFailedException($process);
        // }
        // else{
        //     return "true";
        // }
    }


    public function configGateway()
    {
        
        $idUser = Auth::user()->id; //Ga tau ini buat apa
        $mqtt = new Mqtt();

        //get data
        $dataRooms = Room::get();
        $dataRooms = $dataRooms->map(function ($data) { 
            $data = Arr::add($data, 'floor_name', $data['floor']['name']);
            $data = Arr::add($data, 'building_name', $data['building']['name']);
            $data = Arr::add($data, 'gateway_name', $data['gateway']['name']);
            return Arr::except($data, ['floor', 'building', 'gateway']);
        });
        
        $result = array();
        $information = array();

        //convert json to array like this : 
        // {
        //     "2": "1#5#",
        //     "3": "2#8#",
        //     "1": "3#4#7#10#",
        //     "5": "6#9#"
        // }
        //and convert to json information like this : 
        // {
        //     "GT-2": [
        //         "Ruangan Ifa Astuti",
        //         "Ruangan Cinthia Olivia Handayani S.E."
        //     ],
        //     "GT-3": [
        //         "Ruangan Michelle Usada",
        //         "Ruangan Raharja Rajasa S.Farm"
        //     ],
        //     "GT-1": [
        //         "Ruangan Kenari Ardianto S.Psi",
        //         "Ruangan Wardaya Siregar",
        //         "Ruangan Farhunnisa Tari Hastuti",
        //         "Ruangan Oliva Gina Puspita S.IP"
        //     ],
        //     "GT-5": [
        //         "Ruangan Gantar Vino Pranowo M.Ak",
        //         "Ruangan Restu Suartini"
        //     ]
        // }
        foreach ($dataRooms as $key => $value) {
            //fill result
            $indexStringResult = (string)$value->gateway_id;
            if(!array_key_exists($indexStringResult, $result))
            {
                $result[$indexStringResult] = "";
            }
            $result[$indexStringResult] = $result[$indexStringResult] . $value->id . "#";
            //fill information
            $indexStringInformation = (string)$value->gateway_name;
            if(!array_key_exists($indexStringInformation, $information))
            {
                $information[$indexStringInformation] = array();
            }
            array_push($information[$indexStringInformation], $value->name);
        }
        
        //modify result variable
        //remove hashtag on last character and send to mqtt
        foreach ($result as $key => $value) {
            $length = strlen($value);
            if ($length != 0) {
                if (substr($value, -1) == '#') {
                    //remove hashtag
                    $value = substr($value, 0, $length - 1);
                    $result[$key] = $value;

                    //send to mqtt
                    $output = $mqtt->ConnectAndPublish("config-gateway/" . $key, $value, $idUser);
                    if ($output === false)
                    {
                        return response()->json(['error' => true, 'message'=>$output]);
                    }
                    

                }
            }
        }
        $dataResult = $result;
        $dataInformation = $information;
        return response()->json(['error' => false, 'mqtt'=>$dataResult, 'information'=>$dataInformation]);
        

        

        
        

        
    }
}
