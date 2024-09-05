<?php

namespace App\Jobs;

use App\Models\Infografis_peserta;
use App\Models\Penlat;
use App\Models\Penlat_batch;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

class ImportParticipantInfographics implements ShouldQueue
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
        try {
            // Load the Excel file
            $reader = IOFactory::createReaderForFile($this->filePath);
            $spreadsheet = $reader->load($this->filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            DB::beginTransaction();

            // Loop through the rows and save the data to the database
            foreach ($rows as $index => $row) {
                if ($index == 0) continue; // Skip the header row

                if (empty($row[2]) || empty($row[11]) || empty($row[12]) || empty($row[13]) || empty($row[14])) {
                    continue; // Skip rows with empty required fields
                }

                Infografis_peserta::updateOrCreate(
                    [
                        'nama_peserta' => $row[2],
                        'nama_program' => $row[11],
                        'batch' => $row[10],
                        'tgl_pelaksanaan' => Carbon::createFromFormat('j-M-y', $row[12])->format('Y-m-d'),
                        'tempat_pelaksanaan' => $row[13],
                        'jenis_pelatihan' => $row[14],
                        'keterangan' => $row[16],
                        'subholding' => $row[17],
                        'perusahaan' => $row[18],
                        'kategori_program' => $row[19],
                        'realisasi' => $row[20],
                    ],
                    []
                );

                if (strpos($row[10], '/') !== false) {
                    // Check if the batch already exists
                    $checkBatch = Penlat_batch::where('batch', $row[10])->exists();
                    if (!$checkBatch) {
                        // Get penlat
                        $parts = explode('/', $row[10]);
                        $firstWord = $parts[0];

                        $checkPenlat = Penlat::where('alias', $firstWord)->exists();
                        if ($checkPenlat) {
                            $getPenlat = Penlat::where('alias', $firstWord)->first();
                            Penlat_batch::updateOrCreate(
                                [
                                    'batch' => $row[10],
                                ],
                                [
                                    'penlat_id' => $getPenlat->id,
                                    'nama_program' => $getPenlat->description,
                                    'date' => Carbon::createFromFormat('j-M-y', $row[12])->format('Y-m-d')
                                ]
                            );
                        }
                    }
                }
            }

            DB::commit();

            // Delete the Excel file after processing
            if (file_exists($this->filePath)) {
                unlink($this->filePath);
            }

            Cache::forget('jobs_processing');
        } catch (\Exception $e) {
            DB::rollBack();

            // Delete the Excel file after processing
            if (file_exists($this->filePath)) {
                unlink($this->filePath);
            }
            Cache::forget('jobs_processing');
            // Log or handle the exception as needed
            Log::error('Error processing the file: ' . $e->getMessage());
        }
    }
}
