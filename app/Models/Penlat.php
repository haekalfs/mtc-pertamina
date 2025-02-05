<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penlat extends Model
{
    use HasFactory;
    protected $table = "penlat";
    protected $fillable = ["id", "description", 'alias', 'jenis_pelatihan', 'kategori_pelatihan',"created_at", "updated_at"];


    public function requirement()
    {
        return $this->hasMany('App\Models\Penlat_requirement');
    }

    public function references()
    {
        return $this->hasMany('App\Models\Training_reference', 'penlat_id', 'id');
    }

    public function batch()
    {
        return $this->hasMany('App\Models\Penlat_batch', 'penlat_id', 'id');
    }

    public function penlat_to_certificate()
    {
        return $this->hasMany('App\Models\Certificates_to_penlat', 'penlat_id', 'id');
    }

    public function aliases()
    {
        return $this->hasMany('App\Models\Penlat_alias', 'penlat_id', 'id');
    }

    public function amendments()
    {
        return $this->hasMany('App\Models\Regulator_amendment', 'penlat_id', 'id');
    }
}
