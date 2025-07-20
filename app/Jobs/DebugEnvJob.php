<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class DebugEnvJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        file_put_contents('/tmp/env_worker.txt', print_r($_ENV, true));
        file_put_contents('/tmp/server_worker.txt', print_r($_SERVER, true));
    }
}