<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Regulation extends Model
{
    use HasFactory;
    protected $table = "regulations";
    protected $fillable = ["id", "description", 'status', 'user_id', 'filesize', 'filepath',"created_at", "updated_at"];


    public function statuses(){
    	return $this->belongsTo('App\Models\Status', 'status', 'id')->withDefault();
    }
}
