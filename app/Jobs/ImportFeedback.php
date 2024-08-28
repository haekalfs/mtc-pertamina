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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

class ImportFeedback implements ShouldQueue
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

            $templates = Feedback_template::pluck('id', 'id')->all();

            // Loop through each row in the CSV
            while (($row = fgetcsv($handle)) !== false) {
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
                    $tglPelaksanaan = DateTime::createFromFormat('d/m/Y', $row[7]);
                    $tglPelaksanaanFormatted = $tglPelaksanaan ? $tglPelaksanaan->format('Y-m-d') : today()->format('Y-m-d');

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
