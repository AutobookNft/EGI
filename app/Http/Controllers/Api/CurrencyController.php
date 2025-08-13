<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CurrencyService;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\UltraError;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @Oracode Controller: CurrencyController (Mixed Security Approach)
 * 🎯 Purpose: Provide multi-currency exchange rates with mixed API security
 * 🧱 Core Logic: "Think FIAT, Operate ALGO" architecture implementation
 * 🔓 Public: Anonymous users get USD default rates
 * 🔒 Protected: Authenticated users get personalized currency rates
 * 💱 Multi-Currency: Supports EUR, USD, GBP
 *
 * @package App\Http\Controllers\Api
 * @author Fabio Cherici (Mixed Security Multi-Currency)
 * @version 3.0.0 (Mixed Security Architecture)
 * @date 2025-08-13
 */
class CurrencyController extends Controller {
    private CurrencyService $currencyService;
    private ErrorManagerInterface $errorManager;
    private UltraLogManager $logger;

    /**
     * Constructor with dependency injection
     *
     * @param CurrencyService $currencyService
     * @param ErrorManagerInterface $errorManager
     * @param LogManagerInterface $logger
     */
    public function __construct(
        CurrencyService $currencyService,
        ErrorManagerInterface $errorManager,
        UltraLogManager $logger
    ) {
        $this->currencyService = $currencyService;
        $this->errorManager = $errorManager;
        $this->logger = $logger;
    }

    /**
     * Get exchange rate for specified FIAT currency to ALGO (Public endpoint)
     *
     * @param string $fiatCurrency Currency code (EUR, USD, GBP)
     * @param Request $request HTTP Request
     * @return JsonResponse
     */
    public function getRate(string $fiatCurrency, Request $request): JsonResponse {
        try {
            $fiatCurrency = strtoupper($fiatCurrency);

            // Handle 'default' special case first
            if ($fiatCurrency === 'DEFAULT') {
                $fiatCurrency = 'USD';
            }

            // Validate supported currencies
            $supportedCurrencies = ['EUR', 'USD', 'GBP'];
            if (!in_array($fiatCurrency, $supportedCurrencies)) {
                return response()->json([
                    'error' => 'CURRENCY_UNSUPPORTED_CURRENCY',
                    'message' => 'Unsupported currency',
                    'data' => [
                        'requested_currency' => $fiatCurrency,
                        'supported_currencies' => $supportedCurrencies
                    ]
                ], 400);
            }

            // Get rate from CurrencyService
            $rateData = $this->currencyService->getAlgoToFiatRate($fiatCurrency);

            if (!$rateData || !isset($rateData['rate'])) {
                return response()->json([
                    'error' => 'CURRENCY_RATE_FETCH_FAILED',
                    'message' => 'Unable to fetch exchange rate',
                    'data' => [
                        'fiat_currency' => $fiatCurrency
                    ]
                ], 503);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'fiat_currency' => $fiatCurrency,
                    'rate_to_algo' => $rateData['rate'],
                    'timestamp' => $rateData['timestamp'] ?? now()->toISOString(),
                    'is_cached' => $rateData['is_cached'] ?? false
                ]
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Currency rate fetch failed', [
                'fiat_currency' => $fiatCurrency ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'CURRENCY_EXCHANGE_SERVICE_UNAVAILABLE',
                'message' => 'Failed to fetch exchange rate',
                'data' => [
                    'fiat_currency' => $fiatCurrency ?? 'unknown'
                ]
            ], 500);
        }
    }

    /**
     * Get all supported currency rates (PUBLIC)
     * Perfect for anonymous users to see all available rates
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getAllRates(Request $request): JsonResponse {
        try {
            $supportedCurrencies = ['EUR', 'USD', 'GBP'];
            $rates = [];

            foreach ($supportedCurrencies as $currency) {
                try {
                    $rateData = $this->currencyService->getAlgoToFiatRate($currency);
                    $rates[$currency] = [
                        'rate' => $rateData['rate'],
                        'timestamp' => $rateData['timestamp'],
                        'is_cached' => $rateData['is_cached'] ?? false
                    ];
                } catch (\Exception $e) {
                    $this->logger->warning('Failed to get rate for currency in public endpoint', [
                        'currency' => $currency,
                        'error' => $e->getMessage()
                    ]);

                    $rates[$currency] = [
                        'rate' => null,
                        'error' => 'Rate unavailable'
                    ];
                }
            }

            $this->logger->info('Public all rates retrieved', [
                'currencies_requested' => $supportedCurrencies,
                'currencies_retrieved' => array_keys(array_filter($rates, fn($r) => isset($r['rate']))),
                'is_authenticated' => Auth::check(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'All exchange rates retrieved successfully',
                'data' => [
                    'rates' => $rates,
                    'supported_currencies' => $supportedCurrencies,
                    'default_currency' => 'USD',
                    'endpoint_type' => 'public',
                    'timestamp' => now()
                ]
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to get all currency rates', [
                'action' => 'get_all_rates_public',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'CURRENCY_EXCHANGE_SERVICE_UNAVAILABLE',
                'message' => 'Unable to fetch all currency rates',
                'data' => []
            ], 503);
        }
    }

    /**
     * Get USD rate specifically for anonymous users (PUBLIC)
     * This is the default rate shown in header badge for non-authenticated users
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getDefaultRate(Request $request): JsonResponse {
        try {
            $defaultCurrency = 'USD';
            $rateData = $this->currencyService->getAlgoToFiatRate($defaultCurrency);

            $this->logger->info('Default rate for anonymous users retrieved', [
                'currency' => $defaultCurrency,
                'rate' => $rateData['rate'] ?? null,
                'is_authenticated' => Auth::check()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Default rate retrieved successfully',
                'data' => [
                    'currency' => $defaultCurrency,
                    'rate' => $rateData['rate'],
                    'timestamp' => $rateData['timestamp'],
                    'is_cached' => $rateData['is_cached'] ?? false,
                    'endpoint_type' => 'public_default',
                    'description' => 'Default rate for anonymous users'
                ]
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to get default rate for anonymous user', [
                'action' => 'get_default_rate_anonymous',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'CURRENCY_EXCHANGE_SERVICE_UNAVAILABLE',
                'message' => 'Unable to fetch default currency rate',
                'data' => []
            ], 503);
        }
    }

    /**
     * Get exchange rate for current user's preferred currency (PROTECTED)
     * This method requires authentication and returns user-specific rates
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getCurrentUserRate(Request $request): JsonResponse {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'error' => 'UEM_USER_UNAUTHENTICATED',
                    'message' => 'Authentication required for this endpoint',
                    'data' => []
                ], 401);
            }

            $currency = $user->preferred_currency ?? 'EUR';
            $rateData = $this->currencyService->getAlgoToFiatRate($currency);

            $this->logger->info('User-specific rate retrieved via CurrencyController', [
                'user_id' => $user->id,
                'currency' => $currency,
                'rate' => $rateData['rate'] ?? null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User preferred currency rate retrieved successfully',
                'data' => [
                    'currency' => $currency,
                    'rate' => $rateData['rate'],
                    'timestamp' => $rateData['timestamp'],
                    'is_cached' => $rateData['is_cached'] ?? false,
                    'endpoint_type' => 'protected_user_specific',
                    'user_id' => $user->id
                ]
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to get user-specific currency rate', [
                'action' => 'get_current_user_rate',
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'CURRENCY_EXCHANGE_SERVICE_UNAVAILABLE',
                'message' => 'Unable to fetch user currency rate',
                'data' => []
            ], 503);
        }
    }

    /**
     * Convert FIAT amount to microALGO (PUBLIC)
     * Utility endpoint for conversion calculations
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function convertFiatToAlgo(Request $request): JsonResponse {
        try {
            $request->validate([
                'amount' => 'required|numeric|min:0',
                'currency' => 'required|string|in:EUR,USD,GBP'
            ]);

            $amount = $request->input('amount');
            $currency = strtoupper($request->input('currency'));

            // Prima ottieni il tasso di cambio
            $rateData = $this->currencyService->getAlgoToFiatRate($currency);

            if (!$rateData || !isset($rateData['rate'])) {
                return response()->json([
                    'error' => 'CURRENCY_RATE_FETCH_FAILED',
                    'message' => 'Unable to fetch exchange rate for conversion',
                    'data' => [
                        'currency' => $currency
                    ]
                ], 503);
            }

            $microAlgo = $this->currencyService->convertFiatToMicroAlgo($amount, $rateData['rate']);

            $this->logger->info('FIAT to ALGO conversion performed', [
                'amount' => $amount,
                'currency' => $currency,
                'micro_algo' => $microAlgo,
                'is_authenticated' => Auth::check(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Conversion completed successfully',
                'data' => [
                    'input' => [
                        'amount' => $amount,
                        'currency' => $currency
                    ],
                    'output' => [
                        'micro_algo' => $microAlgo,
                        'algo' => $microAlgo / 1000000 // Convert to ALGO for display
                    ],
                    'endpoint_type' => 'public_utility'
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->logger->error('Currency conversion validation failed', [
                'validation_errors' => $e->errors(),
                'input_data' => $request->all()
            ]);

            return response()->json([
                'error' => 'CURRENCY_CONVERSION_VALIDATION_ERROR',
                'message' => 'Invalid input data for conversion',
                'data' => [
                    'validation_errors' => $e->errors()
                ]
            ], 422);
        } catch (\Exception $e) {
            $this->logger->error('Currency conversion failed', [
                'action' => 'convert_fiat_to_algo',
                'input_data' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'CURRENCY_CONVERSION_ERROR',
                'message' => 'Failed to convert currency',
                'data' => []
            ], 500);
        }
    }

    // Legacy method per retrocompatibilità
    public function getAlgoExchangeRate(Request $request): JsonResponse {
        return $this->getRate('EUR', $request);
    }
}
