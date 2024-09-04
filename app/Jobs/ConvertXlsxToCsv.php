<?php

namespace App\Jobs;

use App\Jobs\ImportPenlat;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ConvertXlsxToCsv implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;

    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    public function handle()
    {
        $csvFilePath = str_replace('.xlsx', '.csv', $this->filePath);

        try {
            // Load the XLSX file
            $spreadsheet = IOFactory::load($this->filePath);

            // Save as CSV
            $writer = IOFactory::createWriter($spreadsheet, 'Csv');
            $writer->save($csvFilePath);

            // Dispatch the ImportPenlat job with the CSV file path
            ImportPenlat::dispatch($csvFilePath);

            // Delete the XLSX file after conversion
            if (file_exists($this->filePath)) {
                unlink($this->filePath);
            }
        } catch (\Exception $e) {
            // Handle exceptions
            Log::error('Error converting XLSX to CSV: ' . $e->getMessage());
        }
    }
}
