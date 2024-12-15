<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Error_log_import extends Model
{
    use HasFactory;
    protected $table = "error_log_import";
    protected $fillable = ["id", "description", 'row', 'user_id', 'import_id',"created_at", "updated_at"];

    public function user(){
    	return $this->belongsTo('App\Models\User')->withDefault();
    }
}
