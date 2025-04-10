<?php

namespace App\Imports;

use App\Models\Error_log_import;
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
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Events\AfterImport;
use App\Models\Notification;
use App\Models\Penlat_alias;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class InfografisImport implements ToCollection, SkipsEmptyRows, WithBatchInserts, WithChunkReading, ShouldQueue, WithStartRow, WithEvents
{
    protected $filePath;
    protected $userId;

    public function __construct($filePath, String $userId)
    {
        $this->filePath = $filePath;
        $this->userId = $userId;
    }
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        try {
            DB::beginTransaction();

            // Loop through the rows and save the data to the database
            foreach ($rows as $index => $row) { // Use $index to track the loop iteration

                $startRow = $this->startRow(); // Get the starting row number

                if (empty($row[2]) || empty($row[11]) || empty($row[12]) || empty($row[13]) || empty($row[14])) {
                    continue; // Skip rows with empty required fields
                }

                // Check for a valid batch format (must contain a slash '/')
                if (strpos($row[10], '/') === false) {
                    // Log the error with meaningful details
                    Error_log_import::create([
                        'description' => 'Invalid row or the batch does not exist: ' . $row[10],
                        'row' => $index + $startRow, // Calculate the actual row number
                        'import_id' => 1, // Replace with the dynamic value for import_id
                        'user_id' => $this->userId, // Use the dynamic user ID
                    ]);
                    continue;
                }

                Infografis_peserta::updateOrCreate(
                    [
                        'batch' => trim($row[10]),
                        'tgl_pelaksanaan' => $this->getFormattedDate($row[12]),
                        'registration_number' => $row[7],
                    ],
                    [
                        'nama_peserta' => $this->cleanString($row[2]), // Clean the input
                        'nama_program' => $this->cleanString($row[11]),
                        'tempat_pelaksanaan' => $this->cleanString($row[13]),
                        'jenis_pelatihan' => $this->cleanString($row[14]),
                        'keterangan' => $this->cleanString($row[16]),
                        'subholding' => $this->cleanString($row[17]),
                        'perusahaan' => $this->cleanString($row[18]),
                        'kategori_program' => $this->cleanString($row[19]),
                        'realisasi' => $this->cleanString($row[20]),
                        'seafarer_code' => $row[8],
                        'participant_id' => $row[1],
                        'birth_place' => $this->cleanString($row[3]),
                        'birth_date' => $this->getFormattedDate($row[4]),
                        'harga_pelatihan' => (int) preg_replace('/[^0-9]/', '', round($row[15])),
                        'tgl_pendaftaran' => $this->getFormattedDate($row[5]),
                        'isDuplicate' => false
                    ]
                );

                if (strpos($row[10], '/') !== false) {
                    // Check if the batch already exists
                    $checkBatch = Penlat_batch::where('batch', trim($row[10]))->exists();
                    if (!$checkBatch) {
                        // Get penlat
                        $parts = explode('/', trim($row[10]));
                        $firstWord = $parts[0];

                        $checkPenlat = Penlat_alias::where('alias', $firstWord)->exists();
                        if ($checkPenlat) {
                            $getPenlat = Penlat_alias::where('alias', $firstWord)->first();
                            Penlat_batch::updateOrCreate(
                                [
                                    'batch' => trim($row[10]),
                                ],
                                [
                                    'penlat_id' => $getPenlat->penlat->id,
                                    'nama_program' => $getPenlat->penlat->description,
                                    'date' => $this->getFormattedDate($row[12])
                                ]
                            );
                        }
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            Cache::forget('jobs_processing');
            Log::error('Error processing the file: ' . $e->getMessage());
        }
    }

    private function cleanString($string)
    {
        $string = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', mb_convert_encoding($string, 'UTF-8', 'ISO-8859-1'));
        $string = preg_replace('/\s+/', ' ', $string); // Normalize spaces
        return trim($string);
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

    protected function encrypt($value)
    {
        $encryptedVal = Crypt::encryptString($value);
        return $encryptedVal;
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

    /**
     * Register the events.
     *
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterImport::class => function(AfterImport $event) {
                // Clear the cache after the import process
                Cache::forget('jobs_processing');

                // Create Notification
                Notification::create([
                    'description' => 'Realisasi Excel has been imported successfully',
                    'readStat' => 0,
                    'user_id' => $this->userId,
                ]);
                // Delete the file from the file system
                if (file_exists($this->filePath)) {
                    unlink($this->filePath);
                }
            },
        ];
    }
}
