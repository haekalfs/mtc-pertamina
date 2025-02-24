<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

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
        'seafarer_code',
        'registration_number',
        'birth_place',
        'birth_date',
        'harga_pelatihan',
        'tgl_pendaftaran'
    ];

    public function getNamaPesertaAttribute($value)
    {
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return '[Decryption Failed]';
        }
    }

    public function setNamaPesertaAttribute($value)
    {
        $this->attributes['nama_peserta'] = Crypt::encryptString($value);
        $this->attributes['birth_place'] = Crypt::encryptString($value);
        $this->attributes['birth_date'] = Crypt::encryptString($value);
        $this->attributes['participant_id'] = Crypt::encryptString($value);
        $this->attributes['seafarer_code'] = Crypt::encryptString($value);
    }

    public function certificate(){
    	return $this->hasMany('App\Models\Receivables_participant_certificate', 'infografis_peserta_id', 'id');
    }

    public function certificateCheck()
    {
    	return $this->belongsTo('App\Models\Receivables_participant_certificate', 'id', 'infografis_peserta_id')->withDefault();
    }

    public function penlatBatch()
    {
        return $this->belongsTo('App\Models\Penlat_batch', 'batch', 'batch')->withDefault();
    }

    public function batches()
    {
        return $this->belongsTo('App\Models\Penlat_batch', 'batch', 'batch')->withDefault();
    }
}
