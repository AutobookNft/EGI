<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * @Oracode Service: CurrencyService
 * 🎯 Purpose: Handles currency conversion between EUR and ALGO
 * 🧱 Core Logic: Fetches exchange rates, performs conversions
 *
 * @package App\Services
 * @author Padmin D. Curtis (for Fabio Cherici)
 * @version 1.0.0
 * @date 2025-05-16
 */
class CurrencyService
{
    /**
     * @var UltraLogManager
     */
    protected UltraLogManager $logger;

    /**
     * @var float Cache duration in minutes
     */
    protected int $cacheDuration = 60; // 1 hour

    /**
     * Constructor with dependency injection
     *
     * @param UltraLogManager $logger
     */
    public function __construct(UltraLogManager $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Convert EUR amount to ALGO
     *
     * @param float $eurAmount Amount in EUR
     * @return float Amount in ALGO
     */
    public function convertEurToAlgo(float $eurAmount): float
    {
        $rate = $this->getAlgoExchangeRate();

        // If rate is 0 or null, log error and use default rate
        if (!$rate) {
            $this->logger->warning('Failed to get ALGO exchange rate, using fallback rate', [
                'eur_amount' => $eurAmount,
                'fallback_rate' => 0.2 // 1 EUR = 5 ALGO (fallback)
            ]);

            $rate = 0.2; // Fallback rate
        }

        // Convert: algos = euros / algo_eur_rate
        $algoAmount = $eurAmount / $rate;

        $this->logger->info('Converted EUR to ALGO', [
            'eur_amount' => $eurAmount,
            'algo_amount' => $algoAmount,
            'rate' => $rate
        ]);

        return $algoAmount;
    }

    /**
     * Get the current ALGO to EUR exchange rate
     *
     * @return float|null Exchange rate (price of 1 ALGO in EUR) or null on failure
     */
    public function getAlgoExchangeRate(): ?float
    {
        // Try to get from cache first
        $cacheKey = 'algo_eur_exchange_rate';

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            // In a real implementation, this would call a cryptocurrency price API
            // For the MVP, we'll use a simplified approach with coingecko
            $response = Http::get('https://api.coingecko.com/api/v3/simple/price', [
                'ids' => 'algorand',
                'vs_currencies' => 'eur'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $rate = $data['algorand']['eur'] ?? null;

                if ($rate) {
                    // Cache the rate
                    Cache::put($cacheKey, $rate, $this->cacheDuration);

                    $this->logger->info('Retrieved ALGO exchange rate', [
                        'rate' => $rate,
                        'source' => 'coingecko'
                    ]);

                    return $rate;
                }
            }

            $this->logger->warning('Failed to retrieve ALGO exchange rate from API', [
                'response' => $response->json()
            ]);

            // Return null on failure, which will trigger fallback rate
            return null;

        } catch (\Exception $e) {
            $this->logger->error('Exception when retrieving ALGO exchange rate', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    /**
     * Set the cache duration
     *
     * @param int $minutes Duration in minutes
     * @return void
     */
    public function setCacheDuration(int $minutes): void
    {
        $this->cacheDuration = $minutes;
    }
}
