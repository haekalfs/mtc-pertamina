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
            // Encrypt sensitive data fields
            $row->participant_id = Crypt::encryptString($row->participant_id);
            // $row->birth_place = Crypt::encryptString($row->birth_place);
            // $row->birth_date = Crypt::encryptString($row->birth_date);
            // $row->seafarer_code = Crypt::encryptString($row->seafarer_code);

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
}
