<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penlat_alias extends Model
{
    use HasFactory;
    protected $table = "penlat_alias";
    protected $fillable = ["id", "penlat_id", "alias","created_at", "updated_at"];

    public function penlat()
    {
        return $this->belongsTo('App\Models\Penlat', 'penlat_id', 'id')->withDefault();
    }
}
