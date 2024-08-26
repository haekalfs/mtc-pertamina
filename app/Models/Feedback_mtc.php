<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Feedback_mtc extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_peserta',
        'judul_pelatihan',
        'tempat_pelaksanaan',
        'tgl_pelaksanaan',
        'email_peserta',
        'relevansi_materi',
        'manfaat_training',
        'durasi_training',
        'sistematika_penyajian',
        'tujuan_tercapai',
        'kedisiplinan_steward',
        'fasilitasi_steward',
        'layanan_pelaksana',
        'proses_administrasi',
        'kemudahan_registrasi',
        'kondisi_peralatan',
        'kualitas_boga',
        'media_online',
        'rekomendasi',
        'saran'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'feedback_mtc';

    public function getAverageFeedbackScore()
    {
        return self::select(DB::raw('
            avg(
                (relevansi_materi +
                manfaat_training +
                durasi_training +
                sistematika_penyajian +
                tujuan_tercapai +
                kedisiplinan_steward +
                fasilitasi_steward +
                layanan_pelaksana +
                proses_administrasi +
                kemudahan_registrasi +
                kondisi_peralatan +
                kualitas_boga +
                media_online +
                rekomendasi) / 14
            ) as average_score
        '))->value('average_score');
    }
}
