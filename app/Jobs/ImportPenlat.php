<?php

namespace App\Jobs;

use App\Models\Penlat;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
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

        $csvFilePath = pathinfo($this->filePath, PATHINFO_DIRNAME) . '/' . pathinfo($this->filePath, PATHINFO_FILENAME) . '.csv';

        try {
            // Load the Excel file
            $reader = IOFactory::createReaderForFile($this->filePath);
            $spreadsheet = $reader->load($this->filePath);

            // Convert to CSV
            $writer = new Csv($spreadsheet);
            $writer->save($csvFilePath);

            // Process the CSV file
            $handle = fopen($csvFilePath, 'r');

            // Skip the header row
            fgetcsv($handle);
            fgetcsv($handle);

            // Loop through the rows and save the data to the database
            while (($row = fgetcsv($handle)) !== FALSE) {
                if (empty($row[60]) || empty($row[68]) || empty($row[69])) {
                    break; // Skip rows with empty required fields
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

            fclose($handle);

            // Clean up CSV file
            unlink($csvFilePath);

        } catch (\Exception $e) {
            // Log or handle the exception as needed
            Log::error('Error processing the file: ' . $e->getMessage());
        }
    }
}
