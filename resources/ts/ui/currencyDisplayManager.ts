/**
 * 📜 Oracode TypeScript UI Manager: CurrencyDisplayManager (Enterprise Multi-Currency UI)
 * @version 1.0.0 (Enterprise Grade)
 * @date 2025-08-13
 * @author GitHub Copilot for Fabio Cherici
 * 🎯 Purpose: Manages currency display and conversion across all UI elements
 * 🛡️ Security: Handles REAL MONEY display with proper formatting
 * 💱 Multi-Currency: Updates all price displays when user changes currency
 * 🧱 Core Logic: Single source of truth for all currency display operations
 */

import { currencyService, CurrencyService, ConversionResult } from '../services/currencyService';
import { UEM } from '../services/uemClientService';

// --- 📊 TYPES & INTERFACES ---

export interface PriceElement {
    element: HTMLElement;
    originalAmount: number;
    originalCurrency: string;
    dataAttributes: {
        priceAmount?: string;
        priceCurrency?: string;
        priceFormat?: string;
    };
}

export interface CurrencyDisplayConfig {
    selector: string;
    amountAttribute: string;
    currencyAttribute: string;
    formatAttribute?: string;
    locale?: string;
}

// --- 🎨 CURRENCY DISPLAY MANAGER CLASS ---

export class CurrencyDisplayManager {
    private readonly currencyService: CurrencyService;
    private currentUserCurrency: string = 'USD';
    private priceElements: Map<string, PriceElement> = new Map();
    private isInitialized: boolean = false;

    // Default selectors for price elements
    private readonly DEFAULT_SELECTORS: CurrencyDisplayConfig[] = [
        {
            selector: '[data-price-amount]',
            amountAttribute: 'data-price-amount',
            currencyAttribute: 'data-price-currency',
            formatAttribute: 'data-price-format'
        },
        {
            selector: '.price-display',
            amountAttribute: 'data-amount',
            currencyAttribute: 'data-currency',
            formatAttribute: 'data-format'
        },
        {
            selector: '.currency-amount',
            amountAttribute: 'data-amount',
            currencyAttribute: 'data-currency'
        },
        {
            selector: '.currency-display',  // 🔧 FIX: Aggiungo il selettore del componente currency-price
            amountAttribute: 'data-price',  // 🔧 FIX: Usa data-price invece di data-amount
            currencyAttribute: 'data-currency',
            formatAttribute: 'data-display-options'
        }
    ];

    constructor(service?: CurrencyService) {
        this.currencyService = service || currencyService;
    }

    // --- 🚀 INITIALIZATION METHODS ---

    /**
     * Initializes the currency display manager
     * Scans DOM for price elements and sets up event listeners
     */
    public async initialize(): Promise<void> {
        try {
            // Get user's preferred currency
            await this.loadUserCurrency();

            // Scan and register all price elements
            this.scanAndRegisterPriceElements();

            // Set up currency selector if it exists
            this.setupCurrencySelector();

            // Set up global event listeners
            this.setupEventListeners();

            // Initial display update
            await this.updateAllPriceDisplays();

            this.isInitialized = true;

        } catch (error) {
            UEM.handleClientError('CURRENCY_DISPLAY_INIT_ERROR', {
                error: error instanceof Error ? error.message : 'Unknown error'
            }, error as Error, 'Failed to initialize CurrencyDisplayManager');
        }
    }

    /**
     * Loads user's preferred currency from the backend
     */
    private async loadUserCurrency(): Promise<void> {
        try {
            this.currentUserCurrency = await this.currencyService.getUserPreferredCurrency();
        } catch (error) {
            UEM.handleClientError('CURRENCY_USER_LOAD_ERROR', {
                error: error instanceof Error ? error.message : 'Unknown error'
            }, error as Error, 'Failed to load user currency preference');
            this.currentUserCurrency = this.currencyService.getDefaultCurrency();
        }
    }

    // --- 🔍 DOM SCANNING METHODS ---

    /**
     * Scans the DOM for price elements and registers them
     */
    private scanAndRegisterPriceElements(): void {
        this.priceElements.clear();

        this.DEFAULT_SELECTORS.forEach(config => {
            const elements = document.querySelectorAll(config.selector);
            elements.forEach((element, index) => {
                this.registerPriceElement(element as HTMLElement, config, `${config.selector}-${index}`);
            });
        });
    }

    /**
     * Registers a single price element for currency conversion
     */
    private registerPriceElement(element: HTMLElement, config: CurrencyDisplayConfig, key: string): void {
        const amountStr = element.getAttribute(config.amountAttribute);
        const currency = element.getAttribute(config.currencyAttribute) || 'USD';

        if (!amountStr) return;

        const amount = parseFloat(amountStr);
        const safeAmount = isNaN(amount) ? 0 : amount;

        const priceElement: PriceElement = {
            element,
            originalAmount: safeAmount,
            originalCurrency: currency.toUpperCase(),
            dataAttributes: {
                priceAmount: amountStr,
                priceCurrency: currency,
                priceFormat: element.getAttribute(config.formatAttribute || '') || 'standard'
            }
        };

        this.priceElements.set(key, priceElement);
    }

    // --- 🎨 DISPLAY UPDATE METHODS ---

    /**
     * Updates all registered price displays with current user currency
     */
    public async updateAllPriceDisplays(): Promise<void> {
        try {
            const updatePromises: Promise<void>[] = [];

            for (const [key, priceElement] of this.priceElements.entries()) {
                updatePromises.push(this.updateSinglePriceDisplay(key, priceElement));
            }

            await Promise.all(updatePromises);

        } catch (error) {
            UEM.handleClientError('CURRENCY_DISPLAY_UPDATE_ERROR', {
                error: error instanceof Error ? error.message : 'Unknown error',
                current_currency: this.currentUserCurrency
            }, error as Error, 'Failed to update price displays');
        }
    }

    /**
     * Updates a single price display element
     */
    private async updateSinglePriceDisplay(key: string, priceElement: PriceElement): Promise<void> {
        try {
            const { element, originalAmount, originalCurrency } = priceElement;

            // If already in target currency, just format
            if (originalCurrency === this.currentUserCurrency) {
                const formatted = this.currencyService.formatCurrency(originalAmount, originalCurrency);
                element.textContent = formatted;
                return;
            }

            // Convert currency
            const conversion = await this.currencyService.convertCurrency(
                originalAmount,
                originalCurrency,
                this.currentUserCurrency
            );

            if (!conversion) {
                // Fallback: show original with currency code
                element.textContent = this.currencyService.formatCurrency(originalAmount, originalCurrency);
                return;
            }

            // Update display with converted amount
            const finalFormatted = this.currencyService.formatCurrency(
                conversion.converted_amount,
                conversion.converted_currency
            );

            element.textContent = finalFormatted;

            // Update data attributes
            element.setAttribute('data-converted-amount', conversion.converted_amount.toString());
            element.setAttribute('data-converted-currency', conversion.converted_currency);
            element.setAttribute('data-conversion-rate', conversion.rate.toString());

        } catch (error) {
            UEM.handleClientError('CURRENCY_SINGLE_DISPLAY_UPDATE_ERROR', {
                key,
                original_amount: priceElement.originalAmount,
                original_currency: priceElement.originalCurrency,
                target_currency: this.currentUserCurrency,
                error: error instanceof Error ? error.message : 'Unknown error'
            }, error as Error, 'Failed to update single price display');
        }
    }

    // --- 🎛️ CURRENCY SELECTOR METHODS ---

    /**
     * Sets up the currency selector dropdown
     */
    private setupCurrencySelector(): void {
        const selector = document.getElementById('currency-selector') as HTMLSelectElement;
        if (!selector) {
            return;
        }

        // Set current value
        selector.value = this.currentUserCurrency;

        // Add event listener
        selector.addEventListener('change', async (event) => {
            const target = event.target as HTMLSelectElement;
            await this.changeCurrency(target.value);
        });
    }

    /**
     * Changes the display currency and updates all elements
     */
    public async changeCurrency(newCurrency: string): Promise<void> {
        try {
            const normalizedCurrency = newCurrency.toUpperCase();

            // Update user preference on backend (if authenticated)
            await this.currencyService.setUserPreferredCurrency(normalizedCurrency);

            // Update local currency
            this.currentUserCurrency = normalizedCurrency;

            // Update all displays
            await this.updateAllPriceDisplays();

            // Update selector
            const selector = document.getElementById('currency-selector') as HTMLSelectElement;
            if (selector) {
                selector.value = normalizedCurrency;
            }

            // Dispatch custom event for other components
            this.dispatchCurrencyChangeEvent(normalizedCurrency);

        } catch (error) {
            UEM.handleClientError('CURRENCY_CHANGE_ERROR', {
                new_currency: newCurrency,
                error: error instanceof Error ? error.message : 'Unknown error'
            }, error as Error, 'Failed to change currency');
        }
    }

    // --- 📡 EVENT HANDLING METHODS ---

    /**
     * Sets up global event listeners
     */
    private setupEventListeners(): void {
        // Listen for DOM changes to register new price elements
        const observer = new MutationObserver((mutations) => {
            let shouldRescan = false;

            mutations.forEach((mutation) => {
                if (mutation.type === 'childList') {
                    mutation.addedNodes.forEach((node) => {
                        if (node.nodeType === Node.ELEMENT_NODE) {
                            const element = node as Element;
                            // Check if added node or its children have price attributes
                            if (element.querySelector('[data-price-amount], .price-display, .currency-amount')) {
                                shouldRescan = true;
                            }
                        }
                    });
                }
            });

            if (shouldRescan) {
                this.scanAndRegisterPriceElements();
                this.updateAllPriceDisplays();
            }
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });

        // Listen for custom currency events
        document.addEventListener('currencyChanged', ((event: CustomEvent) => {
            if (event.detail.currency !== this.currentUserCurrency) {
                this.changeCurrency(event.detail.currency);
            }
        }) as EventListener);
    }

    /**
     * Dispatches custom currency change event
     */
    private dispatchCurrencyChangeEvent(currency: string): void {
        const event = new CustomEvent('currencyChanged', {
            detail: { currency, timestamp: new Date().toISOString() }
        });
        document.dispatchEvent(event);
    }

    // --- 🔧 PUBLIC API METHODS ---

    /**
     * Manually adds a price element to be managed
     */
    public addPriceElement(element: HTMLElement, amount: number, currency: string, key?: string): void {
        const elementKey = key || `manual-${Date.now()}-${Math.random()}`;

        const priceElement: PriceElement = {
            element,
            originalAmount: amount,
            originalCurrency: currency.toUpperCase(),
            dataAttributes: {
                priceAmount: amount.toString(),
                priceCurrency: currency
            }
        };

        this.priceElements.set(elementKey, priceElement);
        this.updateSinglePriceDisplay(elementKey, priceElement);
    }

    /**
     * Gets current user currency
     */
    public getCurrentCurrency(): string {
        return this.currentUserCurrency;
    }

    /**
     * Gets supported currencies
     */
    public getSupportedCurrencies(): string[] {
        return this.currencyService.getSupportedCurrencies();
    }

    /**
     * Forces a refresh of all price displays
     */
    public async refresh(): Promise<void> {
        this.scanAndRegisterPriceElements();
        await this.updateAllPriceDisplays();
    }
}

// --- 🌍 GLOBAL INSTANCE ---
export const currencyDisplayManager = new CurrencyDisplayManager();
