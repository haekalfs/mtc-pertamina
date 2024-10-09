<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Utility extends Model
{
    use HasFactory;
    protected $table = "utility";
    protected $fillable = ["id", "utility_name", "utility_unit", "field_name", "filepath", "created_at", "updated_at"];

    public function items()
    {
        return $this->hasMany('App\Models\Penlat_utility_usage', 'utility_id', 'id');
    }
}
