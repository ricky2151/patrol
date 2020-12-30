<?php

namespace App\Repositories\Contracts;

interface IotRepositoryContract {
    public function getDataRooms();

    public function sendToConfigGatewayTopicMqtt($gatewayId, $stringListNodeId);
}