<?php

namespace App\Imports;

use App\Models\Notification;
use App\Models\Penlat;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Jobs\AfterImportJob;

class PenlatImport implements ToCollection, WithBatchInserts, WithChunkReading, ShouldQueue, WithStartRow, WithEvents
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

            foreach ($rows as $row) {
                if (empty($row[60]) || empty($row[68]) || empty($row[69]) || empty($row[70])) {
                    break;
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

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
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
        return 3;
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
                    'description' => 'List Penlat has been imported successfully',
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
