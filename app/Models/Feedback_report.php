<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback_report extends Model
{
    use HasFactory;

    protected $table = 'feedback_reports';

    // Define the fillable fields
    protected $fillable = [
        'tgl_pelaksanaan',
        'tempat_pelaksanaan',
        'nama',
        'kelompok',
        'judul_pelatihan',
        'instruktur',
        'feedback_template_id',
        'score',
        'updated_at',
        'created_at'
    ];

    public function question()
    {
        return $this->belongsTo('App\Models\Feedback_template', 'feedback_template_id', 'id')->withDefault();
    }
}
