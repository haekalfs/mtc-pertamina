<?php

namespace App\Jobs;

use App\Models\Penlat;
use App\Models\Penlat_batch;
use App\Models\Profit;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
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
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            DB::beginTransaction();

            // Loop through the rows and save the data to the database
            foreach ($rows as $index => $row) {
                if ($index == 0) continue; // Skip the header row

                // Check if the row contains a date in the first column
                if ($this->isDate($row[0])) {
                    // Convert the date to 'Y-m-d' format
                    $currentDate = Carbon::createFromFormat('d-M-y', $row[0])->format('Y-m-d');
                }

                if (strpos($row[0], '/') !== false) {
                    // Get penlat
                    $parts = explode('/', $row[0]);
                    $firstWord = $parts[0];

                    // Make sure the batch doesn't already exist
                    $checkBatch = Penlat_batch::where('batch', $row[0])->exists();
                    if (!$checkBatch) {
                        $getPenlat = Penlat::where('alias', $firstWord)->first();
                        Penlat_batch::updateOrCreate(
                            [
                                'batch' => $row[0],
                            ],
                            [
                                'penlat_id' => $getPenlat->id,
                                'nama_program' => $getPenlat->description,
                                'date' => $currentDate,
                            ]
                        );
                    }

                    Profit::updateOrCreate(
                        [
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
                        ],
                        []
                    );
                } else {
                    Log::warning('Skipped row due to missing or invalid date: ' . implode(', ', $row));
                }
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

    /**
     * Check if the given value is a valid date in the 'd-M-y' format.
     */
    private function isDate($value)
    {
        $date = \DateTime::createFromFormat('d-M-y', $value);
        return $date ? $date->format('Y-m-d') : false;
    }

    /**
     * Sanitize the given number by removing any non-numeric characters.
     */
    private function sanitizeNumber($value)
    {
        return preg_replace('/[^0-9]/', '', str_replace(',00', '', $value));
    }
}
