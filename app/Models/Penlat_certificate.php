<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penlat_certificate extends Model
{
    use HasFactory;
    protected $table = "penlat_certificates";
    protected $fillable = ["id", "penlat_batch_id", "keterangan", "status", "total_issued","created_at", "updated_at"];

    public function batch(){
    	return $this->belongsTo('App\Models\Penlat_batch', 'penlat_batch_id', 'id')->withDefault();
    }

    public function participant()
    {
        return $this->hasMany('App\Models\Receivables_participant_certificate', 'penlat_certificate_id', 'id');
    }
}
