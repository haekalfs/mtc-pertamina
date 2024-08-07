<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penlat_requirement extends Model
{
    use HasFactory;
    protected $table = "penlat_requirement";
    protected $fillable = ["id", "penlat_id", "inventory_tool_id", "amount","created_at", "updated_at"];

    public function penlat()
    {
        return $this->belongsTo('App\Models\Penlat')->withDefault();
    }

    public function tool()
    {
        return $this->belongsTo('App\Models\Inventory_tool')->withDefault();
    }
}
