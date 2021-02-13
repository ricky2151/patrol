<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File; 
use Illuminate\Support\Facades\Storage;

class Photo extends Model
{
    protected $table = 'photos';
    protected $fillable = ['url', 'history_id', 'photo_time'];

    public function history()
    {
    	return $this->belongsTo('App\Models\History', 'history_id', 'id');
    }

    public function delete()
    {   
        $exists = Storage::disk('local')->exists($this->url);
        if($exists)
        {
            File::delete('storage/' . $this->url);
        }
        
        return parent::delete();
    }

    

}
