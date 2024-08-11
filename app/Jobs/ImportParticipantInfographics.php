<?php

namespace App\Jobs;

use App\Models\Infografis_peserta;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

class ImportParticipantInfographics implements ShouldQueue
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

            // Loop through the rows and save the data to the database
            while (($row = fgetcsv($handle)) !== FALSE) {
                if (empty($row[2]) || empty($row[11]) || empty($row[12]) || empty($row[13]) || empty($row[14])) {
                    continue; // Skip rows with empty required fields
                }

                // Additional validation if needed
                if (strlen($row[2]) > 255 || !is_string($row[2])) {
                    continue; // Skip invalid entries
                }

                Infografis_peserta::create([
                    'nama_peserta' => $row[2],
                    'nama_program' => $row[11],
                    'tgl_pelaksanaan' => $row[12],
                    'tempat_pelaksanaan' => $row[13],
                    'jenis_pelatihan' => $row[14],
                    'keterangan' => $row[16],
                    'subholding' => $row[17],
                    'perusahaan' => $row[18],
                    'kategori_program' => $row[19],
                    'realisasi' => $row[20],
                ]);
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
