/**
 * 📜 Exchange Rate Service Module
 * 🎯 Purpose: Handle ALGO/EUR exchange rate operations
 *
 * @version 1.0.0
 * @date 2025-08-21
 * @author Refactored by GitHub Copilot
 */

import { UEM_Client_TS_Placeholder as UEM } from '../uemClientService';
import { route } from '../../config/appConfig';
import type { AlgoExchangeRateResponse } from '../../types/reservationTypes';

// ============================================================================
// STATE MANAGEMENT
// ============================================================================

let currentAlgoRate: number | null = null;

// ============================================================================
// EXCHANGE RATE FUNCTIONS
// ============================================================================

/**
 * Get the current ALGO/EUR exchange rate
 *
 * @returns {Promise<number|null>} The exchange rate or null if unavailable
 */
export async function getAlgoExchangeRate(): Promise<number | null> {
    try {
        const rateUrl = route('api/currency/algo-exchange-rate', {});

        if (UEM && typeof UEM.safeFetch === 'function') {
            const response = await UEM.safeFetch(rateUrl, {
                headers: {
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error("HTTP error: " + response.status + " " + response.statusText);
            }

            const data = await response.json() as AlgoExchangeRateResponse;
            // Handle new API format with data.rate_to_algo or legacy format with rate
            const rate = data.data?.rate_to_algo || data.rate;
            return data.success && rate ? rate : null;
        } else {
            const response = await fetch(rateUrl, {
                headers: {
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error("HTTP error: " + response.status + " " + response.statusText);
            }

            const data = await response.json() as AlgoExchangeRateResponse;
            // Handle new API format with data.rate_to_algo or legacy format with rate
            const rate = data.data?.rate_to_algo || data.rate;
            return data.success && rate ? rate : null;
        }
    } catch (error) {
        console.error('ALGO exchange rate API error:', error);
        return null;
    }
}

/**
 * Get the cached exchange rate without making a new API call
 *
 * @returns {number|null} The cached rate or null if not available
 */
export function getCachedAlgoRate(): number | null {
    return currentAlgoRate;
}

/**
 * Set the cached exchange rate
 *
 * @param {number|null} rate The rate to cache
 */
export function setCachedAlgoRate(rate: number | null): void {
    currentAlgoRate = rate;
}

/**
 * Reset the cached exchange rate
 */
export function resetCachedAlgoRate(): void {
    currentAlgoRate = null;
}
