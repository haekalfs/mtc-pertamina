<?php

namespace App\Imports;

use App\Models\Penlat;
use App\Models\Penlat_batch;
use App\Models\Profit;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ImportProfits implements ToCollection, WithBatchInserts, WithChunkReading, ShouldQueue, WithStartRow, WithCalculatedFormulas
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        try {
            DB::beginTransaction();

            $currentDate = null;
            // Loop through the rows and save the data to the database
            foreach ($rows as $row) {
                // Try to check if it's a date in 'd-M-Y' format (displayed format)
                if ($this->getFormattedDate($row[0])) {
                    $currentDate = $this->getFormattedDate($row[0]);
                }

                if (strpos($row[0], '/') !== false) {

                    // Get penlat
                    $parts = explode('/', $row[0]);
                    $firstWord = $parts[0];

                    // Make sure the batch doesn't already exist
                    $checkBatch = Penlat_batch::where('batch', $row[0])->exists();
                    if (!$checkBatch) {

                        $checkPenlat = Penlat::where('alias', $firstWord)->exists();
                        if($checkPenlat){
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
                    Log::warning('Skipped row due to missing or invalid date:');
                }
            }

            DB::commit();

            Cache::forget('jobs_processing');
        } catch (\Exception $e) {
            DB::rollBack();

            Cache::forget('jobs_processing');
            // Log or handle the exception as needed
            Log::error('Error processing the file: ' . $e->getMessage());
        }
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function startRow(): int
    {
        return 1;
    }

    /**
     * Check if the given value is a valid date in the 'd-M-y' format.
     */
    private function isDate($value, $format)
    {
        $date = \DateTime::createFromFormat($format, $value);
        return $date && $date->format($format) === $value;
    }

    // Define a method to handle date parsing safely
    protected function getFormattedDate($dateValue)
    {
        // Check if the value is numeric (meaning it's an Excel date)
        if (is_numeric($dateValue)) {
            // Convert Excel serial date to DateTime object
            return Date::excelToDateTimeObject($dateValue)->format('Y-m-d');
        }

        // If it's not numeric, assume it's in the format j-M-y and parse with Carbon
        try {
            return Carbon::createFromFormat('j-M-y', $dateValue)->format('Y-m-d');
        } catch (\Exception $e) {
            // Handle exception if date format is invalid
            return null; // Or handle as needed (e.g., return a default date or throw an error)
        }
    }
}