<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instructor extends Model
{
    use HasFactory;
    protected $table = "instructors";
    protected $fillable = ["id", "instructor_name", 'instructor_email', 'instructor_gender', 'working_hour', 'status', 'instructor_dob', 'imgFilepath', 'instructor_address', 'cvFilepath', 'ijazahFilepath', 'documentPendukungFilepath',"created_at", "updated_at"];


    public function certificates()
    {
        return $this->hasMany('App\Models\Instructor_certificate', 'instructor_id', 'id');
    }

    public function feedbacks()
    {
        return $this->hasMany('App\Models\Feedback_report', 'instruktur', 'instructor_name');
    }

    // Calculate the average feedback score
    public function getAverageFeedbackScoreAttribute()
    {
        return $this->feedbacks()->avg('score');
    }
}
