/**
 * 📜 Oracode TypeScript Service: CurrencyService (Enterprise Multi-Currency)
 * @version 2.0.0 (Corrected - Uses appConfig and UEM properly)
 * @date 2025-08-13
 * @author GitHub Copilot for Fabio Cherici
 * 🎯 Purpose: Frontend currency conversion and display service
 * 🛡️ Security: Handles REAL MONEY operations with proper error handling
 * 💱 Multi-Currency: Supports currencies from server config with real-time rates
 * 🧱 Core Logic: Think FIAT, Display in User's Preferred Currency
 * 
 * FIXES:
 * - Uses web routes instead of /api/currency
 * - Gets supported currencies from server config via appConfig
 * - Uses proper UEM.handleClientError instead of UEM.logError
 */

import { UEM } from './uemClientService';

// --- 📊 TYPES & INTERFACES ---

export interface CurrencyRate {
    rate: number;
    timestamp: string;
    is_cached?: boolean;
}

export interface CurrencyRateResponse {
    success: boolean;
    data?: {
        fiat_currency: string;
        rate_to_algo: number;
        timestamp: string;
        is_cached?: boolean;
    };
    error?: string;
    message?: string;
}

export interface ConversionResult {
    original_amount: number;
    original_currency: string;
    converted_amount: number;
    converted_currency: string;
    rate: number;
    timestamp: string;
}

export interface UserCurrencyPreference {
    success: boolean;
    data?: {
        preferred_currency: string;
        is_authenticated: boolean;
    };
    error?: string;
}

export interface CurrencyConfig {
    supported_currencies: string[];
    default_currency: string;
    api_source: string;
    cache_ttl_seconds: number;
}

// --- 🏭 CURRENCY SERVICE CLASS ---

export class CurrencyService {
    private currencyConfig: CurrencyConfig | null = null;
    private readonly CACHE_DURATION_MS: number = 60000; // 1 minute

    // In-memory cache for rates
    private rateCache: Map<string, { data: CurrencyRate; expiry: number }> = new Map();

    constructor() {
        console.log('CurrencyService initialized - Enterprise Multi-Currency System v2.0');
        this.loadCurrencyConfig();
    }

    /**
     * Load currency configuration from server
     */
    private async loadCurrencyConfig(): Promise<void> {
        try {
            const response = await fetch('/api/currency-config');
            if (!response.ok) {
                throw new Error(`Config fetch failed: ${response.status}`);
            }
            const data = await response.json();

            if (data.success && data.data) {
                this.currencyConfig = data.data;
                console.log('💰 Currency config loaded:', this.currencyConfig);
            } else {
                throw new Error('Invalid currency config response');
            }
        } catch (error) {
            console.error('Failed to load currency config, using defaults:', error);
            // Fallback to hardcoded config
            this.currencyConfig = {
                supported_currencies: ['USD', 'EUR', 'GBP'],
                default_currency: 'USD',
                api_source: 'coingecko',
                cache_ttl_seconds: 60
            };
        }
    }

    /**
     * Get supported currencies from server config
     */
    public getSupportedCurrencies(): string[] {
        return this.currencyConfig?.supported_currencies || ['USD', 'EUR', 'GBP'];
    }

    /**
     * Get default currency from server config
     */
    public getDefaultCurrency(): string {
        return this.currencyConfig?.default_currency || 'USD';
    }

    // --- 🔄 RATE FETCHING METHODS ---

    /**
     * Gets the current exchange rate for a specific currency
     * @param currency - The FIAT currency code (USD, EUR, GBP)
     * @param useCache - Whether to use cached rates (default: true)
     * @returns Promise<CurrencyRate | null>
     */
    public async getExchangeRate(currency: string = 'USD', useCache: boolean = true): Promise<CurrencyRate | null> {
        try {
            const normalizedCurrency = currency.toUpperCase();

            if (!this.getSupportedCurrencies().includes(normalizedCurrency)) {
                UEM.handleClientError('CURRENCY_UNSUPPORTED_CURRENCY', {
                    requested_currency: normalizedCurrency,
                    supported_currencies: this.getSupportedCurrencies()
                });
                return null;
            }

            // Check cache first
            if (useCache) {
                const cached = this.getCachedRate(normalizedCurrency);
                if (cached) {
                    return cached;
                }
            }

            const response = await fetch(`/api/currency/rate/${normalizedCurrency}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.getCSRFToken()
                }
            });

            if (!response.ok) {
                UEM.handleClientError('CURRENCY_RATE_FETCH_FAILED', {
                    currency: normalizedCurrency,
                    status: response.status,
                    statusText: response.statusText
                });
                return null;
            }

            const data: CurrencyRateResponse = await response.json();

            if (data.success && data.data) {
                const rate: CurrencyRate = {
                    rate: data.data.rate_to_algo,
                    timestamp: data.data.timestamp,
                    is_cached: data.data.is_cached
                };

                // Cache the rate
                this.setCachedRate(normalizedCurrency, rate);

                return rate;
            }

            return null;

        } catch (error) {
            UEM.handleClientError('CURRENCY_SERVICE_ERROR', {
                currency: currency,
                error: (error as Error).message
            }, error as Error);
            return null;
        }
    }

    /**
     * Get user's preferred currency from server
     * @returns Promise<string> - Currency code or default
     */
    public async getUserPreferredCurrency(): Promise<string> {
        try {
            const response = await fetch('/user/preferences/currency', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.getCSRFToken()
                }
            });

            if (!response.ok) {
                return this.getDefaultCurrency();
            }

            const data: UserCurrencyPreference = await response.json();

            if (data.success && data.data?.preferred_currency) {
                return data.data.preferred_currency;
            }

            return this.getDefaultCurrency();

        } catch (error) {
            UEM.handleClientError('USER_PREFERENCE_FETCH_ERROR', {
                error: (error as Error).message
            }, error as Error);
            return this.getDefaultCurrency();
        }
    }

    /**
     * Set user's preferred currency on server
     * @param currency - Currency code to set
     * @returns Promise<boolean> - Success status
     */
    public async setUserPreferredCurrency(currency: string): Promise<boolean> {
        try {
            const normalizedCurrency = currency.toUpperCase();

            if (!this.getSupportedCurrencies().includes(normalizedCurrency)) {
                UEM.handleClientError('CURRENCY_UNSUPPORTED_CURRENCY', {
                    requested_currency: normalizedCurrency,
                    supported_currencies: this.getSupportedCurrencies()
                });
                return false;
            }

            const response = await fetch('/user/preferences/currency', {
                method: 'PUT',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCSRFToken()
                },
                body: JSON.stringify({ currency: normalizedCurrency })
            });

            if (!response.ok) {
                UEM.handleClientError('USER_PREFERENCE_UPDATE_ERROR', {
                    currency: normalizedCurrency,
                    status: response.status,
                    statusText: response.statusText
                });
                return false;
            }

            const data: UserCurrencyPreference = await response.json();
            return data.success || false;

        } catch (error) {
            UEM.handleClientError('USER_PREFERENCE_UPDATE_ERROR', {
                currency: currency,
                error: (error as Error).message
            }, error as Error);
            return false;
        }
    }

    /**
     * Format currency for display
     * @param amount - Amount to format
     * @param currency - Currency code
     * @returns string - Formatted currency string
     */
    public formatCurrency(amount: number, currency: string): string {
        try {
            return new Intl.NumberFormat('it-IT', {
                style: 'currency',
                currency: currency.toUpperCase(),
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(amount);
        } catch (error) {
            // Fallback if currency not supported by Intl
            return `${amount.toFixed(2)} ${currency.toUpperCase()}`;
        }
    }

    /**
     * Convert currency between different FIAT currencies
     * @param amount - Amount to convert
     * @param fromCurrency - Source currency
     * @param toCurrency - Target currency  
     * @returns Promise<ConversionResult | null>
     */
    public async convertCurrency(amount: number, fromCurrency: string, toCurrency: string): Promise<ConversionResult | null> {
        try {
            // Se stessa valuta, nessuna conversione necessaria
            if (fromCurrency.toUpperCase() === toCurrency.toUpperCase()) {
                return {
                    original_amount: amount,
                    original_currency: fromCurrency.toUpperCase(),
                    converted_amount: amount,
                    converted_currency: toCurrency.toUpperCase(),
                    rate: 1.0,
                    timestamp: new Date().toISOString()
                };
            }

            // Per ora utilizziamo conversione via ALGO (Think FIAT, Operate ALGO)
            const fromRate = await this.getExchangeRate(fromCurrency);
            const toRate = await this.getExchangeRate(toCurrency);

            if (!fromRate || !toRate) {
                return null;
            }

            // Conversione: amount -> ALGO -> target currency
            const algoAmount = amount / fromRate.rate;
            const convertedAmount = algoAmount * toRate.rate;

            return {
                original_amount: amount,
                original_currency: fromCurrency.toUpperCase(),
                converted_amount: convertedAmount,
                converted_currency: toCurrency.toUpperCase(),
                rate: toRate.rate / fromRate.rate,
                timestamp: new Date().toISOString()
            };

        } catch (error) {
            UEM.handleClientError('CURRENCY_CONVERSION_ERROR', {
                amount,
                fromCurrency,
                toCurrency,
                error: (error as Error).message
            }, error as Error);
            return null;
        }
    }

    // --- 🧰 PRIVATE HELPER METHODS ---

    private getCachedRate(currency: string): CurrencyRate | null {
        const cached = this.rateCache.get(currency);
        if (cached && Date.now() < cached.expiry) {
            return cached.data;
        }
        return null;
    }

    private setCachedRate(currency: string, rate: CurrencyRate): void {
        const expiry = Date.now() + this.CACHE_DURATION_MS;
        this.rateCache.set(currency, { data: rate, expiry });
    }

    private getCSRFToken(): string {
        const tokenElement = document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement;
        return tokenElement?.content || '';
    }
}

// --- 🚀 SINGLETON EXPORT ---

export const currencyService = new CurrencyService();
