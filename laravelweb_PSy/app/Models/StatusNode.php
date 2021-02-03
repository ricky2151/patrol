<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusNode extends Model
{
	protected $table = 'status_nodes';
    protected $fillable = ['name']; 
    protected $hidden = ['created_at', 'updated_at'];
    public function histories()
    {
    	return $this->hasMany('App\Models\History');
    }

    public function delete()
    {   
        error_log("status_node.delete");
        foreach($this->histories as $history) { $history->delete(); }
        
        return parent::delete();
    }

 
    
}
