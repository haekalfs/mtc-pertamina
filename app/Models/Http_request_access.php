<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Http_request_access extends Model
{
    use HasFactory;
    protected $table = "http_request_access";

    protected $fillable = ["id", "method_id", "role_id", "created_at", "updated_at"];

    public function role(){
    	return $this->belongsTo('App\Models\Role');
    }
}
