<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receivables_participant_certificate extends Model
{
    use HasFactory;
    protected $table = "receivables_participant_certificates";
    protected $fillable = ["id", "penlat_certificate_id", "infografis_peserta_id", "status", "date_received", "expire_date", "certificate_number", "issued_date","created_at", "updated_at"];


    public function peserta(){
    	return $this->belongsTo('App\Models\Infografis_peserta', 'infografis_peserta_id', 'id')->withDefault();
    }

    public function penlatCertificate()
    {
        return $this->belongsTo('App\Models\Penlat_certificate', 'penlat_certificate_id', 'id')->withDefault();
    }
}
