<?php

namespace App\Jobs;

use App\Models\Penlat;
use App\Models\Penlat_alias;
use App\Models\Penlat_batch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessBatchChunk implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $pesertaRecords; // Holds the chunk data

    /**
     * Create a new job instance.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $pesertaRecords
     * @return void
     */
    public function __construct($pesertaRecords)
    {
        $this->pesertaRecords = $pesertaRecords;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->pesertaRecords as $row) {
            // Explode the 'batch' column value to get the first part
            $parts = explode('/', $row->batch);
            $firstWord = $parts[0];

            // Check if the alias exists in the Penlat table
            $checkPenlat = Penlat_alias::where('alias', $firstWord)->exists();
            if ($checkPenlat) {
                // Fetch the matching Penlat record
                $getPenlat = Penlat_alias::where('alias', $firstWord)->first();

                // Update or create the record in the Penlat_batch table
                Penlat_batch::updateOrCreate(
                    [
                        'batch' => $row->batch, // The unique batch identifier
                    ],
                    [
                        'penlat_id' => $getPenlat->penlat->id,
                        'nama_program' => $getPenlat->penlat->description,
                        'date' => $this->getFormattedDate($row->tgl_pelaksanaan) // Replace 'date_column' with the actual date field in Infografis_peserta
                    ]
                );
            }
        }
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
