<?php

namespace App\Jobs;

use App\Models\Feedback_mtc;
use Carbon\Carbon;
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

class ImportFeedbackMTC implements ShouldQueue
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

            // Get the first worksheet
            $sheet = $spreadsheet->getActiveSheet();

            // Begin a database transaction
            DB::beginTransaction();

            // Start from the third row (assuming the first two rows are headers)
            foreach ($sheet->getRowIterator(3) as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);

                $rowData = [];
                foreach ($cellIterator as $cell) {
                    $rowData[] = $cell->getValue();
                }

                // Check for required fields
                if (empty($rowData[2]) || empty($rowData[11]) || empty($rowData[12]) || empty($rowData[13]) || empty($rowData[14])) {
                    continue; // Skip rows with empty required fields
                }

                // Convert the date format from d/m/Y to Y-m-d
                $tglPelaksanaan = \DateTime::createFromFormat('d/m/Y', $rowData[4]);
                $tglPelaksanaanFormatted = $tglPelaksanaan ? $tglPelaksanaan->format('Y-m-d') : now()->format('Y-m-d');

                // Update or create the Feedback_mtc entry
                Feedback_mtc::updateOrCreate(
                    [
                        'nama_peserta' => $rowData[1],
                        'judul_pelatihan' => $rowData[2],
                        'tempat_pelaksanaan' => $rowData[3],
                        'tgl_pelaksanaan' => $tglPelaksanaanFormatted,
                        'email_peserta' => $rowData[5],
                        'relevansi_materi' => $rowData[6],
                        'manfaat_training' => $rowData[7],
                        'durasi_training' => $rowData[8],
                        'sistematika_penyajian' => $rowData[9],
                        'tujuan_tercapai' => $rowData[10],
                        'kedisiplinan_steward' => $rowData[11],
                        'fasilitasi_steward' => $rowData[12],
                        'layanan_pelaksana' => $rowData[13],
                        'proses_administrasi' => $rowData[14],
                        'kemudahan_registrasi' => $rowData[15],
                        'kondisi_peralatan' => $rowData[16],
                        'kualitas_boga' => $rowData[17],
                        'media_online' => $rowData[18],
                        'rekomendasi' => $rowData[19],
                        'saran' => $rowData[20]
                    ],
                    []
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
