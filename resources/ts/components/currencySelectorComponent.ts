/**
 * Currency Selector Component - Mobile First Implementation
 * Gestisce il badge valuta nell'header per desktop E mobile
 */

export class CurrencySelectorComponent {
    private currentCurrency: string = 'USD';
    private isDesktopDropdownOpen: boolean = false;
    private isMobileDropdownOpen: boolean = false;

    public async initialize(): Promise<void> {
        console.log('🌍 [Currency Selector] Initializing Mobile First...');
        this.setupEventListeners();
        await this.loadCurrentCurrency();
        this.fetchCurrentRate();

        // REMOVED: Auto-refresh polling che causava reset della valuta utente
        // setInterval(() => this.fetchCurrentRate(), 60000);
    }

    private setupEventListeners(): void {
        // === DESKTOP EVENT LISTENERS ===
        const desktopBadge = document.getElementById('currency-badge-desktop');
        if (desktopBadge) {
            desktopBadge.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleDesktopDropdown();
            });
        }

        // === MOBILE EVENT LISTENERS ===
        const mobileBadge = document.getElementById('currency-badge-mobile');
        if (mobileBadge) {
            mobileBadge.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleMobileDropdown();
            });
        }

        // === UNIVERSAL EVENT LISTENERS ===
        document.addEventListener('click', (e) => {
            const target = e.target as HTMLElement;

            // Desktop currency selection
            if (target.closest('.currency-option')) {
                const option = target.closest('.currency-option') as HTMLElement;
                const currency = option.dataset.currency;
                if (currency) this.selectCurrency(currency);
            }
            // Mobile currency selection
            else if (target.closest('.currency-option-mobile')) {
                const option = target.closest('.currency-option-mobile') as HTMLElement;
                const currency = option.dataset.currency;
                if (currency) this.selectCurrency(currency);
            }
            // Close dropdowns when clicking outside
            else if (!target.closest('#currency-badge-container-desktop') &&
                !target.closest('#currency-badge-container-mobile')) {
                this.closeAllDropdowns();
            }
        });

        // Escape key to close dropdowns
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeAllDropdowns();
            }
        });
    }

    private async fetchCurrentRate(): Promise<void> {
        try {
            const response = await fetch(`/api/currency/rate/${this.currentCurrency}`);
            const data = await response.json();

            if (data.success) {
                this.updateBadgeRates(data.data.rate_to_algo);
            }
        } catch (error) {
            console.error('❌ [Currency Selector] Failed to fetch rate:', error);
        }
    }

    private async selectCurrency(currency: string): Promise<void> {
        console.log(`🔄 [Currency Selector] Selecting currency: ${currency}`);

        this.currentCurrency = currency;
        this.updateCurrencyDisplays(currency);
        this.closeAllDropdowns();

        await this.fetchCurrentRate();

        // Salva preferenza per utenti autenticati
        if (this.isAuthenticated()) {
            await this.saveCurrencyPreference(currency);
        }

        // Trigger event per aggiornare prezzi nella pagina
        this.dispatchCurrencyChangeEvent(currency);
    }

    // === DESKTOP DROPDOWN METHODS ===
    private toggleDesktopDropdown(): void {
        const dropdown = document.getElementById('currency-badge-dropdown');
        const arrow = document.getElementById('currency-badge-arrow');

        if (!dropdown) return;

        // Chiudi mobile se aperto
        this.closeMobileDropdown();

        this.isDesktopDropdownOpen = !this.isDesktopDropdownOpen;

        if (this.isDesktopDropdownOpen) {
            dropdown.classList.remove('opacity-0', 'invisible', 'scale-95');
            dropdown.classList.add('opacity-100', 'visible', 'scale-100');
            if (arrow) arrow.style.transform = 'rotate(180deg)';
        } else {
            dropdown.classList.add('opacity-0', 'invisible', 'scale-95');
            dropdown.classList.remove('opacity-100', 'visible', 'scale-100');
            if (arrow) arrow.style.transform = 'rotate(0deg)';
        }
    }

    private closeDesktopDropdown(): void {
        const dropdown = document.getElementById('currency-badge-dropdown');
        const arrow = document.getElementById('currency-badge-arrow');

        if (dropdown) {
            dropdown.classList.add('opacity-0', 'invisible', 'scale-95');
            dropdown.classList.remove('opacity-100', 'visible', 'scale-100');
        }
        if (arrow) arrow.style.transform = 'rotate(0deg)';

        this.isDesktopDropdownOpen = false;
    }

    // === MOBILE DROPDOWN METHODS ===
    private toggleMobileDropdown(): void {
        const dropdown = document.getElementById('currency-badge-dropdown-mobile');
        const arrow = document.getElementById('currency-badge-arrow-mobile');

        if (!dropdown) return;

        // Chiudi desktop se aperto
        this.closeDesktopDropdown();

        this.isMobileDropdownOpen = !this.isMobileDropdownOpen;

        if (this.isMobileDropdownOpen) {
            dropdown.classList.remove('opacity-0', 'invisible', 'scale-95');
            dropdown.classList.add('opacity-100', 'visible', 'scale-100');
            if (arrow) arrow.style.transform = 'rotate(180deg)';
        } else {
            dropdown.classList.add('opacity-0', 'invisible', 'scale-95');
            dropdown.classList.remove('opacity-100', 'visible', 'scale-100');
            if (arrow) arrow.style.transform = 'rotate(0deg)';
        }
    }

    private closeMobileDropdown(): void {
        const dropdown = document.getElementById('currency-badge-dropdown-mobile');
        const arrow = document.getElementById('currency-badge-arrow-mobile');

        if (dropdown) {
            dropdown.classList.add('opacity-0', 'invisible', 'scale-95');
            dropdown.classList.remove('opacity-100', 'visible', 'scale-100');
        }
        if (arrow) arrow.style.transform = 'rotate(0deg)';

        this.isMobileDropdownOpen = false;
    }

    private closeAllDropdowns(): void {
        this.closeDesktopDropdown();
        this.closeMobileDropdown();
    }

    // === UPDATE METHODS ===
    private updateCurrencyDisplays(currency: string): void {
        // Desktop
        const desktopSymbol = document.getElementById('currency-symbol');
        if (desktopSymbol) desktopSymbol.textContent = currency;

        // Mobile
        const mobileSymbol = document.getElementById('currency-symbol-mobile');
        if (mobileSymbol) mobileSymbol.textContent = currency;
    }

    private updateBadgeRates(rate: number): void {
        // Desktop
        const desktopRate = document.getElementById('currency-rate-value');
        if (desktopRate) desktopRate.textContent = rate.toFixed(4);

        // Mobile
        const mobileRate = document.getElementById('currency-rate-value-mobile');
        if (mobileRate) mobileRate.textContent = rate.toFixed(4);

        // Update dropdown rates
        this.updateDropdownRates(rate);
        this.updateLastUpdatedTime();
    }

    private updateDropdownRates(rate: number): void {
        // Desktop dropdown
        const desktopRateElement = document.querySelector(`[data-currency="${this.currentCurrency}"] .currency-rate`);
        if (desktopRateElement) desktopRateElement.textContent = rate.toFixed(4);

        // Mobile dropdown
        const mobileRateElement = document.querySelector(`[data-currency="${this.currentCurrency}"] .currency-rate-mobile`);
        if (mobileRateElement) mobileRateElement.textContent = rate.toFixed(4);
    }

    private updateLastUpdatedTime(): void {
        const now = new Date().toLocaleTimeString('en-US', { hour12: false, hour: '2-digit', minute: '2-digit' });

        // Desktop
        const desktopTime = document.getElementById('currency-last-updated');
        if (desktopTime) desktopTime.textContent = now;

        // Mobile
        const mobileTime = document.getElementById('currency-last-updated-mobile');
        if (mobileTime) mobileTime.textContent = now;
    }

    // === UTILITY METHODS ===
    private async loadCurrentCurrency(): Promise<void> {
        try {
            // METODO 1: Leggi dal meta tag server-side (più affidabile)
            const currencyMeta = document.querySelector('meta[name="user-preferred-currency"]');
            const serverCurrency = currencyMeta?.getAttribute('content');

            if (serverCurrency && serverCurrency !== 'USD') {
                // L'utente ha una valuta specifica salvata nel DB
                this.currentCurrency = serverCurrency;
                console.log(`🌍 [Currency Selector] Loaded from meta tag: ${this.currentCurrency}`);
                this.updateCurrencyDisplays(this.currentCurrency);
                return;
            }

            // METODO 2: Leggi dai simboli nel DOM (già renderizzati server-side)
            const desktopSymbol = document.getElementById('currency-symbol');
            const mobileSymbol = document.getElementById('currency-symbol-mobile');

            const domCurrency = desktopSymbol?.textContent?.trim() ||
                mobileSymbol?.textContent?.trim() ||
                'USD';

            this.currentCurrency = domCurrency;
            console.log(`🌍 [Currency Selector] Loaded from DOM: ${this.currentCurrency}`);
            this.updateCurrencyDisplays(this.currentCurrency);

        } catch (error) {
            console.warn('🌍 [Currency Selector] Error loading currency, using USD:', error);
            this.currentCurrency = 'USD';
            this.updateCurrencyDisplays(this.currentCurrency);
        }
    }

    private isAuthenticated(): boolean {
        const metaTag = document.querySelector('meta[name="user-authenticated"]');
        const isAuth = metaTag?.getAttribute('content') === 'true';

        // Log per debug
        console.log('🌍 [Currency Selector] Auth check:', {
            metaTag: metaTag,
            content: metaTag?.getAttribute('content'),
            isAuthenticated: isAuth
        });

        return isAuth;
    }

    private async saveCurrencyPreference(currency: string): Promise<void> {
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            const response = await fetch('/user/preferences/currency', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || ''
                },
                body: JSON.stringify({ currency })
            });

            if (response.ok) {
                console.log('✅ [Currency Selector] Preference saved');
            } else {
                console.error('❌ [Currency Selector] Failed to save preference:', response.status);
            }
        } catch (error) {
            console.warn('⚠️ [Currency Selector] Failed to save preference:', error);
        }
    }

    private dispatchCurrencyChangeEvent(currency: string): void {
        const event = new CustomEvent('currencyChanged', {
            detail: { currency },
            bubbles: true
        });
        document.dispatchEvent(event);
    }
}
