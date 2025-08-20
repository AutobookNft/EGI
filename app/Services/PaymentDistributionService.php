<?php

namespace App\Services;

use App\Models\PaymentDistribution;
use App\Models\Reservation;
use App\Models\UserActivity;
use App\Models\Wallet;
use App\Enums\PaymentDistribution\UserTypeEnum;
use App\Enums\PaymentDistribution\DistributionStatusEnum;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * @Oracode Service: Payment Distribution Service
 * 🎯 Purpose: Automated payment distribution system with GDPR compliance
 * 🛡️ Privacy: GDPR-compliant activity logging with UEM/ULM integration
 * 🧱 Core Logic: Calculate and create distributions from collection wallets
 *
 * @package App\Services
 * @author GitHub Copilot for Fabio Cherici
 * @version 1.0.0
 * @date 2025-08-20
 */
class PaymentDistributionService
{
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;

    /**
     * Constructor with UEM/ULM dependency injection
     *
     * @param UltraLogManager $logger
     * @param ErrorManagerInterface $errorManager
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
    }
        /**
     * Create distributions for a reservation (treating reservations as virtual payments)
     * GDPR Compliance: All activities logged with user_activities for audit trail
     *
     * @param Reservation $reservation
     * @return array
     */
    public function createDistributionsForReservation(Reservation $reservation): array
    {
        try {
            // Start database transaction
            return DB::transaction(function () use ($reservation) {
                // Validate reservation
                $this->validateReservationForDistribution($reservation);

                // Get collection and validate wallets
                $collection = $reservation->egi->collection;

                // Log operation start
                if ($this->logger) {
                    $this->logger->info('[Payment Distribution] Starting distribution creation', [
                        'reservation_id' => $reservation->id,
                        'collection_id' => $collection->id,
                        'amount_eur' => $reservation->amount_eur,
                        'user_id' => auth()->id(),
                        'timestamp' => now()->toIso8601String()
                    ]);
                }

                // Get all wallets for the collection
                $wallets = $this->getCollectionWallets($collection);

                // Calculate distributions for all wallets
                $distributionsData = $this->calculateDistributions($reservation, $wallets);

                // Create distribution records in database
                $distributions = $this->createDistributionRecords($distributionsData);

                // Log GDPR-compliant user activities
                $this->logUserActivities($reservation, $distributions);

                // Log completion
                if ($this->logger) {
                    $this->logger->info('[Payment Distribution] Distribution creation completed', [
                        'reservation_id' => $reservation->id,
                        'total_distributions' => $distributions->count(),
                        'total_amount' => $distributions->sum('amount_eur')
                    ]);
                }

                return $distributions->toArray();
            });
        } catch (\Exception $e) {
            // Handle error with UEM
            $this->errorManager->handle('PAYMENT_DISTRIBUTION_ERROR', [
                'reservation_id' => $reservation->id,
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'operation' => 'createDistributionsForReservation',
                'timestamp' => now()->toIso8601String(),
                'error' => $e->getMessage()
            ], $e);

            throw $e;
        }
    }

    /**
     * Validate that reservation is eligible for distribution
     * Note: All reservations are treated as virtual payments, only 'highest' rank counts for stats
     *
     * @param Reservation $reservation
     * @throws \Exception
     */
    private function validateReservationForDistribution(Reservation $reservation): void
    {
        // Check if reservation is active (current system only uses 'active' status)
        if ($reservation->status !== 'active') {
            $this->errorManager->handle('RESERVATION_NOT_ACTIVE', [
                'reservation_id' => $reservation->id,
                'current_status' => $reservation->status,
                'user_id' => auth()->id(),
                'timestamp' => now()->toIso8601String()
            ]);
            throw new \Exception("Reservation {$reservation->id} is not active");
        }

        if (!$reservation->amount_eur || $reservation->amount_eur <= 0) {
            $this->errorManager->handle('INVALID_AMOUNT', [
                'reservation_id' => $reservation->id,
                'amount_eur' => $reservation->amount_eur,
                'user_id' => auth()->id(),
                'timestamp' => now()->toIso8601String()
            ]);
            throw new \Exception("Reservation {$reservation->id} has invalid amount_eur");
        }

        if (!$reservation->egi || !$reservation->egi->collection) {
            $this->errorManager->handle('COLLECTION_NOT_FOUND', [
                'reservation_id' => $reservation->id,
                'user_id' => auth()->id(),
                'timestamp' => now()->toIso8601String()
            ]);
            throw new \Exception("Reservation {$reservation->id} has no associated collection");
        }

        // Check if distributions already exist
        $existingDistributions = PaymentDistribution::where('reservation_id', $reservation->id)->count();
        if ($existingDistributions > 0) {
            if ($this->logger) {
                $this->logger->warning('[Payment Distribution] Distributions already exist', [
                    'reservation_id' => $reservation->id,
                    'existing_distributions' => $existingDistributions
                ]);
            }
            throw new \Exception("Distributions already exist for reservation {$reservation->id}");
        }
    }

        /**
     * Get collection wallets with validation
     *
     * @param \App\Models\Collection $collection
     * @return \Illuminate\Database\Eloquent\Collection<Wallet>
     * @throws \Exception
     */
    private function getCollectionWallets(\App\Models\Collection $collection)
    {
        $wallets = $collection->wallets()->with('user')->get();

        if ($wallets->isEmpty()) {
            $this->errorManager->handle('NO_WALLETS_FOUND', [
                'collection_id' => $collection->id,
                'user_id' => auth()->id(),
                'timestamp' => now()->toIso8601String()
            ]);
            throw new \Exception("No wallets found for collection {$collection->id}");
        }

        // Validate total mint percentages sum to 100%
        $totalMintPercentage = $wallets->sum('royalty_mint');
        if ($totalMintPercentage != 100) {
            $this->errorManager->handle('INVALID_MINT_PERCENTAGES', [
                'collection_id' => $collection->id,
                'current_percentage' => $totalMintPercentage,
                'expected_percentage' => 100,
                'user_id' => auth()->id(),
                'timestamp' => now()->toIso8601String()
            ]);
            throw new \Exception("Collection {$collection->id} wallet percentages don't sum to 100% (current: {$totalMintPercentage}%)");
        }

        return $wallets;
    }

    /**
     * Calculate distributions based on wallet percentages
     *
     * @param Reservation $reservation
     * @param \Illuminate\Database\Eloquent\Collection<Wallet> $wallets
     * @return array
     */
    private function calculateDistributions(Reservation $reservation, $wallets): array
    {
        $distributions = [];
        $totalAmount = $reservation->amount_eur;
        $exchangeRate = $reservation->payment_exchange_rate ?? 1.0;

        foreach ($wallets as $wallet) {
            $percentage = $wallet->royalty_mint;
            $amount = round(($totalAmount * $percentage) / 100, 2);

            // Determine user type from wallet platform_role and user data
            $userType = $this->determineUserType($wallet);

            $distributions[] = [
                'reservation_id' => $reservation->id,
                'collection_id' => $reservation->egi->collection_id,
                'user_id' => $wallet->user_id,
                'user_type' => $userType,
                'percentage' => $percentage,
                'amount_eur' => $amount,
                'exchange_rate' => $exchangeRate,
                'is_epp' => $this->isEppWallet($wallet),
                'distribution_status' => DistributionStatusEnum::PENDING,
                'metadata' => [
                    'wallet_id' => $wallet->id,
                    'wallet_address' => $wallet->wallet,
                    'platform_role' => $wallet->platform_role,
                    'calculation_timestamp' => now()->toISOString(),
                    'reservation_type' => $reservation->type,
                    'reservation_rank' => $reservation->rank,
                    'is_highest_rank' => $reservation->rank === 1,
                    'counts_for_stats' => $reservation->rank === 1, // Solo rank #1 conta per statistiche
                ]
            ];
        }

        return $distributions;
    }

    /**
     * Determine user type from wallet and user data
     *
     * @param Wallet $wallet
     * @return UserTypeEnum
     */
    private function determineUserType(Wallet $wallet): UserTypeEnum
    {
        // Priority 1: Platform role mapping
        if ($wallet->platform_role === 'EPP') {
            return UserTypeEnum::EPP;
        }

        if ($wallet->platform_role === 'Creator') {
            return UserTypeEnum::CREATOR;
        }

        // Priority 2: User type from user model
        if ($wallet->user && $wallet->user->usertype) {
            $usertype = $wallet->user->usertype;

            return match ($usertype) {
                'weak' => UserTypeEnum::WEAK,
                'creator' => UserTypeEnum::CREATOR,
                'collector' => UserTypeEnum::COLLECTOR,
                'commissioner' => UserTypeEnum::COMMISSIONER,
                'company' => UserTypeEnum::COMPANY,
                'epp' => UserTypeEnum::EPP,
                'trader-pro' => UserTypeEnum::TRADER_PRO,
                'vip' => UserTypeEnum::VIP,
                default => UserTypeEnum::COLLECTOR, // Default fallback
            };
        }

        // Default fallback
        return UserTypeEnum::COLLECTOR;
    }

    /**
     * Check if wallet is EPP-related
     *
     * @param Wallet $wallet
     * @return bool
     */
    private function isEppWallet(Wallet $wallet): bool
    {
        return $wallet->platform_role === 'EPP' ||
               ($wallet->user && $wallet->user->usertype === 'epp');
    }

    /**
     * Create distribution records in database
     *
     * @param array $distributions
     * @return \Illuminate\Database\Eloquent\Collection<PaymentDistribution>
     */
    private function createDistributionRecords(array $distributions)
    {
        $createdDistributions = collect();

        foreach ($distributions as $distributionData) {
            $distribution = PaymentDistribution::create($distributionData);
            $createdDistributions->push($distribution);
        }

        return $createdDistributions;
    }

    /**
     * Log GDPR-compliant user activities for each distribution
     *
     * @param Reservation $reservation
     * @param \Illuminate\Database\Eloquent\Collection<PaymentDistribution> $distributions
     */
    private function logUserActivities(Reservation $reservation, $distributions): void
    {
        foreach ($distributions as $distribution) {
            try {
                UserActivity::create([
                    'user_id' => $distribution->user_id,
                    'action' => 'payment_distribution_created',
                    'category' => 'blockchain_activity',
                    'context' => [
                        'reservation_id' => $reservation->id,
                        'collection_id' => $distribution->collection_id,
                        'distribution_id' => $distribution->id,
                        'amount_eur' => $distribution->amount_eur,
                        'percentage' => $distribution->percentage,
                        'user_type' => $distribution->user_type->value,
                        'is_epp' => $distribution->is_epp,
                        'transaction_type' => 'payment_distribution'
                    ],
                    'metadata' => [
                        'egi_id' => $reservation->egi_id,
                        'original_amount' => $reservation->amount_eur,
                        'exchange_rate' => $distribution->exchange_rate,
                        'distribution_status' => $distribution->distribution_status->value,
                        'source' => 'PaymentDistributionService'
                    ],
                    'privacy_level' => 'high', // Financial data requires high privacy
                    'ip_address' => request()->ip() ?? '127.0.0.1',
                    'user_agent' => request()->userAgent() ?? 'System/PaymentDistributionService',
                    'expires_at' => now()->addYears(7), // GDPR retention for financial records
                ]);

            } catch (\Exception $e) {
                // Log error but don't fail the whole process - use UEM with non-blocking error
                $this->errorManager->handle('USER_ACTIVITY_LOGGING_FAILED', [
                    'user_id' => $distribution->user_id,
                    'distribution_id' => $distribution->id,
                    'operation' => 'logUserActivities',
                    'error' => $e->getMessage(),
                    'timestamp' => now()->toIso8601String()
                ], $e);
            }
        }
    }

    /**
     * Get distribution statistics for analytics
     * Note: Only 'highest' rank reservations count for real statistics
     *
     * @param \App\Models\Collection $collection
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getDistributionStats(\App\Models\Collection $collection, ?string $startDate = null, ?string $endDate = null): array
    {
        $query = PaymentDistribution::where('collection_id', $collection->id)
            ->whereHas('reservation', function ($q) {
                $q->where('rank', 1); // Only count #1 highest reservations for stats
            });

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        $distributions = $query->with(['user', 'reservation'])->get();

        return [
            'total_distributions' => $distributions->count(),
            'total_amount_eur' => $distributions->sum('amount_eur'),
            'epp_distributions' => $distributions->where('is_epp', true)->count(),
            'epp_amount_eur' => $distributions->where('is_epp', true)->sum('amount_eur'),
            'by_user_type' => $distributions->groupBy('user_type')->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'amount_eur' => $group->sum('amount_eur'),
                ];
            }),
            'by_status' => $distributions->groupBy('distribution_status')->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'amount_eur' => $group->sum('amount_eur'),
                ];
            }),
        ];
    }

    /**
     * Get ALL distribution tracking (including non-highest ranks)
     * Use this for complete audit trail, not for statistics
     *
     * @param \App\Models\Collection $collection
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getAllDistributionTracking(\App\Models\Collection $collection, ?string $startDate = null, ?string $endDate = null): array
    {
        $query = PaymentDistribution::where('collection_id', $collection->id);

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        $distributions = $query->with(['user', 'reservation'])->get();

        return [
            'total_tracking_entries' => $distributions->count(),
            'highest_rank_count' => $distributions->whereHas('reservation', fn($q) => $q->where('rank', 1))->count(),
            'other_ranks_count' => $distributions->whereHas('reservation', fn($q) => $q->where('rank', '>', 1))->count(),
            'total_virtual_amount' => $distributions->sum('amount_eur'),
            'by_rank' => $distributions->groupBy('reservation.rank')->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'amount_eur' => $group->sum('amount_eur'),
                ];
            }),
        ];
    }
}
