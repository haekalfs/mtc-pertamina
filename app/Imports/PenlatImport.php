<?php

namespace App\Imports;

use App\Models\Notification;
use App\Models\Penlat;
use App\Models\Penlat_alias;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Jobs\AfterImportJob;

class PenlatImport implements ToCollection, SkipsEmptyRows, WithBatchInserts, WithChunkReading, ShouldQueue, WithStartRow, WithEvents
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
                // Check if required fields are empty, if so, skip the row
                if (empty($row[60]) || empty($row[68]) || empty($row[69]) || empty($row[70])) {
                    continue;  // Use continue to skip the current iteration and move to the next row
                }

                // Update or create Penlat record
                $penlat = Penlat::updateOrCreate(
                    [
                        'description' => $row[60],
                        'jenis_pelatihan' => $row[69],
                        'kategori_pelatihan' => $row[70],
                    ],
                    []
                );

                // Process aliases (remove spaces and trim)
                $alias = str_replace(' ', '-', trim($row[68]));

                // Check if alias already exists for the given penlat_id
                $existingAlias = Penlat_alias::where('penlat_id', $penlat->id)
                    ->where('alias', $alias)
                    ->first();

                // If alias does not exist, save it
                if (!$existingAlias) {
                    $penlatAlias = new Penlat_alias();
                    $penlatAlias->penlat_id = $penlat->id;  // Associate alias with Penlat ID
                    $penlatAlias->alias = $alias;  // Save the alias
                    $penlatAlias->save();
                }
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
