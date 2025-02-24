<?php

namespace App\Jobs;

use App\Models\Infografis_peserta;
use App\Models\Penlat;
use App\Models\Penlat_alias;
use App\Models\Penlat_batch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Crypt;

class ProcessParticipantsChunk implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $pesertaRecords
     * @return void
     */
    protected $pesertaIds;

    public function __construct($pesertaIds)
    {
        $this->pesertaIds = $pesertaIds;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Infografis_peserta::whereIn('id', $this->pesertaIds)->each(function ($row) {
            // Check and encrypt only if not already encrypted
            $row->participant_id = $this->encryptIfNeeded($row->participant_id);
            $row->nama_peserta = $this->encryptIfNeeded($row->nama_peserta);
            $row->birth_place = $this->encryptIfNeeded($row->birth_place);
            $row->birth_date = $this->encryptIfNeeded($row->birth_date);
            $row->seafarer_code = $this->encryptIfNeeded($row->seafarer_code);

            // Save the updated record
            $row->save();
        });
    }

    /**
     * Helper function to format the date.
     * Modify this as per your required format.
     *
     * @param string $date
     * @return string
     */
    protected function getFormattedDate($date)
    {
        return \Carbon\Carbon::parse($date)->format('Y-m-d');
    }

    private function encryptIfNeeded($value)
    {
        if (is_null($value) || $value === '') {
            return $value; // Skip encryption for NULL or empty values
        }

        if ($this->isEncrypted($value)) {
            return $value; // Already encrypted, return as is
        }

        return $value;
    }

    private function isEncrypted($value)
    {
        // Encrypted values are base64-encoded, check its format
        if (!preg_match('/^[a-zA-Z0-9\/+]+={0,2}$/', $value)) {
            return false; // Not a valid base64 string
        }

        try {
            Crypt::decryptString($value);
            return true; // Successfully decrypted, it's already encrypted
        } catch (\Exception $e) {
            return false; // Decryption failed, it's not encrypted
        }
    }
}
