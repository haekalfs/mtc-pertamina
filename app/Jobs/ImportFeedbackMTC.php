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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

class ImportFeedbackMTC implements ShouldQueue
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


            // Loop through each row in the CSV
            while (($row = fgetcsv($handle)) !== FALSE) {
                if (empty($row[2]) || empty($row[11]) || empty($row[12]) || empty($row[13]) || empty($row[14])) {
                    continue; // Skip rows with empty required fields
                }

                $tglPelaksanaan = DateTime::createFromFormat('d/m/Y', $row[4]);
                $tglPelaksanaanFormatted = $tglPelaksanaan ? $tglPelaksanaan->format('Y-m-d') : today()->format('Y-m-d');

                Feedback_mtc::updateOrCreate(
                    ['nama_peserta' => $row[1], 'judul_pelatihan' => $row[2]],
                    [
                        'nama_peserta' => $row[1],
                        'judul_pelatihan' => $row[2],
                        'tempat_pelaksanaan' => $row[3],
                        'tgl_pelaksanaan' => $tglPelaksanaanFormatted,
                        'email_peserta' => $row[5],
                        'relevansi_materi' => $row[6],
                        'manfaat_training' => $row[7],
                        'durasi_training' => $row[8],
                        'sistematika_penyajian' => $row[9],
                        'tujuan_tercapai' => $row[10],
                        'kedisiplinan_steward' => $row[11],
                        'fasilitasi_steward' => $row[12],
                        'layanan_pelaksana' => $row[13],
                        'proses_administrasi' => $row[14],
                        'kemudahan_registrasi' => $row[15],
                        'kondisi_peralatan' => $row[16],
                        'kualitas_boga' => $row[17],
                        'media_online' => $row[18],
                        'rekomendasi' => $row[19],
                        'saran' => $row[20]
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
