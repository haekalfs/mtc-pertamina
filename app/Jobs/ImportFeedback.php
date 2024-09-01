<?php

namespace App\Jobs;

use App\Models\Feedback_report;
use App\Models\Feedback_template;
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

class ImportFeedback implements ShouldQueue
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

            $templates = Feedback_template::pluck('id', 'id')->all();

            // Start from the third row (assuming the first two rows are headers)
            foreach ($sheet->getRowIterator(3) as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);

                $rowData = [];
                foreach ($cellIterator as $cell) {
                    $rowData[] = $cell->getValue();
                }

                if (empty($rowData[7]) || empty($rowData[4]) || empty($rowData[10])) {
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
                    $tglPelaksanaan = \DateTime::createFromFormat('d/m/Y', $rowData[7]);
                    $tglPelaksanaanFormatted = $tglPelaksanaan ? $tglPelaksanaan->format('Y-m-d') : now()->format('Y-m-d');

                    Feedback_report::updateOrCreate(
                        [
                            'tgl_pelaksanaan' => $tglPelaksanaanFormatted,
                            'tempat_pelaksanaan' => $rowData[6],
                            'nama' => $rowData[3],
                            'kelompok' => $rowData[5],
                            'judul_pelatihan' => $rowData[4],
                            'instruktur' => $rowData[10],
                            'feedback_template_id' => $templateId,
                        ],
                        [
                            'score' => $rowData[$scoreIndex] ?? null, // Handle cases where score might be missing
                            'updated_at' => now(),
                            'feedback_id' => $uniqueId
                        ]
                    );
                }
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
}
