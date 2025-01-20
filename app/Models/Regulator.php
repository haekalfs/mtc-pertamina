<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Regulator extends Model
{
    use HasFactory;
    protected $table = "regulator";
    protected $fillable = ["id", "description","created_at", "updated_at"];
}
