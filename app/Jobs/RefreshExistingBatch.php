<?php

namespace App\Jobs;

use App\Models\Penlat_alias;
use App\Models\Penlat_batch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RefreshExistingBatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Start the DB transaction
        DB::beginTransaction();

        try {
            $getData = Penlat_batch::whereNull('penlat_id')->get();
            // Delete aliases that don't have a relation to Penlat Table
            Penlat_alias::whereDoesntHave('penlat')->delete();

            foreach ($getData as $row) {
                // Extract the alias part from 'batch' column value
                $parts = explode('/', $row->batch);
                $firstWord = trim($parts[0]);

                // Check if it has '-REN' suffix and adjust the alias accordingly
                if (str_ends_with($firstWord, '-REN')) {
                    $alias = explode('-', $firstWord)[0]; // Get the part before "-REN"
                } else {
                    $alias = $firstWord;
                }

                // Check if the alias exists in the Penlat table
                $checkPenlat = Penlat_alias::where('alias', $alias)->exists();
                if ($checkPenlat) {
                    // Fetch the matching Penlat record
                    $getPenlat = Penlat_alias::where('alias', $alias)->first();

                    // Update or create the record in the Penlat_batch table
                    Penlat_batch::updateOrCreate(
                        [
                            'batch' => $row->batch, // The unique batch identifier
                        ],
                        [
                            'penlat_id' => $getPenlat->penlat->id,
                            'nama_program' => $getPenlat->penlat->description,
                            'date' => $row->date // Replace 'date_column' with the actual date field in Infografis_peserta
                        ]
                    );
                }
            }

            // Commit the transaction
            DB::commit();
        } catch (\Exception $e) {
            // Rollback the transaction on error
            DB::rollBack();

            // Log the error or handle it
            Log::error('Batch update failed: ' . $e->getMessage());
        }
    }
    /**
     * Helper function to format the date.
     * Modify this as per your required format.
     *
     * @param string $date
     * @return string
     */
    protected function getFormattedDate($date)
    {
        return \Carbon\Carbon::parse($date)->format('Y-m-d');
    }
}
