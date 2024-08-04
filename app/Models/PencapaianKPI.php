<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PencapaianKPI extends Model
{
    use HasFactory;
    protected $table = "kpi_pencapaian";
    protected $fillable = ["id", "pencapaian", 'score', 'quarter_id', 'periode', 'kpi_id',"created_at", "updated_at"];

    public function quarter()
    {
        return $this->belongsTo('App\Models\Quarter')->withDefault();
    }
}
