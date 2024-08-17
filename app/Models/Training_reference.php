<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Training_reference extends Model
{
    use HasFactory;
    protected $table = "training_references";
    protected $fillable = ["id", "penlat_id", "references", "filepath","created_at", "updated_at"];

    public function penlat()
    {
        return $this->belongsTo('App\Models\Penlat')->withDefault();
    }
}
