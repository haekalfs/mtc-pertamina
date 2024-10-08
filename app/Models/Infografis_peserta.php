<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Infografis_peserta extends Model
{
    use HasFactory;
    protected $table = "infografis_peserta";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'nama_peserta',
        'nama_program',
        'tgl_pelaksanaan',
        'tempat_pelaksanaan',
        'jenis_pelatihan',
        'batch',
        'keterangan',
        'subholding',
        'perusahaan',
        'kategori_program',
        'realisasi',
        'isDuplicate',
        'participant_id',
        'seafarer_code'
    ];

    public function certificate(){
    	return $this->hasMany('App\Models\Receivables_participant_certificate', 'infografis_peserta_id', 'id');
    }
}
