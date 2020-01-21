<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    protected $table = 'photos';
    protected $fillable = ['url', 'shift_id'];

    //detail
    public function getPhoto()
    {
        $detail = [
                'id' => $this['id'],
                'url' => $this['url'],
                'shift_id' => $this['shift']['id'],
            ];
       
        return $detail;
    }

    public function getPhotoWIthId()
    {
        $photo = [
                'id' => $this['id'],
                'url' => $this['url'],
                'shift' => [
                    'id' => $this['shift']['id'],
                    'name' => $this['shift']['scan_time'],
                ],
                
                
            ];
       
        return $photo;
    }

    

    

    public function shift()
    {
    	return $this->belongsTo('App\Models\Shift');
    }

    

}
