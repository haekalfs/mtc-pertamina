<?php

namespace App\Imports;

use App\Models\Penlat;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;

class PenlatImport implements ToCollection, WithBatchInserts, WithChunkReading, ShouldQueue, WithStartRow
{
    /**
     * Process each row of the Excel file.
     *
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
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
        return 3;
    }
}
