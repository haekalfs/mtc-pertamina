<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Social_token extends Model
{
    use HasFactory;
    protected $table = "social_tokens";

    protected $fillable = ["id", "social_media", "account_name", "page_id", "token","created_at", "updated_at"];
}
