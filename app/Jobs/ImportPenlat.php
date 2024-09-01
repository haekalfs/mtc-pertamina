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
        ini_set('memory_limit', '1024M'); // Adjust the memory limit as needed

        try {
            // Load the Excel file
            $reader = IOFactory::createReaderForFile($this->filePath);
            $spreadsheet = $reader->load($this->filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            DB::beginTransaction();

            // Skip the header rows (assuming the first two rows are headers)
            foreach ($rows as $index => $row) {
                if ($index < 2) continue;

                // Skip rows with empty required fields
                if (empty($row[60]) || empty($row[68]) || empty($row[69])) {
                    continue;
                }

                // Insert or update the Penlat records
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

            // Delete the Excel file after processing
            if (file_exists($this->filePath)) {
                unlink($this->filePath);
            }
            Cache::forget('jobs_processing');
        } catch (\Exception $e) {
            DB::rollBack();

            // Delete the Excel file after processing
            if (file_exists($this->filePath)) {
                unlink($this->filePath);
            }
            Cache::forget('jobs_processing');
            // Log or handle the exception as needed
            Log::error('Error processing the file: ' . $e->getMessage());
        }
    }
}
