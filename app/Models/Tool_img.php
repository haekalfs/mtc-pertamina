<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tool_img extends Model
{
    use HasFactory;

    protected $table = "tool-img";
    protected $fillable = ["id", 'filename', 'filepath', 'inventory_tool_id',"created_at", "updated_at"];
}
