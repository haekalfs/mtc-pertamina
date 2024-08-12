<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;
    protected $table = "marketing_campaign";
    protected $fillable = ["id", "campaign_name", 'campaign_details', 'campaign_result', 'user_id', 'date', 'img_filepath',"created_at", "updated_at"];
}
