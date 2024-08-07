<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penlat_utility extends Model
{
    use HasFactory;
    protected $table = "penlat_utility";
    protected $fillable = ["id", "penlat_id", "penlat_utility_usage_id", "batch", "date","created_at", "updated_at"];


    public function penlat()
    {
        return $this->belongsTo('App\Models\Penlat')->withDefault();
    }

    public function penlat_usage()
    {
        return $this->hasMany('App\Models\Penlat_utility_usage');
    }
}
