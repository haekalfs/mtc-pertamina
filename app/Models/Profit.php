<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profit extends Model
{
    use HasFactory;

    protected $fillable = [
        'tgl_pelaksanaan',
        'pelaksanaan',
        'jumlah_peserta',
        'biaya_instruktur',
        'total_pnbp',
        'biaya_transportasi_hari',
        'honor_pnbp',
        'biaya_pendaftaran_peserta',
        'total_biaya_pendaftaran_peserta',
        'penagihan_foto',
        'penagihan_atk',
        'penagihan_snack',
        'penagihan_makan_siang',
        'penagihan_laundry',
        'jumlah_biaya',
        'profit'
    ];

    public function batch(){
        return $this->belongsTo('App\Models\Penlat_batch', 'pelaksanaan', 'batch')->withDefault(function ($batch) {
            // Set default attributes for the default instance
            $batch->id = 1; // Set the default primary key value (or other relevant attributes)
        });
    }
}
