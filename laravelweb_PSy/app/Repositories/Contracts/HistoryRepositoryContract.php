<?php

namespace App\Repositories\Contracts;

interface HistoryRepositoryContract 
{
    public function insertPhotos($id, $photos);
}