<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agreement extends Model
{
    use HasFactory;
    protected $table = "marketing_agreement";
    protected $fillable = ["id", "company_name", 'company_details', 'status', 'spk_filepath', 'img_filepath', 'user_id',"created_at", "updated_at"];


    public function statuses(){
    	return $this->belongsTo('App\Models\Status', 'status', 'id');
    }
}
