<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CurrencyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @Oracode Controller: CurrencyController
 * 🎯 Purpose: Provide currency exchange rates as API endpoints
 * 🧱 Core Logic: Returns ALGO/EUR exchange rate
 *
 * @package App\Http\Controllers\Api
 * @author Padmin D. Curtis (for Fabio Cherici)
 * @version 1.0.0
 * @date 2025-05-16
 */
class CurrencyController extends Controller
{
    /**
     * @var CurrencyService
     */
    protected CurrencyService $currencyService;

    /**
     * Constructor with dependency injection
     *
     * @param CurrencyService $currencyService
     */
    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    /**
     * Get the current ALGO/EUR exchange rate
     *
     * @param Request $request HTTP request
     * @return JsonResponse
     */
    public function getAlgoExchangeRate(Request $request): JsonResponse
    {
        $rate = $this->currencyService->getAlgoExchangeRate();

        if ($rate === null) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve exchange rate',
                'rate' => 0.2 // Fallback rate
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Exchange rate retrieved successfully',
            'rate' => $rate,
            'updated_at' => now()->toIso8601String()
        ]);
    }
}
