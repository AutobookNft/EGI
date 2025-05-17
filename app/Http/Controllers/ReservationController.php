<?php

namespace App\Http\Controllers;

use App\Models\Egi;
use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Ultra\ErrorManager\Facades\UltraError;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * @Oracode Controller: ReservationController
 * 🎯 Purpose: Handles web and API requests for EGI reservations
 * 🧱 Core Logic: Processes reservation requests, validates inputs
 * 🛡️ GDPR: Ensures proper handling of contact data
 *
 * @package App\Http\Controllers
 * @author Padmin D. Curtis (for Fabio Cherici)
 * @version 1.0.0
 * @date 2025-05-16
 */
class ReservationController extends Controller
{
    /**
     * @var ReservationService
     */
    protected ReservationService $reservationService;

    /**
     * @var UltraLogManager
     */
    protected UltraLogManager $logger;

    /**
     * Constructor with dependency injection
     *
     * @param ReservationService $reservationService
     * @param UltraLogManager $logger
     */
    public function __construct(
        ReservationService $reservationService,
        UltraLogManager $logger
    ) {
        $this->reservationService = $reservationService;
        $this->logger = $logger;
    }

    /**
     * Handle web-based reservation requests
     *
     * @param Request $request The HTTP request
     * @param int $egiId The EGI ID
     * @return \Illuminate\Http\RedirectResponse
     *
     * @privacy-safe Only collects necessary contact data with user consent
     */
    public function reserve(Request $request, int $egiId)
    {
        $this->logger->info('Web reservation request received', [
            'egi_id' => $egiId,
            'user_id' => Auth::id(),
            'session_id' => $request->session()->getId()
        ]);

        // Validate inputs
        $validated = $request->validate([
            'offer_amount_eur' => 'required|numeric|min:1',
            'terms_accepted' => 'required|accepted',
            'contact_data' => 'nullable|array'
        ]);

        try {
            // Find the EGI
            $egi = Egi::findOrFail($egiId);

            // Determine auth state
            $user = Auth::user();
            $walletAddress = null;

            // If not authenticated with a user account, check for a session wallet
            if (!$user && $request->session()->has('connected_wallet')) {
                $walletAddress = $request->session()->get('connected_wallet');
            }

            // Ensure user is authenticated or has a connected wallet
            if (!$user && !$walletAddress) {
                $this->logger->warning('Unauthorized reservation attempt', [
                    'egi_id' => $egiId,
                    'ip' => $request->ip()
                ]);

                return redirect()->back()->with('error', __('reservation.unauthorized'));
            }

            // Create the reservation
            $reservation = $this->reservationService->createReservation(
                [
                    'egi_id' => $egiId,
                    'offer_amount_eur' => $validated['offer_amount_eur'],
                    'contact_data' => $validated['contact_data'] ?? null
                ],
                $user,
                $walletAddress
            );

            // Get the certificate
            $certificate = $reservation->certificate;

            $this->logger->info('Web reservation successful', [
                'reservation_id' => $reservation->id,
                'certificate_uuid' => $certificate?->certificate_uuid,
                'egi_id' => $egiId
            ]);

            // Redirect to the certificate view
            return redirect()->route('egi-certificates.show', $certificate->certificate_uuid)
                ->with('success', __('reservation.success'));

        } catch (\Exception $e) {
            // Log the error
            $this->logger->error('Web reservation failed', [
                'egi_id' => $egiId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Handle the error using UEM
            if (method_exists($e, 'getErrorCode')) {
                $errorCode = $e->getErrorCode();
            } else {
                $errorCode = 'RESERVATION_UNKNOWN_ERROR';
            }

            return redirect()->back()->with('error', __("reservation.errors.{$errorCode}"));
        }
    }

    /**
     * Handle API-based reservation requests
     *
     * @param Request $request The HTTP request
     * @param int $egiId The EGI ID
     * @return JsonResponse
     *
     * @privacy-safe Only collects necessary contact data with user consent
     */
    public function apiReserve(Request $request, int $egiId): JsonResponse
    {
        $this->logger->info('API reservation request received', [
            'egi_id' => $egiId,
            'user_id' => Auth::id()
        ]);

        // Validate inputs
        try {
            $validated = $request->validate([
                'offer_amount_eur' => 'required|numeric|min:1',
                'terms_accepted' => 'required|accepted',
                'contact_data' => 'nullable|array',
                'wallet_address' => 'nullable|string|size:58'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => __('reservation.validation_failed'),
                'errors' => $e->errors()
            ], 422);
        }

        try {
            // Find the EGI
            $egi = Egi::findOrFail($egiId);

            // Determine auth state
            $user = Auth::user();
            $walletAddress = $validated['wallet_address'] ?? null;

            // If no wallet provided and using session, get from session
            if (!$walletAddress && !$user && $request->session()->has('connected_wallet')) {
                $walletAddress = $request->session()->get('connected_wallet');
            }

            // Ensure user is authenticated or has a wallet address
            if (!$user && !$walletAddress) {
                return response()->json([
                    'success' => false,
                    'message' => __('reservation.unauthorized'),
                    'error_code' => 'RESERVATION_UNAUTHORIZED'
                ], 401);
            }

            // Create the reservation
            $reservation = $this->reservationService->createReservation(
                [
                    'egi_id' => $egiId,
                    'offer_amount_eur' => $validated['offer_amount_eur'],
                    'contact_data' => $validated['contact_data'] ?? null
                ],
                $user,
                $walletAddress
            );

            // Get the certificate
            $certificate = $reservation->certificate;

            $this->logger->info('API reservation successful', [
                'reservation_id' => $reservation->id,
                'certificate_uuid' => $certificate?->certificate_uuid,
                'egi_id' => $egiId
            ]);

            // Return success response
            return response()->json([
                'success' => true,
                'message' => __('reservation.success'),
                'reservation' => [
                    'id' => $reservation->id,
                    'type' => $reservation->type,
                    'offer_amount_eur' => $reservation->offer_amount_eur,
                    'offer_amount_algo' => $reservation->offer_amount_algo,
                    'status' => $reservation->status,
                    'is_current' => $reservation->is_current
                ],
                'certificate' => [
                    'uuid' => $certificate->certificate_uuid,
                    'url' => $certificate->getPublicUrlAttribute(),
                    'verification_url' => $certificate->getVerificationUrl(),
                    'pdf_url' => $certificate->getPdfUrl()
                ]
            ]);

        } catch (\Exception $e) {
            // Log the error
            $this->logger->error('API reservation failed', [
                'egi_id' => $egiId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Determine error code and HTTP status
            $errorCode = method_exists($e, 'getErrorCode') ? $e->getErrorCode() : 'RESERVATION_UNKNOWN_ERROR';
            $httpStatus = method_exists($e, 'getHttpStatusCode') ? $e->getHttpStatusCode() : 500;

            // Return error response
            return response()->json([
                'success' => false,
                'message' => __("reservation.errors.{$errorCode}"),
                'error_code' => $errorCode
            ], $httpStatus);
        }
    }

    /**
     * Cancel a reservation
     *
     * @param Request $request The HTTP request
     * @param int $id The reservation ID
     * @return JsonResponse
     */
    public function cancel(Request $request, int $id): JsonResponse
    {
        try {
            $reservation = Reservation::findOrFail($id);

            // Check if user can cancel the reservation
            $user = Auth::user();
            $isOwner = $user && $user->id === $reservation->user_id;
            $hasWallet = $request->session()->has('connected_wallet') &&
                         $reservation->certificate &&
                         $reservation->certificate->wallet_address === $request->session()->get('connected_wallet');

            if (!$isOwner && !$hasWallet) {
                return response()->json([
                    'success' => false,
                    'message' => __('reservation.unauthorized_cancel'),
                    'error_code' => 'RESERVATION_UNAUTHORIZED_CANCEL'
                ], 403);
            }

            // Cancel the reservation
            $result = $this->reservationService->cancelReservation($reservation);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => __('reservation.cancel_success')
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => __('reservation.cancel_failed'),
                    'error_code' => 'RESERVATION_CANCEL_FAILED'
                ], 500);
            }

        } catch (\Exception $e) {
            $this->logger->error('Reservation cancellation failed', [
                'reservation_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('reservation.cancel_failed'),
                'error_code' => 'RESERVATION_CANCEL_FAILED'
            ], 500);
        }
    }

    /**
     * List user's active reservations
     *
     * @param Request $request The HTTP request
     * @return JsonResponse
     *
     * @privacy-safe Returns only minimal data for user's own reservations
     */
    public function listUserReservations(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => __('reservation.auth_required'),
                    'error_code' => 'RESERVATION_AUTH_REQUIRED'
                ], 401);
            }

            $reservations = $this->reservationService->getUserActiveReservations($user);

            // Transform data for API response
            $data = $reservations->map(function ($reservation) {
                return [
                    'id' => $reservation->id,
                    'egi' => [
                        'id' => $reservation->egi->id,
                        'title' => $reservation->egi->title,
                        'collection_id' => $reservation->egi->collection_id,
                        'collection_name' => $reservation->egi->collection->collection_name ?? null
                    ],
                    'type' => $reservation->type,
                    'offer_amount_eur' => $reservation->offer_amount_eur,
                    'offer_amount_algo' => $reservation->offer_amount_algo,
                    'created_at' => $reservation->created_at->toIso8601String(),
                    'certificate' => $reservation->certificate ? [
                        'uuid' => $reservation->certificate->certificate_uuid,
                        'url' => $reservation->certificate->getPublicUrlAttribute(),
                        'pdf_url' => $reservation->certificate->getPdfUrl()
                    ] : null
                ];
            });

            return response()->json([
                'success' => true,
                'count' => $reservations->count(),
                'reservations' => $data
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Failed to list user reservations', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('reservation.list_failed'),
                'error_code' => 'RESERVATION_LIST_FAILED'
            ], 500);
        }
    }

    /**
     * Get reservation status for an EGI
     *
     * @param Request $request The HTTP request
     * @param int $egiId The EGI ID
     * @return JsonResponse
     */
    public function getEgiReservationStatus(Request $request, int $egiId): JsonResponse
    {
        try {
            $egi = Egi::findOrFail($egiId);

            // Get highest priority reservation
            $highestReservation = $this->reservationService->getHighestPriorityReservation($egi);

            // Get count of all active reservations
            $totalReservations = Reservation::where('egi_id', $egiId)
                ->where('status', 'active')
                ->where('is_current', true)
                ->count();

            // Check if current user has a reservation
            $user = Auth::user();
            $walletAddress = $request->session()->get('connected_wallet');

            $userReservation = null;

            if ($user) {
                $userReservation = Reservation::where('egi_id', $egiId)
                    ->where('user_id', $user->id)
                    ->where('status', 'active')
                    ->where('is_current', true)
                    ->first();
            } elseif ($walletAddress) {
                $userReservation = Reservation::whereHas('certificate', function ($query) use ($walletAddress) {
                    $query->where('wallet_address', $walletAddress);
                })
                ->where('egi_id', $egiId)
                ->where('status', 'active')
                ->where('is_current', true)
                ->first();
            }

            // Build response data
            $data = [
                'egi_id' => $egiId,
                'is_reserved' => $highestReservation !== null,
                'total_reservations' => $totalReservations,
                'user_has_reservation' => $userReservation !== null,
                'highest_priority_reservation' => $highestReservation ? [
                    'type' => $highestReservation->type,
                    'offer_amount_eur' => $highestReservation->offer_amount_eur,
                    // Only show if it belongs to current user
                    'belongs_to_current_user' => $userReservation && $userReservation->id === $highestReservation->id
                ] : null,
                'user_reservation' => $userReservation ? [
                    'id' => $userReservation->id,
                    'type' => $userReservation->type,
                    'offer_amount_eur' => $userReservation->offer_amount_eur,
                    'offer_amount_algo' => $userReservation->offer_amount_algo,
                    'is_highest_priority' => $highestReservation && $userReservation->id === $highestReservation->id,
                    'created_at' => $userReservation->created_at->toIso8601String(),
                    'certificate' => $userReservation->certificate ? [
                        'uuid' => $userReservation->certificate->certificate_uuid,
                        'url' => $userReservation->certificate->getPublicUrlAttribute()
                    ] : null
                ] : null
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Failed to get EGI reservation status', [
                'egi_id' => $egiId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('reservation.status_failed'),
                'error_code' => 'RESERVATION_STATUS_FAILED'
            ], 500);
        }
    }
}
