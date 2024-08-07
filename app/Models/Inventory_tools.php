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
        'asset_condition',
        'asset_stock',
        'asset_guidance_id'
    ];

    public function img(){
    	return $this->hasOne('App\Models\Tool_img', 'inventory_tool_id', 'id');
    }

    public function penlat_usage()
    {
        return $this->hasMany('App\Models\Penlat_utility_usage', 'inventory_tool_id', 'id');
    }
}
