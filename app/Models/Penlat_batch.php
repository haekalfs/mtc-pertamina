<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penlat_batch extends Model
{
    use HasFactory;
    protected $table = "penlat_batch";
    protected $fillable = ["id", "penlat_id", "nama_program", "filepath", "batch", "date","created_at", "updated_at"];


    public function penlat()
    {
        return $this->belongsTo('App\Models\Penlat')->withDefault();
    }

    public function penlat_usage()
    {
        return $this->hasMany('App\Models\Penlat_utility_usage', 'penlat_batch_id', 'id');
    }

    public function certificate()
    {
        return $this->hasOne('App\Models\Penlat_certificate', 'penlat_batch_id', 'id')->withDefault();
    }
}
