<?php

namespace App\Services;

use App\Models\Egi;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Ultra\ErrorManager\Facades\UltraError;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * @Oracode Service: ReservationService
 * 🎯 Purpose: Manages the business logic for EGI reservations
 * 🧱 Core Logic: Creates, updates, and manages priorities of reservations
 * 🛡️ GDPR: Handles minimal data necessary for reservation management
 *
 * @package App\Services
 * @author Padmin D. Curtis (for Fabio Cherici)
 * @version 1.0.0
 * @date 2025-05-16
 */
class ReservationService
{
    /**
     * @var UltraLogManager
     */
    protected UltraLogManager $logger;

    /**
     * @var CertificateGeneratorService
     */
    protected CertificateGeneratorService $certificateGenerator;

    /**
     * @var CurrencyService
     */
    protected CurrencyService $currencyService;

    /**
     * Constructor with dependency injection
     *
     * @param UltraLogManager $logger
     * @param CertificateGeneratorService $certificateGenerator
     * @param CurrencyService $currencyService
     */
    public function __construct(
        UltraLogManager $logger,
        CertificateGeneratorService $certificateGenerator,
        CurrencyService $currencyService
    ) {
        $this->logger = $logger;
        $this->certificateGenerator = $certificateGenerator;
        $this->currencyService = $currencyService;
    }

    /**
     * Create a new reservation for an EGI
     *
     * @param array $data The reservation data
     * @param User|null $user The user making the reservation (null for wallet-only)
     * @param string|null $walletAddress The wallet address for weak auth users
     * @return Reservation|null
     * @throws \Exception If the reservation cannot be created
     *
     * @privacy-safe Collects only necessary data for reservation
     */
    public function createReservation(array $data, ?User $user = null, ?string $walletAddress = null): ?Reservation
    {
        // Start a transaction to ensure data consistency
        return DB::transaction(function () use ($data, $user, $walletAddress) {
            $egi = Egi::findOrFail($data['egi_id']);

            // Check if EGI is available for reservation
            if (!$this->canReserveEgi($egi)) {
                $this->logger->warning('Attempted to reserve unavailable EGI', [
                    'egi_id' => $egi->id,
                    'user_id' => $user?->id,
                    'wallet' => $walletAddress ?? ($user?->wallet ?? 'unknown')
                ]);

                throw UltraError::handle('RESERVATION_EGI_NOT_AVAILABLE', [
                    'egi_id' => $egi->id
                ]);
            }

            // Determine reservation type based on user authentication
            $reservationType = $user && !$user->is_weak_auth ? 'strong' : 'weak';

            // Ensure we have either a user or a wallet address
            if (!$user && !$walletAddress) {
                throw UltraError::handle('RESERVATION_UNAUTHORIZED', [
                    'message' => 'Either user or wallet address is required'
                ]);
            }

            // Convert EUR amount to ALGO
            $offerAmountEur = (float) $data['offer_amount_eur'];
            $offerAmountAlgo = $this->currencyService->convertEurToAlgo($offerAmountEur);

            // Create the reservation
            $reservation = new Reservation([
                'user_id' => $user?->id,
                'egi_id' => $egi->id,
                'type' => $reservationType,
                'status' => 'active',
                'offer_amount_eur' => $offerAmountEur,
                'offer_amount_algo' => $offerAmountAlgo,
                'is_current' => true,
                'contact_data' => $data['contact_data'] ?? null
            ]);

            $reservation->save();

            // Process existing reservations to maintain priority
            $this->processReservationPriorities($reservation);

            // Generate a certificate for the reservation
            $certificateData = [
                'wallet_address' => $walletAddress ?? $user->wallet ?? 'unknown'
            ];

            $this->certificateGenerator->generateCertificate($reservation, $certificateData);

            $this->logger->info('New reservation created', [
                'reservation_id' => $reservation->id,
                'egi_id' => $egi->id,
                'type' => $reservationType,
                'offer_amount_eur' => $offerAmountEur
            ]);

            return $reservation;
        });
    }

    /**
     * Check if an EGI can be reserved
     *
     * @param Egi $egi The EGI to check
     * @return bool Whether the EGI can be reserved
     */
    public function canReserveEgi(Egi $egi): bool
    {
        // Check if EGI is published or if it's already minted
        return ($egi->is_published || $egi->status === 'published') && !$egi->mint;
    }

    /**
     * Process reservation priorities to maintain consistency
     *
     * @param Reservation $newReservation The new reservation to process
     * @return void
     */
    public function processReservationPriorities(Reservation $newReservation): void
    {
        // Get all active reservations for the same EGI
        $existingReservations = Reservation::where('egi_id', $newReservation->egi_id)
            ->where('id', '!=', $newReservation->id)
            ->where('is_current', true)
            ->where('status', 'active')
            ->get();

        foreach ($existingReservations as $existingReservation) {
            // If new reservation has higher priority, mark existing as superseded
            if ($this->hasHigherPriority($newReservation, $existingReservation)) {
                $existingReservation->markAsSuperseded($newReservation);

                $this->logger->info('Reservation superseded', [
                    'new_reservation_id' => $newReservation->id,
                    'superseded_reservation_id' => $existingReservation->id
                ]);
            }
            // If existing has higher priority, mark the new one as superseded
            elseif ($this->hasHigherPriority($existingReservation, $newReservation)) {
                $newReservation->markAsSuperseded($existingReservation);

                $this->logger->info('New reservation superseded by existing', [
                    'new_reservation_id' => $newReservation->id,
                    'higher_priority_reservation_id' => $existingReservation->id
                ]);

                // No need to check other reservations since this one is already superseded
                break;
            }
            // If equal priority, first come first served (existing wins)
            elseif ($existingReservation->created_at->lt($newReservation->created_at)) {
                $newReservation->markAsSuperseded($existingReservation);

                $this->logger->info('New reservation superseded by older with equal priority', [
                    'new_reservation_id' => $newReservation->id,
                    'older_reservation_id' => $existingReservation->id
                ]);

                // No need to check other reservations
                break;
            }
        }
    }

    /**
     * Determine if one reservation has higher priority than another
     *
     * @param Reservation $a First reservation
     * @param Reservation $b Second reservation
     * @return bool Whether a has higher priority than b
     */
    public function hasHigherPriority(Reservation $a, Reservation $b): bool
    {
        // Strong reservations always have priority over weak ones
        if ($a->type === 'strong' && $b->type === 'weak') {
            return true;
        }

        if ($a->type === 'weak' && $b->type === 'strong') {
            return false;
        }

        // If same type, higher offer amount wins
        if ($a->offer_amount_eur > $b->offer_amount_eur) {
            return true;
        }

        if ($a->offer_amount_eur < $b->offer_amount_eur) {
            return false;
        }

        // If same amount, older reservation wins (handled outside this method)
        return false;
    }

    /**
     * Get the highest priority reservation for an EGI
     *
     * @param Egi $egi The EGI to check
     * @return Reservation|null The highest priority reservation or null if none
     */
    public function getHighestPriorityReservation(Egi $egi): ?Reservation
    {
        // First look for strong reservations
        $strongReservation = $egi->reservations()
            ->where('type', 'strong')
            ->where('is_current', true)
            ->where('status', 'active')
            ->orderBy('offer_amount_eur', 'desc')
            ->orderBy('created_at', 'asc')
            ->first();

        if ($strongReservation) {
            return $strongReservation;
        }

        // If no strong reservations, look for weak ones
        return $egi->reservations()
            ->where('type', 'weak')
            ->where('is_current', true)
            ->where('status', 'active')
            ->orderBy('offer_amount_eur', 'desc')
            ->orderBy('created_at', 'asc')
            ->first();
    }

    /**
     * Get all active reservations for a user
     *
     * @param User $user The user
     * @return Collection Collection of active reservations
     */
    public function getUserActiveReservations(User $user): Collection
    {
        return Reservation::where('user_id', $user->id)
            ->where('is_current', true)
            ->where('status', 'active')
            ->with(['egi', 'certificate'])
            ->get();
    }

    /**
     * Get reservation history for an EGI
     *
     * @param Egi $egi The EGI
     * @return Collection Collection of all reservations for the EGI
     */
    public function getReservationHistory(Egi $egi): Collection
    {
        return Reservation::where('egi_id', $egi->id)
            ->with(['user', 'certificate'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Cancel a reservation
     *
     * @param Reservation $reservation The reservation to cancel
     * @return bool Whether the cancellation was successful
     */
    public function cancelReservation(Reservation $reservation): bool
    {
        // Update the reservation status
        $reservation->status = 'cancelled';
        $reservation->is_current = false;
        $result = $reservation->save();

        if ($result) {
            $this->logger->info('Reservation cancelled', [
                'reservation_id' => $reservation->id,
                'egi_id' => $reservation->egi_id,
                'user_id' => $reservation->user_id
            ]);

            // If this reservation superseded others, we need to reprocess priorities
            $supersededReservations = Reservation::where('superseded_by_id', $reservation->id)
                ->get();

            if ($supersededReservations->isNotEmpty()) {
                // Reactivate superseded reservations
                foreach ($supersededReservations as $supersededReservation) {
                    $supersededReservation->is_current = true;
                    $supersededReservation->superseded_by_id = null;
                    $supersededReservation->save();

                    // Reactivate certificate if it exists
                    if ($supersededReservation->certificate) {
                        $supersededReservation->certificate->is_superseded = false;
                        $supersededReservation->certificate->is_current_highest = true;
                        $supersededReservation->certificate->save();
                    }
                }

                // Reprocess priorities among the reactivated reservations
                $this->reprocessPrioritiesAfterCancellation($reservation->egi_id);
            }
        }

        return $result;
    }

    /**
     * Reprocess priorities after a reservation cancellation
     *
     * @param int $egiId The EGI ID
     * @return void
     */
    private function reprocessPrioritiesAfterCancellation(int $egiId): void
    {
        $activeReservations = Reservation::where('egi_id', $egiId)
            ->where('is_current', true)
            ->where('status', 'active')
            ->orderBy('type', 'desc') // 'strong' comes before 'weak'
            ->orderBy('offer_amount_eur', 'desc')
            ->orderBy('created_at', 'asc')
            ->get();

        if ($activeReservations->isEmpty()) {
            return;
        }

        // The first reservation in the sorted list has the highest priority
        $highestPriority = $activeReservations->shift();

        // Mark all other reservations as superseded by the highest priority one
        foreach ($activeReservations as $reservation) {
            $reservation->markAsSuperseded($highestPriority);
        }

        $this->logger->info('Priorities reprocessed after cancellation', [
            'egi_id' => $egiId,
            'highest_priority_reservation_id' => $highestPriority->id
        ]);
    }
}
