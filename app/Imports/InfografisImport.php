<?php

namespace App\Imports;

use App\Models\Infografis_peserta;
use App\Models\Penlat;
use App\Models\Penlat_batch;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;

class InfografisImport implements ToCollection, WithBatchInserts, WithChunkReading, ShouldQueue, WithStartRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        try {
            DB::beginTransaction();

            // Loop through the rows and save the data to the database
            foreach ($rows as $row) {

                if (empty($row[2]) || empty($row[11]) || empty($row[12]) || empty($row[13]) || empty($row[14])) {
                    continue; // Skip rows with empty required fields
                }

                Infografis_peserta::updateOrCreate(
                    [
                        'nama_peserta' => $row[2],
                        'nama_program' => $row[11],
                        'batch' => $row[10],
                        'tgl_pelaksanaan' => $this->getFormattedDate($row[12]), // Handle date parsing
                        'tempat_pelaksanaan' => $row[13],
                        'jenis_pelatihan' => $row[14],
                        'keterangan' => $row[16],
                        'subholding' => $row[17],
                        'perusahaan' => $row[18],
                        'kategori_program' => $row[19],
                        'realisasi' => $row[20],
                    ],
                    []
                );

                if (strpos($row[10], '/') !== false) {
                    // Check if the batch already exists
                    $checkBatch = Penlat_batch::where('batch', $row[10])->exists();
                    if (!$checkBatch) {
                        // Get penlat
                        $parts = explode('/', $row[10]);
                        $firstWord = $parts[0];

                        $checkPenlat = Penlat::where('alias', $firstWord)->exists();
                        if ($checkPenlat) {
                            $getPenlat = Penlat::where('alias', $firstWord)->first();
                            Penlat_batch::updateOrCreate(
                                [
                                    'batch' => $row[10],
                                ],
                                [
                                    'penlat_id' => $getPenlat->id,
                                    'nama_program' => $getPenlat->description,
                                    'date' => $this->getFormattedDate($row[12])
                                ]
                            );
                        }
                    }
                }
            }

            DB::commit();
            Cache::forget('jobs_processing');
        } catch (\Exception $e) {
            DB::rollBack();

            Cache::forget('jobs_processing');
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
        return 2;
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