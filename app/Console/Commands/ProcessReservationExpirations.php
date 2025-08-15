<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use App\Services\ReservationService;
use App\Services\Notifications\ReservationNotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * @package App\Console\Commands
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 2.0.0 (FlorenceEGI - Pre-Launch Queue System)
 * @date 2025-08-15
 * @purpose Process reservation rankings and send notifications for rank changes
 */
class ProcessReservationRankings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservations:process-rankings
                            {--dry-run : Run without making changes}
                            {--verbose : Show detailed output}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update reservation rankings and notify users of changes';

    /**
     * Services
     */
    protected ReservationService $reservationService;
    protected ReservationNotificationService $notificationService;
    protected ErrorManagerInterface $errorManager;
    protected UltraLogManager $logger;

    /**
     * Counters for reporting
     */
    protected array $stats = [
        'rankings_updated' => 0,
        'notifications_sent' => 0,
        'superseded' => 0,
        'new_highest' => 0,
        'errors' => 0,
    ];

    /**
     * Create a new command instance.
     */
    public function __construct(
        ReservationService $reservationService,
        ReservationNotificationService $notificationService,
        ErrorManagerInterface $errorManager,
        UltraLogManager $logger
    ) {
        parent::__construct();

        $this->reservationService = $reservationService;
        $this->notificationService = $notificationService;
        $this->errorManager = $errorManager;
        $this->logger = $logger;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');
        $isVerbose = $this->option('verbose');

        $this->info('🔄 Processing reservation rankings' . ($isDryRun ? ' (DRY RUN)' : ''));

        // Log start
        $this->logger->info('[CRON] Reservation ranking processing started', [
            'dry_run' => $isDryRun,
            'timestamp' => now()->toIso8601String(),
        ]);

        try {
            // Get all EGIs with active reservations
            $egisWithReservations = Reservation::active()
                ->select('egi_id')
                ->distinct()
                ->pluck('egi_id');

            foreach ($egisWithReservations as $egiId) {
                $this->processEgiRankings($egiId, $isDryRun, $isVerbose);
            }

            // Output statistics
            $this->outputStatistics();

            // Log completion
            $this->logger->info('[CRON] Reservation ranking processing completed', [
                'stats' => $this->stats,
                'dry_run' => $isDryRun,
            ]);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Error processing rankings: ' . $e->getMessage());

            $this->errorManager->handle('RESERVATION_RANKING_CRON_ERROR', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'stats' => $this->stats,
            ], $e);

            return Command::FAILURE;
        }
    }

    /**
     * Process rankings for a specific EGI
     */
    protected function processEgiRankings(int $egiId, bool $isDryRun, bool $isVerbose): void
    {
        if ($isVerbose) {
            $this->info("  📊 Processing EGI #{$egiId}");
        }

        try {
            // Get current rankings
            $currentRankings = Reservation::active()
                ->forEgi($egiId)
                ->orderBy('rank_position')
                ->get()
                ->keyBy('id');

            // Calculate new rankings
            $newRankings = Reservation::active()
                ->forEgi($egiId)
                ->orderByDesc('amount_eur')
                ->orderBy('created_at')
                ->get();

            $changes = [];
            $previousHighest = $currentRankings->where('is_highest', true)->first();

            foreach ($newRankings as $index => $reservation) {
                $newRank = $index + 1;
                $oldRank = $currentRankings->get($reservation->id)?->rank_position;

                if ($oldRank !== $newRank) {
                    $changes[] = [
                        'reservation' => $reservation,
                        'old_rank' => $oldRank,
                        'new_rank' => $newRank,
                        'is_new_highest' => ($newRank === 1 && !$reservation->is_highest),
                    ];
                }
            }

            if (!$isDryRun && count($changes) > 0) {
                $this->applyRankingChanges($changes, $previousHighest, $isVerbose);
            }

            $this->stats['rankings_updated'] += count($changes);

        } catch (\Exception $e) {
            $this->stats['errors']++;

            if ($isVerbose) {
                $this->error("    ❌ Error: " . $e->getMessage());
            }

            $this->errorManager->handle('EGI_RANKING_ERROR', [
                'egi_id' => $egiId,
                'error' => $e->getMessage(),
            ], $e);
        }
    }

    /**
     * Apply ranking changes and send notifications
     */
    protected function applyRankingChanges(array $changes, ?Reservation $previousHighest, bool $isVerbose): void
    {
        DB::transaction(function () use ($changes, $previousHighest, $isVerbose) {
            foreach ($changes as $change) {
                $reservation = $change['reservation'];
                $newRank = $change['new_rank'];

                if ($isVerbose) {
                    $this->line("      - Reservation #{$reservation->id}: Rank {$change['old_rank']} → {$newRank}");
                }

                // Update reservation
                $reservation->previous_rank = $reservation->rank_position;
                $reservation->rank_position = $newRank;

                // Handle new highest
                if ($change['is_new_highest']) {
                    $reservation->is_highest = true;
                    $reservation->sub_status = Reservation::SUB_STATUS_HIGHEST;

                    // Send "you're now highest" notification
                    $this->notificationService->sendNewHighest($reservation);
                    $this->stats['new_highest']++;

                    // Handle previous highest
                    if ($previousHighest && $previousHighest->id !== $reservation->id) {
                        $previousHighest->is_highest = false;
                        $previousHighest->sub_status = Reservation::SUB_STATUS_SUPERSEDED;
                        $previousHighest->superseded_by_id = $reservation->id;
                        $previousHighest->superseded_at = now();
                        $previousHighest->save();

                        // Send "you've been superseded" notification
                        $this->notificationService->sendSuperseded($previousHighest, $reservation);
                        $this->stats['superseded']++;
                    }
                } else {
                    // Just a rank change
                    if ($reservation->rank_position === 1) {
                        $reservation->is_highest = true;
                        $reservation->sub_status = Reservation::SUB_STATUS_HIGHEST;
                    } else {
                        $reservation->is_highest = false;
                        if ($reservation->sub_status === Reservation::SUB_STATUS_HIGHEST) {
                            $reservation->sub_status = Reservation::SUB_STATUS_SUPERSEDED;
                        }
                    }
                }

                $reservation->save();

                // Send rank change notification (if significant)
                if (abs($change['old_rank'] - $newRank) >= 2) {
                    $this->notificationService->sendRankChanged($reservation, $change['old_rank'], $newRank);
                    $this->stats['notifications_sent']++;
                }
            }
        });
    }

    /**
     * Output statistics
     */
    protected function outputStatistics(): void
    {
        $this->newLine();
        $this->info('📊 Processing Statistics:');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Rankings Updated', $this->stats['rankings_updated']],
                ['New Highest Offers', $this->stats['new_highest']],
                ['Superseded Offers', $this->stats['superseded']],
                ['Notifications Sent', $this->stats['notifications_sent']],
                ['Errors', $this->stats['errors']],
            ]
        );
    }
}
