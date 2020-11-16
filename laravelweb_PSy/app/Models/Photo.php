<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File; 
use Illuminate\Support\Facades\Storage;

class Photo extends Model
{
    protected $table = 'photos';
    protected $fillable = ['url', 'history_id', 'photo_time'];

    //detail
    public function getPhoto()
    {
        $detail = [
                'id' => $this['id'],
                'url' => $this['url'],
                'history_id' => $this['history']['id'],
                'photo_time' => $this['photo_time'],
            ];
       
        return $detail;
    }

    public function delete()
    {   
        error_log("photo.delete");
        $exists = Storage::disk('local')->exists($this->url);
        if($exists)
        {
            File::delete('storage/' . $this->url);
        }
        
        return parent::delete();
    }

  

    

    

    public function history()
    {
    	return $this->belongsTo('App\Models\History', 'history_id', 'id');
    }

    

}
