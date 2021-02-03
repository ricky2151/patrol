<?php

namespace App\Repositories\Implementations;

use App\Repositories\Contracts\HistoryRepositoryContract;
use App\Models\History;
use App\Exceptions\StoreDataFailedException;

class HistoryRepositoryImplementation extends BaseRepositoryImplementation implements HistoryRepositoryContract  {

    public function __construct(History $model)
    {
        $this->model = $model;
    }

    public function insertPhotos($id, $photos) {
        try {
            $this->model->find($id)->photos()->createMany($photos);
            return true;
        } catch (\Throwable $th) {
            throw new StoreDataFailedException("insert photos failed");
        }
        
    }

}