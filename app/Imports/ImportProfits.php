<?php

namespace App\Imports;

use App\Models\Notification;
use App\Models\Penlat;
use App\Models\Penlat_alias;
use App\Models\Penlat_batch;
use App\Models\Penlat_utility_usage;
use App\Models\Profit;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\HasReferencesToOtherSheets;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Events\AfterImport;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ImportProfits implements ToCollection, SkipsEmptyRows, WithBatchInserts, WithChunkReading, ShouldQueue, WithStartRow, WithEvents, HasReferencesToOtherSheets
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
                $totalUsageCost = 0;

                // Step 2: Find or initialize batch record
                $findBatch = Penlat_batch::where('batch', $row[1])->first();

                if ($findBatch) {
                    // Get the sum of all 'total' values for the given batch
                    $totalUsageCost = Penlat_utility_usage::where('penlat_batch_id', $findBatch->id)
                        ->sum('total');

                    // Prepare data for the Profit table
                    $profitData = $this->parseProfitData($row, $totalUsageCost);

                    Profit::updateOrCreate(
                        [
                            'tgl_pelaksanaan' => $findBatch->date,
                            'pelaksanaan' => $row[1],
                            'jumlah_peserta' => $row[2]
                        ],
                        $profitData
                    );
                }
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing the file: ' . $e->getMessage());
        }
    }

    /**
     * Helper function to parse the row and prepare data for Profit table.
     */
    private function parseProfitData($row, $totalUsageCost)
    {
        return [
            'biaya_instruktur' => $this->extractNumeric($row[4]),
            'total_pnbp' => $this->extractNumeric($row[5]),
            'biaya_transportasi_hari' => $this->extractNumeric($row[6]),
            'total_biaya_pendaftaran_peserta' => $this->extractNumeric($row[3]),
            'penagihan_foto' => $this->extractNumeric($row[7]),
            'penagihan_atk' => $this->extractNumeric($row[8]),
            'penagihan_snack' => $this->extractNumeric($row[9]),
            'penagihan_makan_siang' => $this->extractNumeric($row[10]),
            'penagihan_laundry' => $this->extractNumeric($row[11]),
            'penlat_usage' => $this->extractNumeric($totalUsageCost),
            'jumlah_biaya' => $this->extractNumeric(str_replace(',00', '', $row[12])),
            'profit' => $this->extractNumeric($row[13]),
        ];
    }

    /**
     * Helper function to remove non-numeric characters and return numeric value.
     */
    private function extractNumeric($value)
    {
        return (int) preg_replace('/[^0-9]/', '', $value);
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
                    'description' => 'Profits has been imported successfully',
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
