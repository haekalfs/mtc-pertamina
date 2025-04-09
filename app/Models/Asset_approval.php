<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset_approval extends Model
{
    use HasFactory;
    protected $table = "asset_approvals";
    protected $fillable = ["id", "asset_code", "asset_condition_id", "isUsed", "asset_last_maintenance", "asset_next_maintenance", 'asset_item_id',"created_at", "updated_at"];

    public function asset_items(){
    	return $this->belongsTo('App\Models\Asset_item', 'asset_item_id', 'id')->withDefault();
    }

    public function condition(){
    	return $this->belongsTo('App\Models\Asset_condition', 'asset_condition_id', 'id')->withDefault();
    }
}
