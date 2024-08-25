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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

class ImportVendorPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath; // Define the property
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

        $csvFilePath = pathinfo($this->filePath, PATHINFO_DIRNAME) . '/' . pathinfo($this->filePath, PATHINFO_FILENAME) . '.csv';

        try {
            // Load the Excel file and convert to CSV
            $reader = IOFactory::createReaderForFile($this->filePath);
            $spreadsheet = $reader->load($this->filePath);
            $writer = new Csv($spreadsheet);
            $writer->save($csvFilePath);

            // Begin a database transaction
            DB::beginTransaction();

            // Open the CSV file for reading
            $handle = fopen($csvFilePath, 'r');

            // Skip the header rows
            fgetcsv($handle);
            fgetcsv($handle);

            // Loop through the rows and save the data to the database
            while (($row = fgetcsv($handle)) !== FALSE) {
                if (empty($row[1]) || empty($row[6]) || empty($row[7])) {
                    break; // Skip rows with empty required fields
                }

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

            fclose($handle);

            // Commit the transaction
            DB::commit();

            // Clean up the CSV file
            unlink($csvFilePath);

        } catch (\Exception $e) {
            // Rollback the transaction if any error occurs
            DB::rollBack();

            // Log the error
            Log::error('Error processing the file: ' . $e->getMessage());
        }
    }
}
