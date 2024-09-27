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

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Chunking the data to dispatch individual jobs for each chunk
        Infografis_peserta::chunk(1000, function ($pesertaRecords) {
            // For each chunk, dispatch a job to process that chunk
            ProcessBatchChunk::dispatch($pesertaRecords);
        });
    }
}
