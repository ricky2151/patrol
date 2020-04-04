<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    protected $table = 'photos';
    protected $fillable = ['url', 'history_id'];

    //detail
    public function getPhoto()
    {
        $detail = [
                'id' => $this['id'],
                'url' => $this['url'],
                'history_id' => $this['history']['id'],
            ];
       
        return $detail;
    }

  

    

    

    public function history()
    {
    	return $this->belongsTo('App\Models\History', 'history_id', 'id');
    }

    

}
