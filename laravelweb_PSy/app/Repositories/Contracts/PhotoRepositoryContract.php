<?php

namespace App\Repositories\Contracts;

interface PhotoRepositoryContract 
{
    public function savePhotoToStorage($image, $folder, $name);

}