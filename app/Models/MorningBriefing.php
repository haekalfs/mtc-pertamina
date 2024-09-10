<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MorningBriefing extends Model
{
    use HasFactory;
    protected $table = "morning_briefing";
    protected $fillable = ["id", "briefing_name", 'briefing_details', 'briefing_result', 'user_id', 'date', 'img_filepath',"created_at", "updated_at"];
}
