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

    private function decryptOrMask($value, $type)
    {
        if (!$this->isEncrypted($value)) {
            return $value; // Return plaintext as is
        }

        try {
            // Fetch admin roles
            $adminRoles = Role::where('isSuperAdmin', 1)->pluck('role')->toArray();

            // Check if the user has admin role
            if (auth()->check() && array_intersect(session('allowed_roles', []), $adminRoles)) {
                return Crypt::decryptString($value);
            }

            // If not admin, return masked
            return $this->maskEncryptedValue($value, $type);
        } catch (\Exception $e) {
            return '[Decryption Failed]';
        }
    }

    private function isEncrypted($value)
    {
        try {
            Crypt::decryptString($value);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function maskEncryptedValue($value, $type = 'default')
    {
        try {
            $decrypted = Crypt::decryptString($value);

            switch ($type) {
                case 'name': // Mask last name, keep first name
                    $parts = explode(' ', $decrypted);
                    if (count($parts) > 1) {
                        $lastName = array_pop($parts);
                        $maskedLastName = substr($lastName, 0, 1) . str_repeat('*', max(strlen($lastName) - 1, 0));
                        $parts[] = $maskedLastName;
                        return implode(' ', $parts);
                    }
                    return $decrypted; // Return as is if there's only one word

                case 'participant_id': // Mask all but last 3 digits
                    return strlen($decrypted) > 3
                        ? str_repeat('*', strlen($decrypted) - 3) . substr($decrypted, -3)
                        : str_repeat('*', strlen($decrypted)); // Mask all if shorter

                case 'birth_place': // Show only first 2 characters
                    return strlen($decrypted) > 2
                        ? substr($decrypted, 0, 2) . str_repeat('*', strlen($decrypted) - 2)
                        : str_repeat('*', strlen($decrypted)); // Mask fully if shorter

                case 'birth_date': // Show full day & month, mask year
                    try {
                        $date = \Carbon\Carbon::parse($decrypted);
                        return $date->format('d F ') . '****';
                    } catch (\Exception $e) {
                        return '[Invalid Date]';
                    }

                case 'seafarer_code': // Show only first 2 characters
                    return strlen($decrypted) > 2
                        ? substr($decrypted, 0, 2) . str_repeat('*', strlen($decrypted) - 2)
                        : str_repeat('*', strlen($decrypted)); // Mask fully if shorter

                default: // Fallback for unknown types
                    return '[Hidden]';
            }
        } catch (\Exception $e) {
            return '[Hidden]';
        }
    }

    public function getNamaPesertaAttribute($value)
    {
        return $this->decryptOrMask($value, 'name');
    }

    public function getParticipantIdAttribute($value)
    {
        return $this->decryptOrMask($value, 'participant_id');
    }

    public function getBirthPlaceAttribute($value)
    {
        return $this->decryptOrMask($value, 'birth_place');
    }

    public function getBirthDateAttribute($value)
    {
        return $this->decryptOrMask($value, 'birth_date');
    }

    public function getSeafarerCodeAttribute($value)
    {
        return $this->decryptOrMask($value, 'seafarer_code');
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
