<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_pencapaian_akhlak_files extends Model
{
    use HasFactory;
    protected $table = "user_pencapaian_akhlak_files";
    protected $fillable = ["id", "user_id", 'filename', 'filepath', 'user_pencapaian_akhlak_id',"created_at", "updated_at"];
}
