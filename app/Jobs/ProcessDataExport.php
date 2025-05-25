<?php

namespace App\Jobs;

use App\Models\DataExport;
use App\Services\Gdpr\DataExportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * @Oracode Job: Process Data Export
 * 🎯 Purpose: Background processing of GDPR data exports
 * 🛡️ Privacy: Processes user data exports securely
 * 🧱 Core Logic: Handles large export generation asynchronously
 *
 * @package App\Jobs
 * @author Padmin D. Curtis (for Fabio Cherici)
 * @version 1.0.0
 */
class ProcessDataExport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The data export to process
     * @var DataExport
     */
    protected DataExport $export;

    /**
     * Job timeout in seconds (30 minutes)
     * @var int
     */
    public int $timeout = 1800;

    /**
     * Number of times the job may be attempted
     * @var int
     */
    public int $tries = 3;

    /**
     * Create a new job instance
     *
     * @param DataExport $export
     * @privacy-safe Job processes specified export only
     */
    public function __construct(DataExport $export)
    {
        $this->export = $export;
        $this->onQueue('exports');
    }

    /**
     * Execute the job
     *
     * @param DataExportService $exportService
     * @param UltraLogManager $logger
     * @param ErrorManagerInterface $errorManager
     * @return void
     * @privacy-safe Processes export with full audit trail
     */
    public function handle(
        DataExportService $exportService,
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ): void {
        try {
            $logger->info('Processing data export job started', [
                'export_id' => $this->export->id,
                'user_id' => $this->export->user_id,
                'format' => $this->export->format,
                'log_category' => 'EXPORT_JOB_PROCESSING'
            ]);

            $exportService->processDataExport($this->export);

            $logger->info('Data export job completed successfully', [
                'export_id' => $this->export->id,
                'user_id' => $this->export->user_id,
                'log_category' => 'EXPORT_JOB_SUCCESS'
            ]);

        } catch (\Exception $e) {
            $logger->error('Data export job failed', [
                'export_id' => $this->export->id,
                'user_id' => $this->export->user_id,
                'error' => $e->getMessage(),
                'log_category' => 'EXPORT_JOB_ERROR'
            ]);

            $errorManager->handle('DATA_EXPORT_JOB_FAILED', [
                'export_id' => $this->export->id,
                'user_id' => $this->export->user_id,
                'error' => $e->getMessage()
            ], $e);

            throw $e;
        }
    }

    /**
     * Handle a job failure
     *
     * @param \Throwable $exception
     * @return void
     * @privacy-safe Updates export status on failure
     */
    public function failed(\Throwable $exception): void
    {
        $this->export->update([
            'status' => 'failed',
            'error_message' => $exception->getMessage(),
            'failed_at' => now()
        ]);
    }
}
