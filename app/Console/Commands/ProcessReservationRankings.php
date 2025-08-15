<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reservation;
use App\Models\Egi;
use App\Services\Notifications\ReservationNotificationService;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;
use Illuminate\Support\Facades\DB;

class ProcessReservationRankings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservations:process-rankings
                            {--egi= : Process specific EGI ID}
                            {--dry-run : Run without sending notifications}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process reservation rankings and send notifications for position changes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get dependencies
        $notificationService = app(ReservationNotificationService::class);
        $logger = app(UltraLogManager::class);
        $errorManager = app(ErrorManagerInterface::class);

        $startTime = microtime(true);
        $dryRun = $this->option('dry-run');
        $verbose = $this->output->isVerbose(); // Usa il verbose di Laravel
        $specificEgi = $this->option('egi');

        $this->info('🚀 Starting reservation rankings processing...');

        if ($dryRun) {
            $this->warn('⚠️  DRY RUN MODE - No notifications will be sent');
        }

        $logger->info('[PROCESS_RANKINGS] Command started', [
            'dry_run' => $dryRun,
            'specific_egi' => $specificEgi,
            'started_at' => now()->toIso8601String()
        ]);

        try {
            // Get EGIs to process
            $egisQuery = Egi::query()
                ->whereHas('reservations', function ($query) {
                    $query->where('status', 'active');
                });

            if ($specificEgi) {
                $egisQuery->where('id', $specificEgi);
            }

            $egis = $egisQuery->get();

            if ($egis->isEmpty()) {
                $this->info('No EGIs with active reservations found.');
                return Command::SUCCESS;
            }

            $this->info("Processing {$egis->count()} EGI(s) with active reservations...");

            $totalProcessed = 0;
            $totalNotifications = 0;
            $errors = 0;

            foreach ($egis as $egi) {
                try {
                    $result = $this->processEgiRankings(
                        $egi,
                        $dryRun,
                        $verbose,
                        $notificationService,
                        $logger,
                        $errorManager
                    );

                    $totalProcessed += $result['processed'];
                    $totalNotifications += $result['notifications'];

                    if ($verbose) {
                        $this->info("  ✓ EGI #{$egi->id} - {$egi->title}: {$result['processed']} reservations, {$result['notifications']} notifications");
                    }

                } catch (\Exception $e) {
                    $errors++;
                    $this->error("  ✗ Error processing EGI #{$egi->id}: {$e->getMessage()}");

                    $errorManager->handle('PROCESS_RANKINGS_EGI_ERROR', [
                        'egi_id' => $egi->id,
                        'error_message' => $e->getMessage()
                    ], $e);
                }
            }

            $executionTime = round(microtime(true) - $startTime, 2);

            // Summary
            $this->newLine();
            $this->info('📊 Processing Complete!');
            $this->table(
                ['Metric', 'Value'],
                [
                    ['EGIs Processed', $egis->count()],
                    ['Reservations Updated', $totalProcessed],
                    ['Notifications Sent', $dryRun ? "0 (dry run)" : $totalNotifications],
                    ['Errors', $errors],
                    ['Execution Time', "{$executionTime}s"],
                ]
            );

            $logger->info('[PROCESS_RANKINGS] Command completed', [
                'egis_processed' => $egis->count(),
                'reservations_updated' => $totalProcessed,
                'notifications_sent' => $totalNotifications,
                'errors' => $errors,
                'execution_time_seconds' => $executionTime
            ]);

            return $errors > 0 ? Command::FAILURE : Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Fatal error: ' . $e->getMessage());

            $errorManager->handle('PROCESS_RANKINGS_FATAL_ERROR', [
                'error_message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ], $e);

            return Command::FAILURE;
        }
    }

    /**
     * Update rankings for a specific EGI
     *
     * @param int $egiId
     * @return void
     */
    private function updateRankingsForEgi(int $egiId): void
    {
        // Get all active reservations for this EGI ordered by amount DESC
        $reservations = Reservation::where('egi_id', $egiId)
            ->where('status', 'active')
            ->where('is_current', true)
            ->orderBy('amount_eur', 'desc')
            ->orderBy('created_at', 'asc') // Tie-breaker: older reservations win
            ->get();

        // Update rank positions
        $rank = 1;
        foreach ($reservations as $reservation) {
            $reservation->update([
                'rank_position' => $rank,
                'is_highest' => ($rank === 1)
            ]);
            $rank++;
        }
    }

    /**
     * Process rankings for a specific EGI
     */
    private function processEgiRankings(
        Egi $egi,
        bool $dryRun,
        bool $verbose,
        ReservationNotificationService $notificationService,
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ): array {
        $processed = 0;
        $notifications = 0;

        DB::transaction(function () use ($egi, $dryRun, $verbose, &$processed, &$notifications, $notificationService, $logger) {
            // Store original rankings
            $originalRankings = Reservation::active()
                ->forEgi($egi->id)
                ->pluck('rank_position', 'id')
                ->toArray();

            // Update rankings for this EGI
            $this->updateRankingsForEgi($egi->id);

            // Get updated reservations
            $reservations = Reservation::active()
                ->forEgi($egi->id)
                ->ranked()
                ->get();

            $processed = $reservations->count();

            if ($verbose) {
                $this->info("  Processing {$processed} reservations for EGI #{$egi->id}");
            }

            // Process each reservation for notifications
            foreach ($reservations as $reservation) {
                $oldRank = $originalRankings[$reservation->id] ?? null;
                $newRank = $reservation->rank_position;

                // Skip if rank hasn't changed
                if ($oldRank === $newRank) {
                    continue;
                }

                if ($verbose) {
                    $this->info("    Reservation #{$reservation->id}: Rank {$oldRank} → {$newRank}");
                }

                if (!$dryRun) {
                    try {
                        // New highest bidder
                        if ($newRank === 1 && $oldRank !== 1) {
                            $notificationService->sendNewHighest($reservation);
                            $notifications++;

                            if ($verbose) {
                                $this->info("      → Sent 'New Highest' notification");
                            }
                        }

                        // Was highest, now superseded
                        if ($oldRank === 1 && $newRank !== 1) {
                            $newHighest = $reservations->firstWhere('rank_position', 1);
                            if ($newHighest) {
                                $notificationService->sendSuperseded($reservation, $newHighest);
                                $notifications++;

                                if ($verbose) {
                                    $this->info("      → Sent 'Superseded' notification");
                                }
                            }
                        }

                        // Significant rank change (3+ positions)
                        if ($oldRank && abs($newRank - $oldRank) >= 3) {
                            $notificationService->sendRankChanged($reservation, $oldRank);
                            $notifications++;

                            if ($verbose) {
                                $direction = $newRank < $oldRank ? 'improved' : 'dropped';
                                $this->info("      → Sent 'Rank Changed' notification ({$direction})");
                            }
                        }

                    } catch (\Exception $e) {
                        $this->error("      ✗ Failed to send notification: {$e->getMessage()}");

                        $logger->error('[PROCESS_RANKINGS] Notification failed', [
                            'reservation_id' => $reservation->id,
                            'old_rank' => $oldRank,
                            'new_rank' => $newRank,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
        });

        return [
            'processed' => $processed,
            'notifications' => $notifications
        ];
    }
}
