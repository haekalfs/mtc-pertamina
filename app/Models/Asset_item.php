<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset_item extends Model
{
    use HasFactory;
    protected $table = "asset_items";
    protected $fillable = ["id", "asset_code", "asset_condition_id", "isUsed", 'inventory_tool_id',"created_at", "updated_at"];

    public function tools(){
    	return $this->belongsTo('App\Models\Inventory_tools', 'inventory_tool_id', 'id');
    }

    public function condition(){
    	return $this->belongsTo('App\Models\Asset_condition', 'asset_condition_id', 'id');
    }
}