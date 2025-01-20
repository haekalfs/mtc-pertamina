<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization_lead extends Model
{
    use HasFactory;
    protected $table = "organization_lead";
    protected $fillable = ["id", "description","created_at", "updated_at"];
}
