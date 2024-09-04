<?php

namespace App\Jobs;

use App\Models\Penlat;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

class ImportPenlat implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            // Load the CSV file
            $rows = array_map('str_getcsv', file($this->filePath));

            DB::beginTransaction();

            foreach ($rows as $index => $row) {
                if ($index < 2) continue;

                if (empty($row[60]) || empty($row[68]) || empty($row[69]) || empty($row[70])) {
                    continue;
                }

                Penlat::updateOrCreate(
                    [
                        'description' => $row[60],
                        'alias' => $row[68],
                        'jenis_pelatihan' => $row[69],
                        'kategori_pelatihan' => $row[70],
                    ],
                    []
                );
            }

            DB::commit();

            if (file_exists($this->filePath)) {
                unlink($this->filePath);
            }
            Cache::forget('jobs_processing');
        } catch (\Exception $e) {
            DB::rollBack();

            if (file_exists($this->filePath)) {
                unlink($this->filePath);
            }
            Cache::forget('jobs_processing');
            Log::error('Error processing the file: ' . $e->getMessage());
        }
    }
}
