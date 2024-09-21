<?php

namespace App\Imports;

use App\Models\Feedback_report;
use App\Models\Feedback_template;
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

class FeedbackImport implements ToCollection, WithBatchInserts, WithChunkReading, ShouldQueue, WithStartRow, WithEvents
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

            $templates = Feedback_template::pluck('id', 'id')->all();

            // Loop through the rows and save the data to the database
            foreach ($rows as $row) {

                if (empty($row[7]) || empty($row[4]) || empty($row[10])) {
                    continue; // Skip rows with empty required fields
                }

                $uniqueId = random_int(1000000000000000, 9999999999999999); // 16-digit random number

                while (Feedback_report::where('feedback_id', $uniqueId)->exists()) {
                    $uniqueId = random_int(1000000000000000, 9999999999999999);
                }

                // Iterate over the template questions
                foreach ($templates as $templateId) {
                    $scoreIndex = 11 + ($templateId - 1); // Adjust index based on template ID

                    // Convert the date format from d/m/Y to Y-m-d
                    $tglPelaksanaan = \DateTime::createFromFormat('d/m/Y', $row[7]);
                    $tglPelaksanaanFormatted = $tglPelaksanaan ? $tglPelaksanaan->format('Y-m-d') : now()->format('Y-m-d');

                    Feedback_report::updateOrCreate(
                        [
                            'tgl_pelaksanaan' => $tglPelaksanaanFormatted,
                            'tempat_pelaksanaan' => $row[6],
                            'nama' => $row[3],
                            'kelompok' => $row[5],
                            'judul_pelatihan' => $row[4],
                            'instruktur' => $row[10],
                            'feedback_template_id' => $templateId,
                        ],
                        [
                            'score' => $row[$scoreIndex] ?? null, // Handle cases where score might be missing
                            'updated_at' => now(),
                            'feedback_id' => $uniqueId
                        ]
                    );
                }
            }

            // Commit the transaction
            DB::commit();

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
                    'description' => 'Feedback Instruktur has been imported successfully',
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
