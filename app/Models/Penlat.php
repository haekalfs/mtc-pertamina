<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penlat extends Model
{
    use HasFactory;
    protected $table = "penlat";
    protected $fillable = ["id", "description","created_at", "updated_at"];


    public function requirement()
    {
        return $this->hasMany('App\Models\Penlat_requirement');
    }
}
