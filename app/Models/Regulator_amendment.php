<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Regulator_amendment extends Model
{
    use HasFactory;
    protected $table = "regulator_amendment";
    protected $fillable = ["id", "penlat_id", "description", "translation", "regulator_id","created_at", "updated_at"];

    public function certificates()
    {
        return $this->hasMany('App\Models\Penlat_certificate', 'regulator_amendment', 'id');
    }

    public function penlats()
    {
        return $this->belongsTo('App\Models\Penlat', 'penlat_id', 'id')->withDefault();
    }

    public function regulators()
    {
        return $this->belongsTo('App\Models\Regulator', 'regulator_id', 'id')->withDefault();
    }
}
