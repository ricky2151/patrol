<?php

namespace App\Repositories\Implementations;

use App\Repositories\Contracts\IotRepositoryContract;
use App\Models\Room;
use Illuminate\Support\Arr;
use App\Exceptions\MqttFailedException;
use Salman\Mqtt\MqttClass\Mqtt;

class IotRepositoryImplementation implements IotRepositoryContract  {
    public function getDataRooms()
    {
        $dataRooms = Room::get();
        $dataRooms = $dataRooms->map(function ($data) { 
            $data = Arr::add($data, 'floor_name', $data['floor']['name']);
            $data = Arr::add($data, 'building_name', $data['building']['name']);
            $data = Arr::add($data, 'gateway_name', $data['gateway']['name']);
            return Arr::except($data, ['floor', 'building', 'gateway']);
        });
        $dataRooms = $dataRooms->toArray();
        return $dataRooms;
    }

    public function sendToConfigGatewayTopicMqtt($gatewayId, $stringListNodeId)
    {
        $idUser = auth()->user()->id;
        $mqtt = new Mqtt();

        //idUser itu cuman client_id
        //sebenernya aku ga tau client_id ini buat apa, karena gak ada hubunganya sama data ynag dikirim
        //https://github.com/salmanzafar949/MQTT-Laravel#publishing-topic
        //maka dari itu sementara kita pake id dari user yang login dulu aja

        $output = $mqtt->ConnectAndPublish("config-gateway/" . $gatewayId, $stringListNodeId, $idUser);
        if ($output === false)
        {
            throw new MqttFailedException("cannot send data to topic 'config-gateway'");
        }
        return true;
    }
}