<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\FetchFacebookInsights;
use Illuminate\Foundation\Bus\Dispatchable;  // Add this line

class FetchFacebookInsightsCommand extends Command
{
    // Command name to run via artisan
    protected $signature = 'facebook:fetch-insights';

    // Command description
    protected $description = 'Fetch Facebook insights and dispatch the job to queue';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Dispatch the FetchFacebookInsights job
        FetchFacebookInsights::dispatch();

        // Output a message to confirm the job has been dispatched
        $this->info('Facebook insights fetch job has been dispatched.');
    }
}
