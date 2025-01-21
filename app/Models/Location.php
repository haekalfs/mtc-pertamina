<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;
    protected $table = "locations";
    protected $fillable = ["id", "location_code", 'description',"created_at", "updated_at"];

    public function tools()
    {
        return $this->hasMany('App\Models\Inventory_tools', 'location_id', 'id');
    }
}
