<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Imported_file extends Model
{
    use HasFactory;
    protected $table = "imported_files";
    protected $fillable = ["id", "filename", 'filepath',"created_at", "updated_at"];
}
