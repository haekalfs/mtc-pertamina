<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificates_catalog extends Model
{
    use HasFactory;
    protected $table = "certificates_catalogs";
    protected $fillable = ["id", "certificate_name", 'issuedBy', 'keterangan', 'total_issued',"created_at", "updated_at"];

    public function relationOne()
    {
        return $this->hasMany('App\Models\Certificates_to_penlat', 'certificates_catalog_id', 'id');
    }
}
