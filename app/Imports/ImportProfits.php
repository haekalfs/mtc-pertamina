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
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Events\AfterImport;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ImportProfits implements ToCollection, SkipsEmptyRows, WithBatchInserts, WithChunkReading, ShouldQueue, WithStartRow, WithCalculatedFormulas, WithEvents
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

            $currentDate = null;

            foreach ($rows as $row) {
                if ($row[0]) {
                    $totalUsageCost = 0;

                    // Step 1: Clean up and separate text and number parts
                    $batch = $this->extractBatch($row[0]);

                    // Step 2: Find or initialize batch record
                    $findBatch = Penlat_batch::where('batch', 'like', "%$batch%")->first();

                    if ($findBatch) {
                        // Get the sum of all 'total' values for the given batch
                        $totalUsageCost = Penlat_utility_usage::where('penlat_batch_id', $findBatch->id)
                            ->sum('total');
                    }

                    // Prepare data for the Profit table
                    $profitData = $this->parseProfitData($row, $currentDate, $totalUsageCost);

                    Profit::updateOrCreate(
                        [
                            'tgl_pelaksanaan' => $currentDate,
                            'pelaksanaan' => $row[0],
                            'jumlah_peserta' => $row[2]
                        ],
                        $profitData
                    );
                } else {
                    Log::warning('Skipped row due to missing or invalid date.');
                }
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing the file: ' . $e->getMessage());
        }
    }

    /**
     * Helper function to extract the batch text and number parts.
     */
    private function extractBatch($cell)
    {
        $cleanedCell = preg_replace('/\s+/', '', $cell);
        preg_match_all('/([A-Za-z-\/]+)(\d+)/', $cleanedCell, $matches, PREG_SET_ORDER);

        // Combine text and number to form batch if match exists
        return $matches ? $matches[0][1] . '/' . $matches[0][2] : null;
    }

    /**
     * Helper function to parse the row and prepare data for Profit table.
     */
    private function parseProfitData($row, $currentDate, $totalUsageCost)
    {
        return [
            'biaya_instruktur' => $this->extractNumeric($row[4]),
            'total_pnbp' => $this->extractNumeric($row[5]),
            'biaya_transportasi_hari' => $this->extractNumeric($row[6]),
            'honor_pnbp' => $this->extractNumeric($row[7]),
            'biaya_pendaftaran_peserta' => $this->extractNumeric($row[8]),
            'total_biaya_pendaftaran_peserta' => $this->extractNumeric($row[9]),
            'penagihan_foto' => $this->extractNumeric($row[10]),
            'penagihan_atk' => $this->extractNumeric($row[11]),
            'penagihan_snack' => $this->extractNumeric($row[12]),
            'penagihan_makan_siang' => $this->extractNumeric($row[13]),
            'penagihan_laundry' => $this->extractNumeric($row[14]),
            'penlat_usage' => $this->extractNumeric($totalUsageCost),
            'jumlah_biaya' => $this->extractNumeric(str_replace(',00', '', $row[15])),
            'profit' => $this->extractNumeric($row[16]),
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
        return 1;
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
