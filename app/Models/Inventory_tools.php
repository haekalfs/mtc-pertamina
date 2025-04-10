<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory_tools extends Model
{
    use HasFactory;
    protected $table = "inventory_tools";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'asset_id',
        'asset_name',
        'asset_maker',
        'asset_condition_id',
        'asset_stock',
        'asset_guidance',
        'next_maintenance',
        'last_maintenance',
        'initial_stock',
        'location_id',
        'purchased_date',
        'used_amount',
        'created_by'
    ];

    public function img(){
    	return $this->hasOne('App\Models\Tool_img', 'inventory_tool_id', 'id')->withDefault();
    }

    public function condition(){
    	return $this->belongsTo('App\Models\Asset_condition', 'asset_condition_id', 'id')->withDefault();
    }

    public function items()
    {
        return $this->hasMany('App\Models\Asset_item', 'inventory_tool_id', 'id');
    }

    public function location()
    {
        return $this->belongsTo('App\Models\Location')->withDefault();
    }

    public function rooms_inventory()
    {
        return $this->hasMany('App\Models\Inventory_room', 'inventory_tool_id', 'id');
    }

    public function penlat_requirement()
    {
        return $this->hasMany('App\Models\Penlat_requirement', 'inventory_tool_id', 'id');
    }

    public function user(){
    	return $this->belongsTo('App\Models\User', 'created_by', 'id')->withDefault();
    }
}
