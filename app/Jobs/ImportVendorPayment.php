<?php

namespace App\Jobs;

use App\Models\Feedback_report;
use App\Models\Feedback_template;
use App\Models\Vendor_payment;
use DateTime;
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

class ImportVendorPayment implements ShouldQueue
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
        ini_set('memory_limit', '1024M'); // Adjust memory limit as needed

        try {
            // Load the Excel file
            $reader = IOFactory::createReaderForFile($this->filePath);
            $spreadsheet = $reader->load($this->filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            // Begin a database transaction
            DB::beginTransaction();

            // Skip the header rows (assuming the first two rows are headers)
            foreach ($rows as $index => $row) {
                if ($index < 2) continue;

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

            // Delete the Excel file after processing
            if (file_exists($this->filePath)) {
                unlink($this->filePath);
            }
            Cache::forget('jobs_processing');
        } catch (\Exception $e) {
            // Rollback the transaction if any error occurs
            DB::rollBack();

            // Delete the Excel file after processing
            if (file_exists($this->filePath)) {
                unlink($this->filePath);
            }
            Cache::forget('jobs_processing');
            // Log the error
            Log::error('Error processing the file: ' . $e->getMessage());
        }
    }
}
