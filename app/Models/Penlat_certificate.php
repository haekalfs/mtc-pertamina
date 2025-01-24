<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penlat_certificate extends Model
{
    use HasFactory;
    protected $table = "penlat_certificates";
    protected $fillable = [
        'penlat_batch_id',
        'keterangan',
        'status',
        'total_issued',
        'created_by',
        'start_date',
        'end_date',
        'regulator',
        'certificate_title',
        'regulator_amendment'
    ];

    public function batch(){
    	return $this->belongsTo('App\Models\Penlat_batch', 'penlat_batch_id', 'id')->withDefault();
    }

    public function batches(){
    	return $this->belongsTo('App\Models\Penlat_batch', 'penlat_batch_id', 'id')->withDefault();
    }

    public function regulation(){
    	return $this->belongsTo('App\Models\Regulator', 'regulator', 'id')->withDefault();
    }

    public function regulation_amendments(){
    	return $this->belongsTo('App\Models\Regulator_amendment', 'regulator_amendment', 'id')->withDefault();
    }

    public function participant()
    {
        return $this->hasMany('App\Models\Receivables_participant_certificate', 'penlat_certificate_id', 'id');
    }
    // Use the plural name for participants
    public function participants()
    {
        return $this->hasMany('App\Models\Receivables_participant_certificate', 'penlat_certificate_id', 'id');
    }
}
