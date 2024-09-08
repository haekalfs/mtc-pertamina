<?php

namespace App\Imports;

use App\Models\Feedback_mtc;
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
use App\Models\Notification;

class FeedbackMTCImport implements ToCollection, WithBatchInserts, WithChunkReading, ShouldQueue, WithStartRow, WithEvents
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

            // Loop through the rows and save the data to the database
            foreach ($rows as $row) {
                // Check for required fields
                if (empty($row[2]) || empty($row[11]) || empty($row[12]) || empty($row[13]) || empty($row[14])) {
                    continue; // Skip rows with empty required fields
                }

                // Convert the date format from d/m/Y to Y-m-d
                $tglPelaksanaan = \DateTime::createFromFormat('d/m/Y', $row[4]);
                $tglPelaksanaanFormatted = $tglPelaksanaan ? $tglPelaksanaan->format('Y-m-d') : now()->format('Y-m-d');

                // Update or create the Feedback_mtc entry
                Feedback_mtc::updateOrCreate(
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
                    ],
                    []
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
                    'description' => 'Feedback Pelatihan has been imported successfully',
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
