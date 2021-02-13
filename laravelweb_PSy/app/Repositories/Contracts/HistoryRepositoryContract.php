<?php

namespace App\Repositories\Contracts;

interface HistoryRepositoryContract 
{
    public function insertPhotos($id, $photos);

    public function getGraphData();

    public function getCurrentEvent();
    
}