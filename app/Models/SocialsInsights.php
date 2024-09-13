<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialsInsights extends Model
{
    use HasFactory;
    protected $table = "social_media_insights";

    protected $fillable = ["id", "social_id", "posts_count", "likes_count", "visitors_count", "comments_count","created_at", "updated_at"];
}
