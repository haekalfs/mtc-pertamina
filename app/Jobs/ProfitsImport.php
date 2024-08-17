<?php

namespace App\Jobs;

use App\Models\Profit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

class ProfitsImport implements ShouldQueue
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

            // Convert to CSV if not already a CSV
            $extension = pathinfo($this->filePath, PATHINFO_EXTENSION);
            if ($extension !== 'csv') {
                $csvFilePath = pathinfo($this->filePath, PATHINFO_DIRNAME) . '/' . pathinfo($this->filePath, PATHINFO_FILENAME) . '.csv';
                $writer = new Csv($spreadsheet);
                $writer->save($csvFilePath);
            } else {
                $csvFilePath = $this->filePath;
            }

            // Process the CSV file
            $handle = fopen($csvFilePath, 'r');

            // Skip the header row
            fgetcsv($handle);

            // Loop through the rows and save the data to the database
            while (($row = fgetcsv($handle)) !== FALSE) {
                // Check if the row contains a date in the first column
                if ($this->isDate($row[0])) {
                    // Convert the date to 'Y-m-d' format
                    $currentDate = \DateTime::createFromFormat('d-M-y', $row[0])->format('Y-m-d');
                }

                if (strpos($row[0], '/') !== false) {
                    // Process the row as a batch value row
                    Profit::create([
                        'tgl_pelaksanaan' => $currentDate,
                        'pelaksanaan' => $row[0],
                        'jumlah_peserta' => $row[2],
                        'biaya_instruktur' => preg_replace('/[^0-9]/', '', $row[4]),
                        'total_pnbp' => preg_replace('/[^0-9]/', '', $row[5]),
                        'biaya_transportasi_hari' => preg_replace('/[^0-9]/', '', $row[6]),
                        'honor_pnbp' => preg_replace('/[^0-9]/', '', $row[7]),
                        'biaya_pendaftaran_peserta' => preg_replace('/[^0-9]/', '', $row[8]),
                        'total_biaya_pendaftaran_peserta' => preg_replace('/[^0-9]/', '', $row[9]),
                        'penagihan_foto' => preg_replace('/[^0-9]/', '', $row[10]),
                        'penagihan_atk' => preg_replace('/[^0-9]/', '', $row[11]),
                        'penagihan_snack' => preg_replace('/[^0-9]/', '', $row[12]),
                        'penagihan_makan_siang' => preg_replace('/[^0-9]/', '', $row[13]),
                        'penagihan_laundry' => preg_replace('/[^0-9]/', '', $row[14]),
                        'jumlah_biaya' => preg_replace('/[^0-9]/', '', str_replace(',00', '', $row[15])),
                        'profit' => preg_replace('/[^0-9]/', '', $row[16]),
                    ]);
                } else {
                    Log::warning('Skipped row due to missing date: ' . implode(', ', $row));
                }
            }

            fclose($handle);

            // Clean up CSV file if it was converted
            if ($extension !== 'csv') {
                unlink($csvFilePath);
            }

        } catch (\Exception $e) {
            // Log or handle the exception as needed
            Log::error('Error processing the file: ' . $e->getMessage());
        }
    }

    private function isDate($value)
    {
        $date = \DateTime::createFromFormat('d-M-y', $value);
        return $date ? $date->format('Y-m-d') : false;
    }
}
