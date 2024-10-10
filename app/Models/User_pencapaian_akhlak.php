<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_pencapaian_akhlak extends Model
{
    use HasFactory;
    protected $table = "user_pencapaian_akhlak";
    protected $fillable = ["id", "judul_kegiatan", 'score', 'user_id', 'akhlak_id', 'quarter_id', 'periode',"created_at", "updated_at"];

    public function file(){
    	return $this->hasOne('App\Models\User_pencapaian_akhlak_files')->withDefault();
    }

    public function user(){
    	return $this->belongsTo('App\Models\User')->withDefault();
    }

    public function akhlak(){
    	return $this->belongsTo('App\Models\Akhlak')->withDefault();
    }

    public function quarter()
    {
        return $this->belongsTo('App\Models\Quarter')->withDefault();
    }

    public function scores(){
    	return $this->belongsTo('App\Models\Nilai', 'score', 'id')->withDefault();
    }
}
