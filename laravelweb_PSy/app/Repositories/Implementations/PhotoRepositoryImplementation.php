<?php

namespace App\Repositories\Implementations;

use App\Repositories\Contracts\PhotoRepositoryContract;
use App\Models\Photo;

class PhotoRepositoryImplementation extends BaseRepositoryImplementation implements PhotoRepositoryContract  {
    public function __construct(Photo $model)
    {
        $this->model = $model;
    }

    public function savePhotoToStorage($image, $folder, $name) {
        $path = $image->storeAs($folder, $name);
        return $path;
    }
}