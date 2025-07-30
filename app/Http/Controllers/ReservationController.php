<?php

namespace App\Http\Controllers;

use App\Helpers\FegiAuth;
use App\Models\Egi;
use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;
use Illuminate\Support\Facades\Session;

/**
 * ReservationController OS2.0 - UEM/ULM Orchestrated
 *
 * Handles EGI reservation requests with UEM for errors and ULM for operational logging.
 * Uses existing error codes from config and creates new ones only when needed.
 *
 * @author Padmin D. Curtis OS2.0 (for Fabio Cherici)
 * @version 2.1.0-oracode-corrected
 * @package App\Http\Controllers
 */
class ReservationController extends Controller
{
    protected ReservationService $reservationService;
    protected ErrorManagerInterface $errorManager;
    protected UltraLogManager $logger;

    public function __construct(
        ReservationService $reservationService,
        ErrorManagerInterface $errorManager,
        UltraLogManager $logger
    ) {
        $this->reservationService = $reservationService;
        $this->errorManager = $errorManager;
        $this->logger = $logger;
    }

    /**
     * Handle web-based reservation requests
     *
     * @param Request $request
     * @param int $egiId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reserve(Request $request, int $egiId)
    {
        $this->logger->info('[RESERVATION_WEB_ATTEMPT] Web reservation attempt started', [
            'egi_id' => $egiId,
            'user_id' => FegiAuth::id(),
            'session_id' => $request->session()->getId()
        ]);

        $user = FegiAuth::user();
        if (!$user) {
            $this->logger->warning('Unauthenticated like attempt on EGI', [
                'egi_id' => $egiId,
                'user_id' => FegiAuth::id(),
                'session_id' => $request->session()->getId(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return $this->errorManager->handle('AUTH_REQUIRED', [
                'operation' => 'web_reservation',
                'endpoint' => 'reservations.reserve',
                'user_id' => FegiAuth::id(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'session_id' => $request->session()->getId()
            ]);
        }

        try {
            // Validate inputs
            $validated = $request->validate([
                'offer_amount_eur' => 'required|numeric|min:1',
                'terms_accepted' => 'required|accepted',
                'contact_data' => 'nullable|array'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorManager->handle(
                'VALIDATION_ERROR',
                [
                    'egi_id' => $egiId,
                    'operation' => 'web_reservation',
                    'validation_errors' => $e->errors(),
                    'user_id' => FegiAuth::id(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ],
                $e
            );
        }

        try {
            // Find EGI - wrapped in dedicated try/catch
            $egi = Egi::findOrFail($egiId);

        } catch (\Exception $e) {
            return $this->errorManager->handle('RESERVATION_EGI_NOT_FOUND', [
                    'egi_id' => $egiId,
                    'operation' => 'web_reservation',
                    'user_id' => FegiAuth::id(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ], $e);
        }

        try {
            // Resolve authentication - dedicated try/catch for auth validation
            $user = FegiAuth::user();
            $walletAddress = Session::get('connected_wallet');

            if (!$user && !$walletAddress) {
                // Throw a custom exception to be caught by the outer catch
                throw new \Exception('Unauthorized access: no user or wallet authentication found');
            }

            // Create reservation
            $reservation = $this->reservationService->createReservation(
                [
                    'egi_id' => $egiId,
                    'offer_amount_eur' => $validated['offer_amount_eur'],
                    'contact_data' => $validated['contact_data'] ?? null
                ],
                $user,
                $walletAddress
            );

            // Log success
            $this->logger->info('[RESERVATION_WEB_SUCCESS] Web reservation completed successfully', [
                'reservation_id' => $reservation->id,
                'egi_id' => $egiId,
                'user_id' => FegiAuth::id(),
                'certificate_uuid' => $reservation->certificate?->certificate_uuid,
                'offer_amount_eur' => $reservation->offer_amount_eur
            ]);

            return redirect()->route('egi-certificates.show', $reservation->certificate->certificate_uuid)
                ->with('success', __('reservation.success'));

        } catch (\Exception $e) {
            // Handle unauthorized access and any other reservation creation errors
            if (str_contains($e->getMessage(), 'Unauthorized access')) {
                return $this->errorManager->handle(
                    'RESERVATION_UNAUTHORIZED',
                    [
                        'egi_id' => $egiId,
                        'has_user' => FegiAuth::user() !== null,
                        'has_wallet' => Session::has('connected_wallet'),
                        'operation' => 'web_reservation',
                        'user_id' => FegiAuth::id(),
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent()
                    ],
                    $e
                );
            }

            // Check if it's a known reservation exception
            $errorCode = method_exists($e, 'getErrorCode') ? $e->getErrorCode() : 'RESERVATION_UNKNOWN_ERROR';

            return $this->errorManager->handle(
                $errorCode,
                [
                    'egi_id' => $egiId,
                    'operation' => 'web_reservation',
                    'exception_class' => get_class($e),
                    'user_id' => FegiAuth::id(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'error' => $e->getMessage()
                ],
                $e
            );
        }
    }

    /**
     * Handle API-based reservation requests
     *
     * @param Request $request
     * @param int $egiId
     * @return JsonResponse
     */
    public function apiReserve(Request $request, int $egiId): JsonResponse
    {
        $this->logger->info('[RESERVATION_API_ATTEMPT] API reservation attempt started', [
            'egi_id' => $egiId,
            'user_id' => FegiAuth::id(),
            'api_version' => $request->header('API-Version', 'v1'),
            'connected_wallet' => Session::get('connected_wallet', null),
            'session_id' => Session::getId()
        ]);

        $user = FegiAuth::user();
        if (!$user) {
            $this->logger->warning('Unauthenticated like attempt on EGI', [
                'egi_id' => $egiId,
                'user_id' => FegiAuth::id(),
                'session_id' => $request->session()->getId(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return $this->errorManager->handle('AUTH_REQUIRED', [
                'operation' => 'web_reservation',
                'endpoint' => 'reservations.reserve',
                'user_id' => FegiAuth::id(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'session_id' => $request->session()->getId()
            ]);
        }
        try {
            // Validate inputs
            $validated = $request->validate([
                'offer_amount_eur' => 'required|numeric|min:1',
                'terms_accepted' => 'required|accepted',
                'contact_data' => 'nullable|array',
                'wallet_address' => 'nullable|string|size:58'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorManager->handle(
                'VALIDATION_ERROR',
                [
                    'egi_id' => $egiId,
                    'operation' => 'api_reservation',
                    'validation_errors' => $e->errors(),
                    'user_id' => FegiAuth::id(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ],
                $e
            );
        }

        try {
            // Find EGI - dedicated try/catch
            $egi = Egi::findOrFail($egiId);

        } catch (\Exception $e) {
            return $this->errorManager->handle(
                'RESERVATION_EGI_NOT_FOUND',
                [
                    'egi_id' => $egiId,
                    'operation' => 'api_reservation',
                    'user_id' => FegiAuth::id(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ], $e );
        }

        try {
            // Resolve authentication - dedicated try/catch for auth validation
            $user = FegiAuth::user();
            $sessionWallet = Session::get('connected_wallet');
            $walletAddress = $validated['wallet_address'] ?? $sessionWallet;

            if (!$user && !$walletAddress) {
                throw new \Exception('Unauthorized access: no user or wallet authentication found');
            }

            // Create reservation
            $reservation = $this->reservationService->createReservation(
                [
                    'egi_id' => $egiId,
                    'offer_amount_eur' => $validated['offer_amount_eur'],
                    'contact_data' => $validated['contact_data'] ?? null
                ],
                $user,
                $walletAddress
            );

            // Log success
            $this->logger->info('[RESERVATION_API_SUCCESS] API reservation completed successfully', [
                'reservation_id' => $reservation->id,
                'egi_id' => $egiId,
                'user_id' => FegiAuth::id(),
                'certificate_uuid' => $reservation->certificate?->certificate_uuid,
                'offer_amount_eur' => $reservation->offer_amount_eur
            ]);

            return response()->json([
                'success' => true,
                'message' => __('reservation.success'),
                'data' => [
                    'reservation' => [
                        'id' => $reservation->id,
                        'type' => $reservation->type,
                        'offer_amount_eur' => $reservation->offer_amount_eur,
                        'offer_amount_algo' => $reservation->offer_amount_algo,
                        'status' => $reservation->status,
                        'is_current' => $reservation->is_current
                    ],
                    'certificate' => [
                        'uuid' => $reservation->certificate->certificate_uuid,
                        'url' => $reservation->certificate->getPublicUrlAttribute(),
                        'verification_url' => $reservation->certificate->getVerificationUrl(),
                        'pdf_url' => $reservation->certificate->getPdfUrl()
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            // Handle unauthorized access and any other reservation creation errors
            if (str_contains($e->getMessage(), 'Unauthorized access')) {
                return $this->errorManager->handle(
                    'RESERVATION_UNAUTHORIZED',
                    [
                        'egi_id' => $egiId,
                        'operation' => 'api_reservation',
                        'has_user' => FegiAuth::user() !== null,
                        'has_wallet' => Session::has('connected_wallet'),
                        'user_id' => FegiAuth::id(),
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent()
                    ],
                    $e
                );
            }

            $errorCode = method_exists($e, 'getErrorCode') ? $e->getErrorCode() : 'RESERVATION_UNKNOWN_ERROR';

            return $this->errorManager->handle(
                $errorCode,
                [
                    'egi_id' => $egiId,
                    'operation' => 'api_reservation',
                    'exception_class' => get_class($e),
                    'user_id' => FegiAuth::id(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'error' => $e->getMessage()
                ],
                $e
            );
        }
    }

    /**
     * Cancel a reservation
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function cancel(Request $request, int $id): JsonResponse
    {
        $this->logger->info('[RESERVATION_CANCEL_ATTEMPT] Reservation cancellation attempt', [
            'reservation_id' => $id,
            'user_id' => FegiAuth::id()
        ]);

        try {
            $reservation = Reservation::findOrFail($id);

        } catch (\Exception $e) {
            return $this->errorManager->handle(
                'RECORD_NOT_FOUND',
                [
                    'model' => 'Reservation',
                    'id' => $id,
                    'operation' => 'cancel',
                    'user_id' => FegiAuth::id(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ], $e );
        }

        try {
            // Check authorization - dedicated try/catch for auth validation
            $user = FegiAuth::user();
            $isOwner = $user && $user->id === $reservation->user_id;
            $hasWallet = $request->session()->has('connected_wallet') &&
                         $reservation->certificate &&
                         $reservation->certificate->wallet_address === $request->session()->get('connected_wallet');

            if (!$isOwner && !$hasWallet) {
                throw new \Exception('Unauthorized cancellation attempt');
            }

            // Cancel reservation
            $result = $this->reservationService->cancelReservation($reservation);

            if (!$result) {
                throw new \Exception('Reservation cancellation failed in service');
            }

            $this->logger->info('[RESERVATION_CANCEL_SUCCESS] Reservation cancelled successfully', [
                'reservation_id' => $id,
                'user_id' => FegiAuth::id(),
                'egi_id' => $reservation->egi_id
            ]);

            return response()->json([
                'success' => true,
                'message' => __('reservation.cancel_success')
            ]);

        } catch (\Exception $e) {
            if (str_contains($e->getMessage(), 'Unauthorized cancellation')) {
                return $this->errorManager->handle(
                    'RESERVATION_UNAUTHORIZED_CANCEL',
                    [
                        'reservation_id' => $id,
                        'user_id' => FegiAuth::id(),
                        'is_owner' => $user && $user->id === $reservation->user_id,
                        'has_wallet' => $request->session()->has('connected_wallet'),
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent()
                    ],
                    $e
                );
            }

            return $this->errorManager->handle(
                'RESERVATION_CANCEL_FAILED',
                [
                    'reservation_id' => $id,
                    'user_id' => FegiAuth::id(),
                    'exception_class' => get_class($e),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'error' => $e->getMessage()
                ],
                $e
            );
        }
    }

    /**
     * List user's active reservations
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function listUserReservations(Request $request): JsonResponse
    {
        $this->logger->info('[RESERVATION_LIST_ATTEMPT] User reservations list requested', [
            'user_id' => FegiAuth::id()
        ]);

        try {
            $user = FegiAuth::user();

            if (!$user) {
                throw new \Exception('Authentication required for listing reservations');
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

            $this->logger->info('[RESERVATION_LIST_SUCCESS] User reservations retrieved successfully', [
                'user_id' => FegiAuth::id(),
                'count' => $reservations->count()
            ]);

            return response()->json([
                'success' => true,
                'count' => $reservations->count(),
                'reservations' => $data
            ]);

        } catch (\Exception $e) {
            if (str_contains($e->getMessage(), 'Authentication required')) {
                return $this->errorManager->handle(
                    'AUTH_REQUIRED',
                    [
                        'operation' => 'list_reservations',
                        'endpoint' => 'reservations.list',
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent()
                    ],
                    $e
                );
            }

            return $this->errorManager->handle(
                'RESERVATION_LIST_FAILED',
                [
                    'user_id' => FegiAuth::id(),
                    'exception_class' => get_class($e),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'error' => $e->getMessage()
                ],
                $e
            );
        }
    }

    /**
     * Get reservation status for an EGI
     *
     * @param Request $request
     * @param int $egiId
     * @return JsonResponse
     */
    public function getEgiReservationStatus(Request $request, int $egiId): JsonResponse
    {
        // $this->logger->info('[RESERVATION_STATUS_REQUEST] EGI reservation status requested', [
        //     'egi_id' => $egiId,
        //     'user_id' => FegiAuth::id()
        // ]);

        try {
            $egi = Egi::findOrFail($egiId);

        } catch (\Exception $e) {
            return $this->errorManager->handle(
                'RECORD_EGI_NOT_FOUND_IN_RESERVATION_CONTROLLER',
                [
                    'model' => 'Egi',
                    'id' => $egiId,
                    'operation' => 'reservation_status',
                    'user_id' => FegiAuth::id(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ], $e );
        }

        try {
            // Get highest priority reservation
            $highestReservation = $this->reservationService->getHighestPriorityReservation($egi);

            // Get count of all active reservations
            $totalReservations = Reservation::where('egi_id', $egiId)
                ->where('status', 'active')
                ->where('is_current', true)
                ->count();

            // Check if current user has a reservation
            $user = FegiAuth::user();
            $walletAddress = session('connected_wallet');
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

            // $this->logger->info('[RESERVATION_STATUS_SUCCESS] EGI reservation status retrieved', [
            //     'egi_id' => $egiId,
            //     'total_reservations' => $totalReservations,
            //     'user_has_reservation' => $userReservation !== null
            // ]);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return $this->errorManager->handle(
                'RESERVATION_STATUS_FAILED',
                [
                    'egi_id' => $egiId,
                    'user_id' => FegiAuth::id(),
                    'exception_class' => get_class($e),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'error' => $e->getMessage()
                ],
                $e
            );
        }
    }
}