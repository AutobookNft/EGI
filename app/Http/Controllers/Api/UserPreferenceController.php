<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CurrencyService;
use App\Contracts\ErrorManagerInterface;
use App\Contracts\LogManagerInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * @Oracode Controller: UserPreferenceController
 * 🎯 Purpose: Manage user preferences with multi-currency support
 * 🧱 Core Logic: "Think FIAT, Operate ALGO" architecture implementation
 * 🛡️ Auth: Mixed approach - public for anonymous, protected for authenticated
 *
 * Implements mixed API security approach:
 * - Public endpoints for anonymous users (USD default rates)
 * - Protected endpoints for authenticated user preferences
 *
 * @package App\Http\Controllers\Api
 * @author Fabio Cherici (Multi-Currency System)
 * @version 2.0.0
 * @date 2025-08-13
 */
class UserPreferenceController extends Controller {
    private CurrencyService $currencyService;
    private ErrorManagerInterface $errorManager;
    private LogManagerInterface $logger;

    public function __construct(
        CurrencyService $currencyService,
        ErrorManagerInterface $errorManager,
        LogManagerInterface $logger
    ) {
        $this->currencyService = $currencyService;
        $this->errorManager = $errorManager;
        $this->logger = $logger;
    }

    /**
     * Update user's preferred currency (PROTECTED)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updatePreferredCurrency(Request $request): JsonResponse {
        try {
            $user = Auth::user();

            if (!$user) {
                return $this->errorManager->handleError(
                    'UEM_USER_UNAUTHENTICATED',
                    null,
                    ['endpoint' => 'updatePreferredCurrency']
                );
            }

            $request->validate([
                'preferred_currency' => ['required', 'string', Rule::in(['EUR', 'USD', 'GBP'])]
            ]);

            $oldCurrency = $user->preferred_currency;
            $user->preferred_currency = $request->preferred_currency;
            $user->save();

            $this->logger->info('User preferred currency updated', [
                'user_id' => $user->id,
                'old_currency' => $oldCurrency,
                'new_currency' => $request->preferred_currency
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Currency preference updated successfully',
                'data' => [
                    'preferred_currency' => $user->preferred_currency
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorManager->handleError(
                'CURRENCY_UNSUPPORTED_CURRENCY',
                $e,
                [
                    'validation_errors' => $e->errors(),
                    'requested_currency' => $request->input('preferred_currency')
                ]
            );
        } catch (\Exception $e) {
            return $this->errorManager->handleError(
                'USER_PREFERENCE_UPDATE_FAILED',
                $e,
                [
                    'action' => 'update_preferred_currency',
                    'user_id' => Auth::id(),
                    'requested_currency' => $request->input('preferred_currency')
                ]
            );
        }
    }

    /**
     * Get user's current preferences (PROTECTED)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getPreferences(Request $request): JsonResponse {
        try {
            $user = Auth::user();

            if (!$user) {
                return $this->errorManager->handleError(
                    'UEM_USER_UNAUTHENTICATED',
                    null,
                    ['endpoint' => 'getPreferences']
                );
            }

            $preferences = [
                'preferred_currency' => $user->preferred_currency ?? 'EUR',
                'language' => $user->language ?? 'en',
            ];

            $this->logger->info('User preferences retrieved', [
                'user_id' => $user->id,
                'preferences' => $preferences
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'preferences' => $preferences
                ]
            ]);
        } catch (\Exception $e) {
            return $this->errorManager->handleError(
                'USER_PREFERENCE_UPDATE_FAILED',
                $e,
                [
                    'action' => 'get_preferences',
                    'user_id' => Auth::id()
                ]
            );
        }
    }

    /**
     * Get user-specific exchange rate (PROTECTED)
     * Returns rate in user's preferred currency
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getUserExchangeRate(Request $request): JsonResponse {
        try {
            $user = Auth::user();

            if (!$user) {
                return $this->errorManager->handleError(
                    'UEM_USER_UNAUTHENTICATED',
                    null,
                    ['endpoint' => 'getUserExchangeRate']
                );
            }

            $preferredCurrency = $user->preferred_currency ?? 'EUR';

            // Get the exchange rate for user's preferred currency
            $rateData = $this->currencyService->getAlgoToFiatRate($preferredCurrency);

            $this->logger->info('User-specific exchange rate retrieved', [
                'user_id' => $user->id,
                'preferred_currency' => $preferredCurrency,
                'rate' => $rateData['rate'] ?? null
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'currency' => $preferredCurrency,
                    'rate' => $rateData['rate'],
                    'timestamp' => $rateData['timestamp'],
                    'is_cached' => $rateData['is_cached'] ?? false
                ]
            ]);
        } catch (\Exception $e) {
            return $this->errorManager->handleError(
                'CURRENCY_EXCHANGE_SERVICE_UNAVAILABLE',
                $e,
                [
                    'action' => 'get_user_exchange_rate',
                    'user_id' => Auth::id()
                ]
            );
        }
    }

    /**
     * Get comprehensive user currency data (PROTECTED)
     * Includes preferences and current rates for dashboard
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getUserCurrencyData(Request $request): JsonResponse {
        try {
            $user = Auth::user();

            if (!$user) {
                return $this->errorManager->handleError(
                    'UEM_USER_UNAUTHENTICATED',
                    null,
                    ['endpoint' => 'getUserCurrencyData']
                );
            }

            $preferredCurrency = $user->preferred_currency ?? 'EUR';

            // Get exchange rate data for user's preferred currency
            $rateData = $this->currencyService->getAlgoToFiatRate($preferredCurrency);

            // Get rates for all supported currencies for comparison
            $allRates = [];
            foreach (['EUR', 'USD', 'GBP'] as $currency) {
                try {
                    $currencyRate = $this->currencyService->getAlgoToFiatRate($currency);
                    $allRates[$currency] = [
                        'rate' => $currencyRate['rate'],
                        'timestamp' => $currencyRate['timestamp']
                    ];
                } catch (\Exception $e) {
                    $this->logger->warning('Failed to get rate for currency in user data', [
                        'currency' => $currency,
                        'error' => $e->getMessage(),
                        'user_id' => $user->id
                    ]);
                }
            }

            $response = [
                'success' => true,
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'preferred_currency' => $preferredCurrency
                    ],
                    'current_rate' => [
                        'currency' => $preferredCurrency,
                        'rate' => $rateData['rate'],
                        'timestamp' => $rateData['timestamp'],
                        'is_cached' => $rateData['is_cached'] ?? false
                    ],
                    'all_rates' => $allRates,
                    'supported_currencies' => ['EUR', 'USD', 'GBP']
                ]
            ];

            $this->logger->info('Comprehensive user currency data retrieved', [
                'user_id' => $user->id,
                'preferred_currency' => $preferredCurrency,
                'currencies_retrieved' => array_keys($allRates)
            ]);

            return response()->json($response);
        } catch (\Exception $e) {
            return $this->errorManager->handleError(
                'CURRENCY_EXCHANGE_SERVICE_UNAVAILABLE',
                $e,
                [
                    'action' => 'get_user_currency_data',
                    'user_id' => Auth::id()
                ]
            );
        }
    }
}
