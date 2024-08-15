<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificates_to_penlat extends Model
{
    use HasFactory;
    protected $table = "certificates_to_penlats";
    protected $fillable = ["id", "penlat_id", 'certificates_catalog_id',"created_at", "updated_at"];

    public function penlat()
    {
        return $this->belongsTo('App\Models\Penlat')->withDefault();
    }
}
