<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Regulator_amendment extends Model
{
    use HasFactory;
    protected $table = "regulator_amendment";
    protected $fillable = ["id", "description", "translation","created_at", "updated_at"];

    public function certificates()
    {
        return $this->hasMany('App\Models\Penlat_certificate', 'regulator_amendment', 'id');
    }
}
