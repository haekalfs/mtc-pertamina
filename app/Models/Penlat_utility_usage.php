<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penlat_utility_usage extends Model
{
    use HasFactory;
    protected $table = "penlat_utility_usage";
    protected $fillable = ["id", "penlat_batch_id", "utility_id", "amount", "price", "total","created_at", "updated_at"];

    public function penlat_utility()
    {
        return $this->belongsTo('App\Models\Penlat_utility')->withDefault();
    }

    public function utility()
    {
        return $this->belongsTo('App\Models\Utility')->withDefault();
    }
}
