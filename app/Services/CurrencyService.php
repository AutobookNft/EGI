<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * @Oracode Service: CurrencyService (Enhanced Multi-Currency)
 * 🎯 Purpose: Handles multi-currency conversion between FIAT and ALGO
 * 🧱 Core Logic: Fetches exchange rates, performs conversions, Think FIAT - Operate ALGO
 * 🛡️ Error Management: Integrated UEM for robust error handling
 * 💱 Multi-Currency: Supports EUR, USD, GBP, etc.
 *
 * @package App\Services
 * @author Fabio Cherici (Enhanced for Multi-Currency)
 * @version 2.0.0 (Multi-Currency)
 * @date 2025-08-13
 */
class CurrencyService {
    private const CACHE_KEY_PREFIX = 'currency_rate_';
    private const CACHE_TTL_SECONDS = 60; // Cache per 1 minuto

    /**
     * Constructor with dependency injection
     *
     * @param UltraLogManager $logger
     * @param ErrorManagerInterface $errorManager
     */
    public function __construct(
        protected UltraLogManager $logger,
        protected ErrorManagerInterface $errorManager
    ) {
    }

    /**
     * Ottiene il tasso di cambio attuale da ALGO a una valuta FIAT.
     * Implementa caching e failover.
     *
     * @param string $fiatCurrency
     * @return array|null ['rate' => float, 'timestamp' => Carbon]
     */
    public function getAlgoToFiatRate(string $fiatCurrency = 'USD'): ?array {
        $cacheKey = self::CACHE_KEY_PREFIX . strtoupper($fiatCurrency);

        return Cache::remember($cacheKey, self::CACHE_TTL_SECONDS, function () use ($fiatCurrency) {
            try {
                $response = Http::get('https://api.coingecko.com/api/v3/simple/price', [
                    'ids' => 'algorand',
                    'vs_currencies' => strtolower($fiatCurrency),
                ]);

                if ($response->failed() || !isset($response->json()['algorand'][strtolower($fiatCurrency)])) {
                    throw new \Exception('CoinGecko API request failed or returned invalid data.');
                }

                $rateData = [
                    'rate' => (float) $response->json()['algorand'][strtolower($fiatCurrency)],
                    'timestamp' => now(),
                ];

                $this->logger->info('Currency exchange rate fetched successfully', [
                    'currency' => $fiatCurrency,
                    'rate' => $rateData['rate'],
                    'source' => 'coingecko'
                ]);

                return $rateData;
            } catch (\Exception $e) {
                // Gestione errore standardizzata con UEM iniettato
                $this->errorManager->handle('CURRENCY_EXCHANGE_SERVICE_FAILED', [
                    'currency' => $fiatCurrency,
                    'error' => $e->getMessage(),
                ], $e);

                // Ritorna null per essere gestito a monte. L'errore è già stato processato da UEM.
                return null;
            }
        });
    }

    /**
     * Converte un importo FIAT in microALGO.
     *
     * @param float $fiatAmount
     * @param float $rate
     * @return int microALGO
     */
    public function convertFiatToMicroAlgo(float $fiatAmount, float $rate): int {
        if ($rate <= 0) return 0;
        $algoAmount = $fiatAmount / $rate;
        return (int) ($algoAmount * 1_000_000); // Converte in microALGO
    }

    /**
     * Converte microALGO in un importo FIAT.
     *
     * @param int $microAlgoAmount
     * @param float $rate
     * @return float
     */
    public function convertMicroAlgoToFiat(int $microAlgoAmount, float $rate): float {
        $algoAmount = $microAlgoAmount / 1_000_000;
        return $algoAmount * $rate;
    }

    /**
     * Ottiene il tasso per la valuta preferita dell'utente
     * Se l'utente non è autenticato, usa USD come default
     *
     * @return array|null
     */
    public function getCurrentUserCurrencyRate(): ?array {
        $preferredCurrency = auth()->check()
            ? (auth()->user()->preferred_currency ?? 'USD')
            : 'USD';

        return $this->getAlgoToFiatRate($preferredCurrency);
    }

    /**
     * Ottiene la valuta preferita dell'utente corrente
     *
     * @return string
     */
    public function getCurrentUserCurrency(): string {
        return auth()->check()
            ? (auth()->user()->preferred_currency ?? 'USD')
            : 'USD';
    }

    // Metodi legacy per retrocompatibilità
    public function convertEurToAlgo(float $eurAmount): float {
        $rateData = $this->getAlgoToFiatRate('EUR');
        if (!$rateData) {
            $this->logger->warning('Failed to get EUR exchange rate, using fallback', [
                'eur_amount' => $eurAmount,
                'fallback_rate' => 0.2
            ]);
            return $eurAmount / 0.2; // Fallback
        }

        return $eurAmount / $rateData['rate'];
    }

    public function getAlgoExchangeRate(): ?float {
        $rateData = $this->getAlgoToFiatRate('EUR');
        return $rateData ? $rateData['rate'] : null;
    }
}
