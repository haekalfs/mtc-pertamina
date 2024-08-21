<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instructor extends Model
{
    use HasFactory;
    protected $table = "instructors";
    protected $fillable = ["id", "instructor_name", 'instructor_email', 'working_hour', 'status', 'instructor_dob', 'imgFilepath', 'instructor_address', 'cvFilepath', 'ijazahFilepath',"created_at", "updated_at"];


    public function certificates()
    {
        return $this->hasMany('App\Models\Instructor_certificate', 'instructor_id', 'id');
    }
}
