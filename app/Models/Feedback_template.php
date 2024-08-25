<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback_template extends Model
{
    use HasFactory;

    protected $table = 'feedback_template';

    // Define the fillable fields
    protected $fillable = [
        'id',
        'questioner',
        'updated_at',
        'created_at'
    ];

    public function reports()
    {
        return $this->hasMany('App\Models\Feedback_report', 'feedback_template_id', 'id');
    }
}
