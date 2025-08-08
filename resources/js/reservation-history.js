/**
 * 🎯 Reservation History System
 * Sistema per la gestione della cronologia delle prenotazioni EGI
 */

class ReservationHistoryManager {
    constructor() {
        console.log('🚀 ReservationHistoryManager initialized');
        this.cache = new Map();
        this.currentPopup = null;
        this.isLoading = false;

        this.init();
    }

    /**
     * 🎯 Calcola il posizionamento intelligente del popup
     * @param {Element} button Il pulsante cronologia
     * @param {number} popupWidth Larghezza del popup (default: 350)
     * @param {number} popupHeight Altezza del popup (default: 400)
     * @returns {Object} Posizione ottimale {top, left, transform}
     */
    calculatePopupPosition(button, popupWidth = 350, popupHeight = 400) {
        const rect = button.getBoundingClientRect();
        const viewport = {
            width: window.innerWidth,
            height: window.innerHeight
        };

        // Margine di sicurezza dai bordi dello schermo
        const margin = 15;

        // Trova il container EGI (il div padre della card)
        const egiCard = button.closest('.bg-gray-800, .bg-white, [class*="egi"], .p-4, .p-6, .rounded, .shadow');
        const cardRect = egiCard ? egiCard.getBoundingClientRect() : rect;

        let top, left, transform = null;
        let arrowClass = null;

        // LOGICA DI POSIZIONAMENTO:
        // 1. IN ALTO rispetto al pulsante cronologia
        // 2. CENTRATO rispetto alla card EGI
        // 3. RESPONSIVE per mobile e edge cases

        // Posizione verticale: sopra il pulsante con margine
        top = rect.top - popupHeight - margin;

        // Se il popup va fuori dal top dello schermo, posiziona sotto
        if (top < margin) {
            top = rect.bottom + margin;
            transform = 'bottom center';
            arrowClass = 'arrow-top'; // Freccia verso l'alto (popup sotto il pulsante)
        } else {
            transform = 'top center';
            arrowClass = 'arrow-bottom'; // Freccia verso il basso (popup sopra il pulsante)
        }

        // Posizione orizzontale: centrato rispetto alla card EGI
        const cardCenterX = cardRect.left + (cardRect.width / 2);
        left = cardCenterX - (popupWidth / 2);

        // Dimensioni effettive del popup (può essere ridimensionato per mobile)
        let effectiveWidth = popupWidth;

        // Aggiustamenti per mobile e bordi schermo
        if (viewport.width <= 768) {
            // Mobile: centra nello schermo con margini
            left = margin;
            // Ridimensiona popup per mobile
            effectiveWidth = Math.min(popupWidth, viewport.width - (margin * 2));
            // Su mobile non usiamo frecce laterali
            arrowClass = arrowClass; // Mantieni freccia verticale
        } else {
            // Desktop: controlla bordi dello schermo
            if (left < margin) {
                left = margin;
                transform = 'top left';
                // Se il popup è molto spostato a sinistra, usa freccia destra
                if (left + effectiveWidth < cardCenterX - 50) {
                    arrowClass = 'arrow-right';
                }
            } else if (left + effectiveWidth > viewport.width - margin) {
                left = viewport.width - effectiveWidth - margin;
                transform = 'top right';
                // Se il popup è molto spostato a destra, usa freccia sinistra
                if (left > cardCenterX + 50) {
                    arrowClass = 'arrow-left';
                }
            }
        }

        // Controlla se il popup va fuori dal bottom dello schermo
        if (top + popupHeight > viewport.height - margin) {
            top = viewport.height - popupHeight - margin;
        }

        console.log(`📍 Popup position: top=${top}, left=${left}, transform=${transform}, arrow=${arrowClass}`);

        return {
            top: Math.max(margin, top),
            left: Math.max(margin, left),
            transform: transform,
            arrowClass: arrowClass
        };
    }

    init() {
        console.log('⚡ ReservationHistoryManager.init() called');
        if (document.readyState === 'loading') {
            console.log('📄 Document is loading, waiting for DOMContentLoaded...');
            document.addEventListener('DOMContentLoaded', () => {
                console.log('✅ DOMContentLoaded fired');
                this.bindEvents();
                this.checkInitialReservationStates();
            });
        } else {
            console.log('📄 Document already loaded');
            this.bindEvents();
            this.checkInitialReservationStates();
        }
    }

    bindEvents() {
        // Bind eventi per i pulsanti cronologia
        document.addEventListener('click', this.handleHistoryButtonClick.bind(this));
    }

    async checkInitialReservationStates() {
        console.log('🔍 Checking initial reservation states...');
        const buttons = document.querySelectorAll('.history-button');
        console.log('📊 Found history buttons:', buttons.length);

        buttons.forEach(button => {
            console.log('🔘 History button found:', button, 'EGI ID:', button.dataset.egiId);
        });
    }

    async handleButtonHover(event) {
        const button = event.target.closest('.reserve-button, .reserved-button');
        if (!button) return;

        const egiId = button.dataset.egiId;
        const isReserved = button.classList.contains('reserved-button');

        if (!egiId || !isReserved) return;

        // Delay per evitare popup premature
        button.hoverTimeout = setTimeout(async () => {
            await this.showReservationHistory(button, egiId);
        }, 500);
    }

    handleButtonLeave(event) {
        const button = event.target.closest('.reserve-button, .reserved-button');
        if (!button) return;

        // Cancella il timeout se esiste
        if (button.hoverTimeout) {
            clearTimeout(button.hoverTimeout);
            button.hoverTimeout = null;
        }

        // Nasconde il popup dopo un breve delay
        setTimeout(() => {
            this.hidePopup();
        }, 200);
    }

    async handleHistoryButtonClick(event) {
        const button = event.target.closest('.history-button');
        if (!button) return;

        event.preventDefault();
        const egiId = button.dataset.egiId;
        console.log('🕐 History button clicked for EGI:', egiId);

        if (egiId) {
            await this.showReservationHistory(button, egiId, true);
        }
    }

    async showReservationHistory(button, egiId, forceShow = false) {
        if (this.isLoading) return;

        try {
            this.isLoading = true;

            // Ottiene la cronologia delle prenotazioni
            const history = await this.getReservationHistory(egiId);

            // Crea e mostra il popup
            this.createHistoryPopup(button, history, forceShow);

        } catch (error) {
            console.error('Errore nel caricamento cronologia:', error);
            this.showErrorPopup(button, 'Errore nel caricamento della cronologia');
        } finally {
            this.isLoading = false;
        }
    }

    async getReservationHistory(egiId) {
        // Check cache first
        if (this.cache.has(egiId)) {
            return this.cache.get(egiId);
        }

        // Carica dalla API
        const response = await fetch(`/api/reservations/egi/${egiId}/history`);

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        const data = await response.json();

        // Salva in cache
        this.cache.set(egiId, data);

        return data;
    }

    createHistoryPopup(button, historyData, forceShow = false) {
        // Rimuove popup esistente
        this.hidePopup();

        const popup = document.createElement('div');
        popup.className = 'reservation-history-popup';

        popup.innerHTML = this.buildHistoryHTML(historyData);

        // Posizionamento intelligente del popup
        const position = this.calculatePopupPosition(button);

        // Stili CSS inline per garantire funzionamento
        popup.style.cssText = `
            position: fixed;
            top: ${position.top}px;
            left: ${position.left}px;
            z-index: 9999;
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
            border: 1px solid #374151;
            border-radius: 12px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.1);
            max-width: ${window.innerWidth <= 768 ? 'calc(100vw - 30px)' : '350px'};
            width: ${window.innerWidth <= 768 ? 'calc(100vw - 30px)' : 'auto'};
            max-height: ${window.innerHeight <= 600 ? '300px' : '400px'};
            overflow-y: auto;
            color: white;
            font-size: 14px;
            backdrop-filter: blur(10px);
            transition: all 0.2s ease;
            transform: translateY(-10px);
            opacity: 0;
            ${position.transform ? `transform-origin: ${position.transform};` : ''}
        `;

        // Aggiungi classe freccia in base alla posizione
        if (position.arrowClass) {
            popup.classList.add(position.arrowClass);
        }

        document.body.appendChild(popup);

        // Anima l'entrata
        requestAnimationFrame(() => {
            popup.style.transform = 'translateY(0)';
            popup.style.opacity = '1';
        });

        this.currentPopup = popup;

        // Auto-hide se non è forzato
        if (!forceShow) {
            popup.addEventListener('mouseenter', () => {
                if (this.hideTimeout) {
                    clearTimeout(this.hideTimeout);
                    this.hideTimeout = null;
                }
            });

            popup.addEventListener('mouseleave', () => {
                this.hideTimeout = setTimeout(() => {
                    this.hidePopup();
                }, 300);
            });
        } else {
            // Pulsante chiudi per popup forzato
            const closeButton = popup.querySelector('.close-popup');
            if (closeButton) {
                closeButton.addEventListener('click', () => {
                    this.hidePopup();
                });
            }
        }
    }

    buildHistoryHTML(historyData) {
        const reservations = historyData.reservations || [];
        const totalCount = reservations.length;

        let html = `
            <div class="p-4">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-white font-semibold flex items-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.414-1.414L11 9.586V6z" clip-rule="evenodd" />
                        </svg>
                        ${this.translate('egi.history.title')}
                    </h4>
                    <button class="close-popup text-gray-400 hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
        `;

        if (totalCount === 0) {
            html += `
                <div class="text-gray-400 text-center py-4">
                    ${this.translate('egi.history.no_reservations')}
                </div>
            `;
        } else {
            html += `
                <div class="text-xs text-gray-400 mb-3">
                    ${this.translatePlural('egi.history.total_reservations', totalCount)}
                </div>
                <div class="space-y-2 max-h-64 overflow-y-auto reservation-list">
            `;

            reservations.forEach((reservation, index) => {
                const isHighest = index === 0; // Primo elemento = priorità più alta
                const statusClass = isHighest ? 'bg-green-600/20 border-green-500/30' : 'bg-yellow-600/20 border-yellow-500/30';
                const statusText = isHighest ? this.translate('egi.history.current_highest') : this.translate('egi.history.superseded');
                const typeText = reservation.type === 'strong' ? this.translate('egi.history.type_strong') : this.translate('egi.history.type_weak');

                html += `
                    <div class="border rounded-lg p-3 ${statusClass}">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-medium ${isHighest ? 'text-green-300' : 'text-yellow-300'}">${statusText}</span>
                            <span class="text-xs text-gray-400">${typeText}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-white font-semibold">€${parseFloat(reservation.offer_amount_eur).toFixed(2)}</span>
                            <span class="text-xs text-gray-400">${this.formatDate(reservation.created_at)}</span>
                        </div>
                    </div>
                `;
            });

            html += '</div>';
        }

        html += '</div>';

        return html;
    }

    showErrorPopup(button, message) {
        this.hidePopup();

        const popup = document.createElement('div');
        popup.className = 'reservation-history-popup error';

        // Usa il sistema di posizionamento intelligente
        const position = this.calculatePopupPosition(button, 250, 120); // popup più piccolo per errori

        popup.innerHTML = `
            <div class="p-4 text-center">
                <svg class="w-8 h-8 mx-auto mb-2 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                <p class="text-red-200 text-sm">${message}</p>
            </div>
        `;

        popup.style.cssText = `
            position: fixed;
            top: ${position.top}px;
            left: ${position.left}px;
            z-index: 9999;
            background: linear-gradient(135deg, #7f1d1d 0%, #450a0a 100%);
            border: 1px solid #dc2626;
            border-radius: 12px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
            max-width: 250px;
            color: white;
            backdrop-filter: blur(10px);
            ${position.transform ? `transform-origin: ${position.transform};` : ''}
        `;

        // Aggiungi classe freccia per popup di errore
        if (position.arrowClass) {
            popup.classList.add(position.arrowClass);
        }

        document.body.appendChild(popup);
        this.currentPopup = popup;

        // Auto-remove dopo 3 secondi
        setTimeout(() => {
            this.hidePopup();
        }, 3000);
    }

    hidePopup() {
        if (this.currentPopup) {
            this.currentPopup.style.transform = 'translateY(-10px)';
            this.currentPopup.style.opacity = '0';

            setTimeout(() => {
                if (this.currentPopup && this.currentPopup.parentNode) {
                    this.currentPopup.parentNode.removeChild(this.currentPopup);
                }
                this.currentPopup = null;
            }, 200);
        }

        if (this.hideTimeout) {
            clearTimeout(this.hideTimeout);
            this.hideTimeout = null;
        }
    }

    formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('it-IT', {
            day: '2-digit',
            month: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    translate(key) {
        // Fallback per le traduzioni - in un'app reale useresti un sistema i18n
        const translations = {
            'egi.history.title': 'Cronologia Prenotazioni',
            'egi.history.no_reservations': 'Nessuna prenotazione trovata',
            'egi.history.current_highest': 'Priorità massima attuale',
            'egi.history.superseded': 'Priorità inferiore',
            'egi.history.type_strong': 'Forte',
            'egi.history.type_weak': 'Debole',
        };

        return translations[key] || key;
    }

    translatePlural(key, count) {
        if (count === 1) {
            return `1 prenotazione`;
        }
        return `${count} prenotazioni`;
    }
}

// Inizializza il manager
const reservationHistory = new ReservationHistoryManager();

// Esporta per uso globale
window.ReservationHistoryManager = reservationHistory;
