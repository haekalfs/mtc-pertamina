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
}
