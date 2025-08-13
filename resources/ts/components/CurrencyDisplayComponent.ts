/**
 *
 * Componente per mostrare prezzi in diverse valute con conversione real-time.
 * Integrazione con il sistema "Think FIAT, Operate ALGO" di EGI.
 *
 * @package EGI Multi-Currency System
 * @author Fabio Cherici
 * @version 1.0.0
 * @date 2025-08-13
 */

interface CurrencyRate {
    rate: number;
    timestamp: string;
    is_cached?: boolean;
}

interface PriceDisplayOptions {
    showOriginalCurrency?: boolean;
    showConversionNote?: boolean;
    formatStyle?: 'compact' | 'standard' | 'scientific';
    minimumFractionDigits?: number;
    maximumFractionDigits?: number;
}

export class CurrencyDisplayComponent {
    private currentCurrency: string = 'USD';
    private exchangeRates: Map<string, CurrencyRate> = new Map();
    private displayElements: Set<HTMLElement> = new Set();

    // Ottimizzazioni Performance
    private initializeTimeout: number | null = null;
    private currencyChangeTimeout: number | null = null;
    private lastApiCall: number = 0;
    private readonly MIN_API_INTERVAL = 5000; // 5 secondi minimo tra API calls

    public async initialize(): Promise<void> {
        console.log('💰 [Currency Display] Initializing Multi-Currency Display System...');

        // Carica valuta utente corrente PRIMA di tutto
        await this.loadUserCurrency();
        console.log(`💰 [Currency Display] User currency loaded: ${this.currentCurrency}`);

        // Inizializza elementi price display esistenti
        this.initializeExistingElements();

        // Listen per cambi di valuta dal selettore
        document.addEventListener('currencyChanged', (e: any) => {
            this.onCurrencyChanged(e.detail.currency);
        });

        // Auto-refresh rates ogni 2 minuti
        setInterval(() => this.refreshAllRates(), 120000);
    }

    /**
     * Inizializzazione elementi con debounce per evitare overhead
     */
    private debouncedInitialize(): void {
        if (this.initializeTimeout) {
            clearTimeout(this.initializeTimeout);
        }

        this.initializeTimeout = window.setTimeout(async () => {
            await this.initializeExistingElements();
        }, 300); // 300ms debounce
    }

    /**
     * Cambio valuta con throttling per limitare API calls
     */
    private throttledCurrencyChange(newCurrency: string): void {
        const now = Date.now();

        // Throttle: minimo 5 secondi tra cambi valuta
        if (now - this.lastApiCall < this.MIN_API_INTERVAL) {
            console.log('💰 [Currency Display] Currency change throttled, waiting...');

            if (this.currencyChangeTimeout) {
                clearTimeout(this.currencyChangeTimeout);
            }

            this.currencyChangeTimeout = window.setTimeout(() => {
                this.onCurrencyChanged(newCurrency);
            }, this.MIN_API_INTERVAL - (now - this.lastApiCall));

            return;
        }

        this.lastApiCall = now;
        this.onCurrencyChanged(newCurrency);
    }

    /**
     * Registra un elemento per la visualizzazione del prezzo in multi-currency
     *
     * @param element - L'elemento HTML che contiene il prezzo
     * @param originalPrice - Il prezzo originale in EUR (base currency del sistema)
     * @param originalCurrency - La valuta originale (default: 'EUR')
     * @param options - Opzioni per il display
     */
    public registerPriceElement(
        element: HTMLElement,
        originalPrice: number,
        originalCurrency: string = 'EUR',
        options: PriceDisplayOptions = {}
    ): void {

        // Imposta attributi data per tracking
        element.setAttribute('data-original-price', originalPrice.toString());
        element.setAttribute('data-original-currency', originalCurrency);
        element.setAttribute('data-currency-display', 'true');

        // Store options
        if (options) {
            element.setAttribute('data-display-options', JSON.stringify(options));
        }

        this.displayElements.add(element);

        // REMOVED: Non fare update immediato! Causa cascata di API calls
        // this.updatePriceElement(element);

        console.log('💰 [Currency Display] Registered price element (deferred update):', {
            original_price: originalPrice,
            original_currency: originalCurrency,
            target_currency: this.currentCurrency
        });
    }

    /**
     * Aggiorna automaticamente tutti gli elementi price trovati nel DOM
     */
    public async initializeExistingElements(): Promise<void> {
        // Cerca elementi con data-price attribute
        const priceElements = document.querySelectorAll('[data-price]');

        console.log(`💰 [Currency Display] Found ${priceElements.length} price elements`);

        if (priceElements.length === 0) {
            console.log('💰 [Currency Display] No price elements found, skipping initialization');
            return;
        }

        // Registra tutti gli elementi SENZA update immediato
        priceElements.forEach((element) => {
            const htmlElement = element as HTMLElement;
            const price = parseFloat(htmlElement.getAttribute('data-price') || '0');
            const currency = htmlElement.getAttribute('data-currency') || 'EUR';

            if (price > 0) {
                this.registerPriceElement(htmlElement, price, currency);
            }
        });

        // Batch update: aggiorna tutti insieme alla fine
        console.log(`💰 [Currency Display] Batch updating ${this.displayElements.size} registered elements`);
        await this.updateAllPriceElements();
    }

    /**
     * Gestore cambio valuta dal selettore
     */
    private async onCurrencyChanged(newCurrency: string): Promise<void> {
        console.log('💰 [Currency Display] Currency changed to:', newCurrency);

        this.currentCurrency = newCurrency;

        // Aggiorna tutti gli elementi price
        await this.updateAllPriceElements();
    }

    /**
     * Carica la valuta utente corrente dai meta tag server-side (no API calls)
     */
    private async loadUserCurrency(): Promise<void> {
        try {
            // METODO 1: Leggi dal currency-symbol del header (già popolato server-side)
            const currencySymbolElement = document.getElementById('currency-symbol');
            if (currencySymbolElement && currencySymbolElement.textContent?.trim()) {
                this.currentCurrency = currencySymbolElement.textContent.trim();
                console.log(`💰 [Currency Display] Loaded from DOM currency-symbol: ${this.currentCurrency}`);
                return;
            }

            // FALLBACK mobile
            const currencySymbolMobile = document.getElementById('currency-symbol-mobile');
            if (currencySymbolMobile && currencySymbolMobile.textContent?.trim()) {
                this.currentCurrency = currencySymbolMobile.textContent.trim();
                console.log(`💰 [Currency Display] Loaded from DOM currency-symbol-mobile: ${this.currentCurrency}`);
                return;
            }

            // METODO 2: Leggi dal meta tag server-side come fallback
            const currencyMeta = document.querySelector('meta[name="user-preferred-currency"]');
            const serverCurrency = currencyMeta?.getAttribute('content');

            if (serverCurrency) {
                this.currentCurrency = serverCurrency;
                console.log(`💰 [Currency Display] Loaded from meta tag: ${this.currentCurrency}`);
                return;
            }

            // METODO 3: Prova localStorage come cache
            const cached = localStorage.getItem('user_preferred_currency');
            if (cached) {
                this.currentCurrency = cached;
                console.log(`💰 [Currency Display] Using cached currency: ${cached}`);
                return;
            }

            // FALLBACK: Default USD
            console.log('💰 [Currency Display] Using default USD');
            this.currentCurrency = 'USD';

        } catch (error) {
            console.warn('💰 [Currency Display] Error loading currency, using USD:', error);
            this.currentCurrency = 'USD';
        }
    }    /**
     * Aggiorna tutti gli elementi price con la nuova valuta (BATCH OPTIMIZED)
     */
    private async updateAllPriceElements(): Promise<void> {
        if (this.displayElements.size === 0) return;

        console.log(`💰 [Currency Display] Batch updating ${this.displayElements.size} elements`);

        // Pre-fetch tutti i tassi di cambio necessari in una volta sola
        const currencyPairs = new Set<string>();
        for (const element of this.displayElements) {
            const originalCurrency = element.getAttribute('data-original-currency') || 'EUR';
            if (originalCurrency !== this.currentCurrency) {
                currencyPairs.add(`${originalCurrency}-${this.currentCurrency}`);
            }
        }

        // Carica tutti i tassi necessari in parallelo
        const ratePromises = Array.from(currencyPairs).map(pair => {
            const [from, to] = pair.split('-');
            return this.getExchangeRate(from, to).then(rate => ({ pair, rate }));
        });

        const rates = await Promise.all(ratePromises);
        const rateMap = new Map();
        rates.forEach(({ pair, rate }) => {
            if (rate) rateMap.set(pair, rate);
        });

        // Aggiorna tutti gli elementi usando i tassi pre-caricati
        for (const element of this.displayElements) {
            this.updatePriceElementWithRate(element, rateMap);
        }
    }

    /**
     * Aggiorna un singolo elemento usando tassi pre-caricati (no API calls)
     */
    private updatePriceElementWithRate(element: HTMLElement, rateMap: Map<string, number>): void {
        try {
            const originalPrice = parseFloat(element.getAttribute('data-original-price') || '0');
            const originalCurrency = element.getAttribute('data-original-currency') || 'EUR';
            const optionsStr = element.getAttribute('data-display-options') || '{}';
            const options: PriceDisplayOptions = JSON.parse(optionsStr);

            if (originalPrice === 0) return;

            // Se stessa valuta, nessuna conversione necessaria
            if (originalCurrency === this.currentCurrency) {
                const formattedPrice = this.formatPrice(originalPrice, this.currentCurrency, options);
                element.innerHTML = formattedPrice;
                return;
            }

            // Usa tasso pre-caricato
            const pairKey = `${originalCurrency}-${this.currentCurrency}`;
            const rate = rateMap.get(pairKey);

            if (!rate) {
                console.warn(`💰 [Currency Display] No rate available for ${pairKey}`);
                return;
            }

            const convertedPrice = this.convertPrice(originalPrice, originalCurrency, this.currentCurrency, rate);
            const formattedPrice = this.formatPrice(convertedPrice, this.currentCurrency, options);
            element.innerHTML = formattedPrice;

        } catch (error) {
            console.error('💰 [Currency Display] Error updating element:', error);
        }
    }

    /**
     * Ottiene il tasso di cambio tra due valute con cache ottimizzata
     */
    private async getExchangeRate(fromCurrency: string, toCurrency: string): Promise<CurrencyRate | null> {
        const cacheKey = `${fromCurrency}_${toCurrency}`;

        // Controlla cache con TTL esteso
        if (this.exchangeRates.has(cacheKey)) {
            const cached = this.exchangeRates.get(cacheKey)!;
            const age = Date.now() - new Date(cached.timestamp).getTime();

            // Cache valida per 5 minuti invece che 1 minuto
            if (age < 300000) { // 5 minuti = 300,000 ms
                return cached;
            }
        }

        // Rate limiting: evita chiamate API troppo frequenti
        const now = Date.now();
        if (now - this.lastApiCall < this.MIN_API_INTERVAL) {
            console.log('💰 [Currency Display] API call rate limited, using cache or skipping');
            return this.exchangeRates.get(cacheKey) || null;
        }

        try {
            // Per semplicità, usiamo sempre ALGO come valuta di conversione intermedia
            let rate: number;

            if (fromCurrency === 'EUR') {
                // EUR -> toCurrency via ALGO
                const eurToAlgo = await this.fetchAlgoRate('EUR');
                const algoToTarget = await this.fetchAlgoRate(toCurrency);
                if (!eurToAlgo || !algoToTarget) return null;
                rate = algoToTarget / eurToAlgo;
            } else if (toCurrency === 'EUR') {
                // fromCurrency -> EUR via ALGO
                const fromToAlgo = await this.fetchAlgoRate(fromCurrency);
                const algoToEur = await this.fetchAlgoRate('EUR');
                if (!fromToAlgo || !algoToEur) return null;
                rate = algoToEur / fromToAlgo;
            } else {
                // fromCurrency -> toCurrency via ALGO
                const fromToAlgo = await this.fetchAlgoRate(fromCurrency);
                const algoToTarget = await this.fetchAlgoRate(toCurrency);
                if (!fromToAlgo || !algoToTarget) return null;
                rate = algoToTarget / fromToAlgo;
            }

            const rateData: CurrencyRate = {
                rate,
                timestamp: new Date().toISOString(),
                is_cached: false
            };

            this.exchangeRates.set(cacheKey, rateData);
            this.lastApiCall = now; // Track API call timing

            return rateData;

        } catch (error) {
            console.error('💰 [Currency Display] Error fetching exchange rate:', error);
            return null;
        }
    }

    /**
     * Ottiene il tasso ALGO per una specifica valuta FIAT
     */
    private async fetchAlgoRate(currency: string): Promise<number | null> {
        try {
            const response = await fetch(`/api/currency/rate/${currency}`);
            if (!response.ok) return null;

            const data = await response.json();
            return data.data?.rate_to_algo || null;
        } catch (error) {
            console.error('💰 [Currency Display] Error fetching ALGO rate:', error);
            return null;
        }
    }

    /**
     * Converte un prezzo da una valuta all'altra
     */
    private convertPrice(amount: number, fromCurrency: string, toCurrency: string, exchangeRate: number): number {
        return amount * exchangeRate;
    }

    /**
     * Formatta un prezzo per la visualizzazione
     */
    private formatPrice(amount: number, currency: string, options: PriceDisplayOptions = {}): string {
        const formatter = new Intl.NumberFormat('it-IT', {
            style: 'currency',
            currency: currency,
            notation: options.formatStyle === 'compact' ? 'compact' : 'standard',
            minimumFractionDigits: options.minimumFractionDigits ?? 2,
            maximumFractionDigits: options.maximumFractionDigits ?? 2,
        });

        return formatter.format(amount);
    }

    /**
     * Refresh ottimizzato: solo i tassi scaduti, non tutti
     */
    private async refreshAllRates(): Promise<void> {
        console.log('💰 [Currency Display] Refreshing expired exchange rates only...');

        const now = Date.now();
        let expiredRates = 0;

        // Rimuovi solo i tassi scaduti (>10 minuti)
        for (const [key, rate] of this.exchangeRates.entries()) {
            const age = now - new Date(rate.timestamp).getTime();
            if (age > 600000) { // 10 minuti
                this.exchangeRates.delete(key);
                expiredRates++;
            }
        }

        console.log(`💰 [Currency Display] Removed ${expiredRates} expired rates`);

        // Re-update solo se necessario e se ci sono elementi
        if (expiredRates > 0 && this.displayElements.size > 0) {
            await this.updateAllPriceElements();
        }
    }

    /**
     * Utility: Converte un elemento esistente in currency display
     */
    public static convertElement(element: HTMLElement, options?: PriceDisplayOptions): void {
        const text = element.textContent || '';
        const priceMatch = text.match(/([€$£¥]?)\s*(\d{1,3}(?:[.,]\d{3})*(?:[.,]\d{2})?)/);

        if (priceMatch) {
            const price = parseFloat(priceMatch[2].replace(/[,\.]/g, '.').replace(/\.(?=.*\.)/g, ''));
            const currencySymbol = priceMatch[1];
            let currency = 'EUR'; // default

            if (currencySymbol === '$') currency = 'USD';
            else if (currencySymbol === '£') currency = 'GBP';
            else if (currencySymbol === '¥') currency = 'JPY';

            // Registra elemento
            window.currencyDisplay?.registerPriceElement(element, price, currency, options);
        }
    }
}

// Global instance
declare global {
    interface Window {
        currencyDisplay: CurrencyDisplayComponent;
    }
}

// Auto-initialize intelligente: solo se ci sono elementi price nella pagina
document.addEventListener('DOMContentLoaded', async () => {
    // Verifica se ci sono elementi che potrebbero beneficiare del currency display
    const hasPriceElements = document.querySelectorAll('[data-price], .price, [class*="price"], [class*="cost"]').length > 0;
    const hasCurrencySelector = document.getElementById('currency-badge-desktop') !== null;

    if (hasPriceElements || hasCurrencySelector) {
        console.log('💰 [Currency Display] Auto-initializing - found price elements or currency selector');
        window.currencyDisplay = new CurrencyDisplayComponent();
        await window.currencyDisplay.initialize();
        console.log('💰 [Currency Display] Auto-initialization completed');
    } else {
        console.log('💰 [Currency Display] Auto-init skipped - no price elements detected');
    }
});
