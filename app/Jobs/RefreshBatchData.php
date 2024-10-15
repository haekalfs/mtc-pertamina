<?php

namespace App\Jobs;

use App\Models\Infografis_peserta;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RefreshBatchData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $year;

    // Constructor to accept the year
    public function __construct($year)
    {
        $this->year = $year;
    }

    // Handle method to process the job
    public function handle()
    {
        // Chunking the data filtered by year and dispatch jobs for each chunk
        Infografis_peserta::whereYear('tgl_pelaksanaan', $this->year)
            ->chunk(1000, function ($pesertaRecords) {
                ProcessBatchChunk::dispatch($pesertaRecords);
        });
    }
}
