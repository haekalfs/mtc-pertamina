<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign_type extends Model
{
    use HasFactory;
    protected $table = "campaign_type";
    protected $fillable = ["id", "description","created_at", "updated_at"];
}
