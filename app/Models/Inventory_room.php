<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory_room extends Model
{
    use HasFactory;
    protected $table = "inventory_rooms";

    protected $fillable = ["id", "room_id", "inventory_tool_id", "amount","created_at", "updated_at"];

    public function tools(){
    	return $this->belongsTo('App\Models\Inventory_tools', 'inventory_tool_id', 'id');
    }
}
