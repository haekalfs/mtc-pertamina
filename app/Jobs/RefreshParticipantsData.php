<?php

namespace App\Jobs;

use App\Models\Infografis_peserta;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RefreshParticipantsData implements ShouldQueue
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
        // Get only records where nama_peserta is likely not encrypted (shortest length)
        Infografis_peserta::whereYear('tgl_pelaksanaan', $this->year)
        ->whereRaw('CHAR_LENGTH(nama_peserta) < 50') // Adjust 50 based on expected encrypted length
        ->select('id') // Only pass IDs
        ->chunk(1000, function ($pesertaRecords) {
            ProcessParticipantsChunk::dispatch($pesertaRecords->pluck('id'));
        });

    }
}
