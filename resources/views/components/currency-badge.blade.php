{{--
💱 Currency Badge Component Template
Componente autonomo per il badge EUR → ALGO con TypeScript integrato
--}}

<div id="{{ $uniqueId }}" class="currency-badge-component {{ $responsiveClasses }} {{ $getPositionClasses() }}"
    data-size="{{ $size }}" data-position="{{ $position }}">

    <div
        class="inline-flex items-center bg-gradient-to-r from-slate-800/90 to-slate-700/90 backdrop-blur-sm border border-slate-600/50 rounded-lg shadow-lg {{ $getSizeClasses()['container'] }}">

        {{-- Status Dot --}}
        <div class="relative mr-2">
            <span id="{{ $getStatusDotId() }}"
                class="{{ $getSizeClasses()['dot'] }} bg-green-400 rounded-full block animate-pulse"></span>
            <span
                class="absolute inset-0 {{ $getSizeClasses()['dot'] }} bg-green-400 rounded-full animate-ping opacity-60"></span>
        </div>

        {{-- EUR → ALGO Display --}}
        <div class="flex items-center {{ $getSizeClasses()['spacing'] }}">
            <span class="{{ $getSizeClasses()['text'] }} font-bold text-white">EUR</span>
            <svg class="{{ $getSizeClasses()['arrow'] }} text-slate-400" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6">
                </path>
            </svg>
            <span class="{{ $getSizeClasses()['text'] }} font-bold text-emerald-400">ALGO</span>
        </div>

        {{-- Live Rate Display --}}
        <div class="flex items-center {{ $getSizeClasses()['spacing'] }}">
            <span id="{{ $getRateValueId() }}"
                class="font-mono {{ $getSizeClasses()['text'] }} font-medium text-white">--</span>
        </div>
    </div>
</div>

{{-- TypeScript integrato per gestione autonoma --}}
<script>
    (function() {
    'use strict';

    /**
     * 💱 Currency Badge Manager - Versione Autonoma
     * Gestisce un singolo badge della currency in modo completamente autonomo
     */
    class AutonomousCurrencyBadge {
        constructor(badgeElement) {
            this.badgeElement = badgeElement;
            this.uniqueId = badgeElement.id;
            this.size = badgeElement.dataset.size;
            this.position = badgeElement.dataset.position;

            // Riferimenti agli elementi interni
            this.statusDotElement = badgeElement.querySelector('[id$="-status-dot"]');
            this.rateValueElement = badgeElement.querySelector('[id$="-rate-value"]');

            this.currentRate = null;
            this.updateInterval = null;
            this.retryCount = 0;
            this.maxRetries = 3;

            this.init();
        }

        init() {
            console.log(`💱 Autonomous Currency Badge initialized: ${this.uniqueId}`);

            // Prima fetch immediata
            this.fetchAndUpdateRate();

            // Auto-refresh ogni 30 secondi
            this.startAutoUpdate();

            // Ascolta eventi globali di cambio valuta
            document.addEventListener('currency-changed', this.handleCurrencyChange.bind(this));
            document.addEventListener('user-logout', this.handleUserLogout.bind(this));
        }

        /**
         * Avvia l'aggiornamento automatico
         */
        startAutoUpdate() {
            this.updateInterval = setInterval(() => {
                this.fetchAndUpdateRate();
            }, 30000); // 30 secondi
        }

        /**
         * Ferma l'aggiornamento automatico
         */
        stopAutoUpdate() {
            if (this.updateInterval) {
                clearInterval(this.updateInterval);
                this.updateInterval = null;
            }
        }

        /**
         * Fetch del tasso di cambio EUR → ALGO
         */
        async fetchAndUpdateRate() {
            try {
                this.setLoadingState();

                const response = await fetch('/api/currency/rate/EUR', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const data = await response.json();

                if (data.success && data.data) {
                    const rate = data.data.rate_to_algo || 0;
                    const timestamp = data.data.timestamp || new Date().toISOString();

                    this.updateRate(rate, timestamp);
                    this.setSuccessState();
                    this.retryCount = 0; // Reset retry count on success
                } else {
                    throw new Error('Invalid API response format');
                }

            } catch (error) {
                console.error(`💱 Failed to fetch currency rate for ${this.uniqueId}:`, error);
                this.handleError(error);
            }
        }

        /**
         * Aggiorna il tasso visualizzato
         */
        updateRate(rate, timestamp) {
            if (!this.rateValueElement) return;

            const formattedRate = this.formatRate(rate);
            const hasChanged = this.currentRate !== rate;

            // Animazione se il valore è cambiato
            if (hasChanged && this.currentRate !== null) {
                this.animateValueChange(this.rateValueElement, formattedRate);
            } else {
                this.rateValueElement.textContent = formattedRate;
            }

            this.currentRate = rate;

            // Dispatch evento per notificare altri componenti
            document.dispatchEvent(new CustomEvent('currency-rate-updated', {
                detail: {
                    badgeId: this.uniqueId,
                    rate: rate,
                    formattedRate: formattedRate,
                    timestamp: timestamp,
                    size: this.size,
                    position: this.position
                }
            }));
        }

        /**
         * Formatta il tasso di cambio
         */
        formatRate(rate) {
            if (rate === 0 || !rate) return '--';

            // Formattazione professionale con decimali appropriati
            if (rate >= 1) {
                return rate.toFixed(4);
            } else if (rate >= 0.01) {
                return rate.toFixed(6);
            } else {
                return rate.toFixed(8);
            }
        }

        /**
         * Animazione per cambio di valore
         */
        animateValueChange(element, newValue) {
            element.style.opacity = '0.5';
            element.style.transform = 'scale(0.95)';
            element.style.transition = 'all 0.15s ease-out';

            setTimeout(() => {
                element.textContent = newValue;
                element.style.opacity = '1';
                element.style.transform = 'scale(1)';
                element.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
            }, 150);
        }

        /**
         * Stato di caricamento
         */
        setLoadingState() {
            if (this.statusDotElement) {
                this.statusDotElement.className = this.statusDotElement.className.replace('bg-green-400', 'bg-yellow-400');
            }
        }

        /**
         * Stato di successo
         */
        setSuccessState() {
            if (this.statusDotElement) {
                this.statusDotElement.className = this.statusDotElement.className.replace('bg-yellow-400', 'bg-green-400');
                this.statusDotElement.className = this.statusDotElement.className.replace('bg-red-400', 'bg-green-400');
            }

            // Flash di successo
            this.flashSuccess();
        }

        /**
         * Stato di errore
         */
        setErrorState() {
            if (this.statusDotElement) {
                this.statusDotElement.className = this.statusDotElement.className.replace('bg-green-400', 'bg-red-400');
                this.statusDotElement.className = this.statusDotElement.className.replace('bg-yellow-400', 'bg-red-400');
            }

            if (this.rateValueElement) {
                this.rateValueElement.textContent = 'ERR';
            }
        }

        /**
         * Animazione di successo
         */
        flashSuccess() {
            const container = this.badgeElement.querySelector('.inline-flex');
            if (container) {
                container.style.boxShadow = '0 20px 25px -5px rgba(16, 185, 129, 0.3), 0 10px 10px -5px rgba(16, 185, 129, 0.2)';
                setTimeout(() => {
                    container.style.boxShadow = '';
                    container.style.transition = 'box-shadow 1s ease-out';
                }, 500);
            }
        }

        /**
         * Gestione errori con retry logic
         */
        handleError(error) {
            this.setErrorState();

            this.retryCount++;
            if (this.retryCount <= this.maxRetries) {
                // Retry con delay crescente
                const delay = Math.pow(2, this.retryCount) * 1000; // 2s, 4s, 8s
                console.log(`💱 Retrying in ${delay}ms (attempt ${this.retryCount}/${this.maxRetries})`);

                setTimeout(() => {
                    this.fetchAndUpdateRate();
                }, delay);
            } else {
                console.error(`💱 Max retries reached for ${this.uniqueId}, stopping automatic updates`);
                this.stopAutoUpdate();
            }
        }

        /**
         * Gestisce eventi di cambio valuta globali
         */
        handleCurrencyChange(event) {
            // Per ora gestiamo solo EUR, ma in futuro potremmo supportare altre valute
            console.log(`💱 Currency change event received: ${this.uniqueId}`, event.detail);
            this.fetchAndUpdateRate();
        }

        /**
         * Gestisce il logout utente
         */
        handleUserLogout() {
            this.stopAutoUpdate();
            this.setErrorState();
        }

        /**
         * Cleanup del badge
         */
        destroy() {
            this.stopAutoUpdate();
            document.removeEventListener('currency-changed', this.handleCurrencyChange);
            document.removeEventListener('user-logout', this.handleUserLogout);
        }
    }

    // Auto-inizializzazione quando il DOM è pronto
    document.addEventListener('DOMContentLoaded', function() {
        const badgeElement = document.getElementById('{{ $uniqueId }}');
        if (badgeElement) {
            // Crea l'istanza del badge autonomo
            const autonomousBadge = new AutonomousCurrencyBadge(badgeElement);

            // Salva l'istanza per eventuali cleanup
            badgeElement._autonomousBadge = autonomousBadge;
        }
    });

    // Cleanup globale su beforeunload
    window.addEventListener('beforeunload', function() {
        const badgeElement = document.getElementById('{{ $uniqueId }}');
        if (badgeElement && badgeElement._autonomousBadge) {
            badgeElement._autonomousBadge.destroy();
        }
    });

})();
</script>
