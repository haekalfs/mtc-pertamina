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

    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    public function handle()
    {
        try {
            // Load the spreadsheet and get the first sheet
            $spreadsheet = IOFactory::load($this->filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            // Remove empty rows (ensure your row index starts from 1)
            $filteredRows = array_filter($rows, function($row) {
                // Assuming columns 60, 68, 69, and 70 should not be empty
                return !empty($row[60]) && !empty($row[68]) && !empty($row[69]) && !empty($row[70]);
            });

            DB::beginTransaction();

            // Skip the header rows (assuming the first two rows are headers)
            foreach ($filteredRows as $index => $row) {
                if ($index < 2) continue;

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

            // Delete the Excel file if an error occurs
            if (file_exists($this->filePath)) {
                unlink($this->filePath);
            }
            Cache::forget('jobs_processing');
            // Log or handle the exception as needed
            Log::error('Error processing the file: ' . $e->getMessage());
        }
    }
}
