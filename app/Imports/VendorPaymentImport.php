<?php

namespace App\Imports;

use App\Models\Vendor_payment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Events\AfterImport;
use App\Models\Notification;

class VendorPaymentImport implements ToCollection, WithBatchInserts, WithChunkReading, ShouldQueue, WithStartRow, WithCalculatedFormulas, WithEvents
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
            // Begin a database transaction
            DB::beginTransaction();

            // Skip the header rows (assuming the first two rows are headers)
            foreach ($rows as $row) {

                // Skip rows with empty required fields
                if (empty($row[1]) || empty($row[6]) || empty($row[7])) {
                    continue;
                }

                // Insert or update the Vendor_payment records
                Vendor_payment::updateOrCreate(
                    [
                        'tanggal_terima_dokumen_invoice' => $row[1],
                        'jenis_vendor' => $row[6],
                        'vendor' => $row[7],
                        'nomor_vendor' => $row[8],
                        'uraian' => $row[9],
                        'wapu' => $row[10],
                        'kode_pajak' => $row[11],
                        'no_invoice' => $row[12],
                    ],
                    [
                        'nilai' => $row[13],
                        'pajak' => $row[14],
                        'management_fee' => $row[15],
                        'no_req_id' => $row[19],
                        'no_req_release' => $row[17],
                        'no_pr' => $row[18],
                        'no_po' => $row[20],
                        'no_sa_gr' => $row[21],
                        'no_req_payment_approval' => $row[22],
                        'no_req_bmc' => $row[23],
                        'tanggal_kirim_ke_edoc_ssc' => $row[24],
                        'keterangan' => $row[25],
                        'tanggal_terbayarkan' => $row[26],
                    ]
                );
            }

            // Commit the transaction
            DB::commit();

            Cache::forget('jobs_processing');
        } catch (\Exception $e) {
            // Rollback the transaction if any error occurs
            DB::rollBack();

            Cache::forget('jobs_processing');
            // Log the error
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
                    'description' => 'Vendor Payments has been imported successfully',
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
