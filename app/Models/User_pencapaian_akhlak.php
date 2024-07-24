<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_pencapaian_akhlak extends Model
{
    use HasFactory;
    protected $table = "user_pencapaian_akhlak";
    protected $fillable = ["id", "judul_kegiatan", 'score', 'user_id', 'akhlak_id', 'periode_start', 'periode_end',"created_at", "updated_at"];

    public function file(){
    	return $this->hasMany('App\Models\PencapaianKPI');
    }

    public function user(){
    	return $this->belongsTo('App\Models\User');
    }

    public function akhlak(){
    	return $this->belongsTo('App\Models\Akhlak');
    }
}
