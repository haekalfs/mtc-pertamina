<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PencapaianKPI extends Model
{
    use HasFactory;
    protected $table = "kpi_pencapaian";
    protected $fillable = ["id", "pencapaian", 'score', 'periode_start', 'periode_end', 'kpi_id',"created_at", "updated_at"];
}
