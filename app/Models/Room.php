<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;
    protected $table = "rooms";

    protected $fillable = ["id", "room_name", "filepath","created_at", "updated_at"];

    public function list(){
    	return $this->hasMany('App\Models\Inventory_room', 'room_id', 'id');
    }
}
