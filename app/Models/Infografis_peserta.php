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
        'keterangan',
        'subholding',
        'perusahaan',
        'kategori_program',
        'realisasi'
    ];
}
