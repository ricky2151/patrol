<?php
namespace App\Services\Implementations;

use App\Services\Contracts\IotServiceContract;
use App\Repositories\Contracts\IotRepositoryContract as IotRepo;

class IotServiceImplementation implements IotServiceContract {
    protected $iotRepo;
    public function __construct(IotRepo $iotRepo)
    {
        $this->iotRepo = $iotRepo;
    }

    public function configGateway(){
        $dataRooms = $this->iotRepo->getDataRooms();
        $result = array();
        $information = array();

        //convert json to array like this : 
        // {
        //     "2": "1#5#",
        //     "3": "2#8#",
        //     "1": "3#4#7#10#",
        //     "5": "6#9#"
        // }
        //store to $result variable

        //and convert to json information like this : 
        // {
        //     "GT-2": [
        //         "Ruangan Ifa Astuti",
        //         "Ruangan Cinthia Olivia Handayani S.E."
        //     ],
        //     "GT-3": [
        //         "Ruangan Michelle Usada",
        //         "Ruangan Raharja Rajasa S.Farm"
        //     ]
        // }
        //store to $information variable
        foreach ($dataRooms as $key => $value) {
            //fill result
            $indexStringResult = (string)$value['gateway_id'];
            if(!array_key_exists($indexStringResult, $result))
            {
                $result[$indexStringResult] = "";
            }
            $result[$indexStringResult] = $result[$indexStringResult] . $value['id'] . "#";
            //fill information
            $indexStringInformation = (string)$value['gateway_name'];
            if(!array_key_exists($indexStringInformation, $information))
            {
                $information[$indexStringInformation] = array();
            }
            array_push($information[$indexStringInformation], $value['name']);
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
                    $this->iotRepo->sendToConfigGatewayTopicMqtt($key, $value);
                }
            }
        }

        return [
            "mqtt" => $result,
            "information" => $information
        ];
    }
}