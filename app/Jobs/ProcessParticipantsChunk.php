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
use Illuminate\Support\Facades\Log;

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
            // Only assign values if not already encrypted
            if (!$this->isEncrypted($row->participant_id)) {
                $row->participant_id = $row->participant_id; // Triggers mutator
            }
            if (!$this->isEncrypted($row->nama_peserta)) {
                $row->nama_peserta = $row->nama_peserta;
            }
            if (!$this->isEncrypted($row->birth_place)) {
                $row->birth_place = $row->birth_place;
            }
            if (!$this->isEncrypted($row->birth_date)) {
                $row->birth_date = $row->birth_date;
            }
            if (!$this->isEncrypted($row->seafarer_code)) {
                $row->seafarer_code = $row->seafarer_code;
            }

            // Save only if any changes are made
            $row->save();
        });
    }

    private function isEncrypted($value)
    {
        if (is_null($value) || $value === '') {
            Log::info('isEncrypted check: Value is empty or null', ['value' => $value]);
            return false; // Empty values are NOT encrypted
        }

        try {
            Crypt::decryptString($value);
            Log::info('isEncrypted check: Value is encrypted', ['value' => $value]);
            return true; // Successfully decrypted, meaning it's encrypted
        } catch (\Exception $e) {
            Log::warning('isEncrypted check: Value is NOT encrypted', [
                'value' => $value,
                'error' => $e->getMessage()
            ]);
            return false; // Failed to decrypt, so it's not encrypted
        }
    }
}
