<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KPI extends Model
{
    use HasFactory;
    protected $table = "kpi";
    protected $fillable = ["id", "indicator", 'target', 'periode_start', 'periode_end',"created_at", "updated_at"];

    public function pencapaian(){
    	return $this->hasMany('App\Models\PencapaianKPI', 'kpi_id', 'id');
    }
}
