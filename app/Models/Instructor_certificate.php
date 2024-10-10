<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instructor_certificate extends Model
{
    use HasFactory;
    protected $table = "instructor_certificates";
    protected $fillable = ["id", "instructor_id", 'certificates_catalog_id',"created_at", "updated_at"];

    public function catalog(){
    	return $this->belongsTo('App\Models\Certificates_catalog', 'certificates_catalog_id', 'id')->withDefault();
    }

    public function instructor(){
    	return $this->belongsTo('App\Models\Instructor', 'instructor_id', 'id')->withDefault();
    }
}
