<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Monitoring_approval extends Model
{
    use HasFactory;
    protected $table = "monitoring_approvals";
    protected $fillable = ["id", "description", 'type', 'approval_date', 'user_id', 'filesize', 'filepath',"created_at", "updated_at"];
}
