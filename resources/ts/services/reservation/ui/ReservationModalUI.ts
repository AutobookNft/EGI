/**
 * 📜 Reservation Modal UI Component
 * 🎯 Purpose: Handle modal UI rendering and DOM interactions
 * 🧱 Core Logic: Single responsibility for UI management
 * 🎨 Features: Modal creation, form rendering, event handling
 *
 * @version 1.0.0
 * @date 2025-08-22
 * @author GitHub Copilot for Fabio Cherici
 */

import { ModalConfig, ValidationError } from '../types';

/**
 * UI component for reservation modal
 */
export class ReservationModalUI {
    private modalElement: HTMLElement | null = null;
    private onSubmitCallback?: (formData: FormData) => void;
    private onCloseCallback?: () => void;

    /**
     * Show modal with configuration
     */
    show(config: ModalConfig): void {
        this.createModal(config);
        this.attachEvents();
        this.modalElement?.classList.add('show');
    }

    /**
     * Hide modal
     */
    hide(): void {
        if (this.modalElement) {
            this.modalElement.classList.remove('show');
            setTimeout(() => this.destroy(), 300); // Wait for animation
        }
    }

    /**
     * Set submit callback
     */
    onSubmit(callback: (formData: FormData) => void): void {
        this.onSubmitCallback = callback;
    }

    /**
     * Set close callback
     */
    onClose(callback: () => void): void {
        this.onCloseCallback = callback;
    }

    /**
     * Display validation errors
     */
    showErrors(errors: ValidationError[]): void {
        // TODO: Move error display logic from original file
        console.log('TODO: Display errors', errors);
    }

    /**
     * Clear all errors
     */
    clearErrors(): void {
        // TODO: Move error clearing logic from original file
        console.log('TODO: Clear errors');
    }

    /**
     * Set loading state
     */
    setLoading(isLoading: boolean): void {
        const submitButton = this.modalElement?.querySelector('[data-submit-btn]') as HTMLButtonElement;
        if (submitButton) {
            submitButton.disabled = isLoading;
            submitButton.textContent = isLoading ? 'Processing...' : 'Submit Reservation';
        }
    }

    /**
     * Update ALGO rate display
     */
    updateAlgoRate(rate: number | null): void {
        const rateElement = this.modalElement?.querySelector('[data-algo-rate]');
        if (rateElement) {
            rateElement.textContent = rate ? `1 ALGO = €${rate.toFixed(4)}` : 'Rate unavailable';
        }
    }

    /**
     * Create modal HTML structure
     */
    private createModal(config: ModalConfig): void {
        // TODO: Move modal creation logic from original file
        // This is a massive HTML template that needs to be extracted
        this.modalElement = document.createElement('div');
        this.modalElement.className = 'reservation-modal';
        this.modalElement.innerHTML = this.getModalTemplate(config);

        document.body.appendChild(this.modalElement);
    }

    /**
     * Get modal HTML template
     */
    private getModalTemplate(config: ModalConfig): string {
        // TODO: Extract the huge HTML template from original file
        return `
            <div class="modal-backdrop">
                <div class="modal-content">
                    <h2>${config.title}</h2>
                    <form data-reservation-form>
                        <!-- TODO: Move form HTML from original file -->
                        <div class="form-group">
                            <label>Amount (EUR)</label>
                            <input type="number" name="offer_amount_fiat" required>
                        </div>
                        <div class="form-group">
                            <input type="checkbox" name="terms_accepted" required>
                            <label>I accept terms and conditions</label>
                        </div>
                        <div class="form-actions">
                            <button type="button" data-close-btn>Cancel</button>
                            <button type="submit" data-submit-btn>Submit Reservation</button>
                        </div>
                    </form>
                    <div data-algo-rate></div>
                </div>
            </div>
        `;
    }

    /**
     * Attach event listeners
     */
    private attachEvents(): void {
        if (!this.modalElement) return;

        // Form submission
        const form = this.modalElement.querySelector('[data-reservation-form]') as HTMLFormElement;
        form?.addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(form);
            this.onSubmitCallback?.(formData);
        });

        // Close button
        const closeBtn = this.modalElement.querySelector('[data-close-btn]');
        closeBtn?.addEventListener('click', () => {
            this.hide();
            this.onCloseCallback?.();
        });

        // Backdrop click
        const backdrop = this.modalElement.querySelector('.modal-backdrop');
        backdrop?.addEventListener('click', (e) => {
            if (e.target === backdrop) {
                this.hide();
                this.onCloseCallback?.();
            }
        });
    }

    /**
     * Destroy modal element
     */
    private destroy(): void {
        if (this.modalElement) {
            this.modalElement.remove();
            this.modalElement = null;
        }
    }

    /**
     * Mostra il modal di successo per pre-launch
     */
    showPreLaunchSuccessModal(): void {
        // Rimuovi eventuali modal esistenti
        document.querySelectorAll('#pre-launch-success-modal').forEach(modal => modal.remove());

        // Crea il contenuto del modal
        const modalHtml = `
            <div id="pre-launch-success-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75">
                <div class="bg-white rounded-lg shadow-lg max-w-md mx-4 p-6">
                    <div class="text-center">
                        <div class="mb-4">
                            <svg class="w-16 h-16 mx-auto text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>

                        <h2 class="text-2xl font-bold text-gray-900 mb-2">🎉 Prenotazione Confermata!</h2>

                        <p class="text-gray-600 mb-6">
                            La tua prenotazione è stata registrata con successo.
                            Riceverai una notifica quando la criptovaluta sarà disponibile per il lancio.
                        </p>

                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">
                                        <strong>Importante:</strong> La prenotazione non è ancora definitiva.
                                        Potrai completare l'acquisto quando la criptovaluta sarà lanciata.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <button onclick="document.getElementById('pre-launch-success-modal').remove()"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-200">
                            Perfetto!
                        </button>
                    </div>
                </div>
            </div>
        `;

        // Aggiungi il modal al DOM
        document.body.insertAdjacentHTML('beforeend', modalHtml);
    }

    /**
     * Aggiorna le card EGI-CARD-LIST dopo una prenotazione
     */
    handleEgiCardListUpdate(egiCard: Element, response: any): void {
        console.log('🎯 INIZIO GESTIONE EGI-CARD-LIST UPDATE');

        // 💰 Aggiorna il prezzo prima
        if (response.data?.reservation?.offer_amount_fiat) {
            const newPrice = parseFloat(response.data.reservation.offer_amount_fiat.toString()).toFixed(2);
            console.log(`💰 Aggiornamento prezzo per egi-card-list: €${newPrice}`);

            const priceElements = egiCard.querySelectorAll('[data-price-display]');
            Array.from(priceElements).forEach((el) => {
                if (el instanceof HTMLElement) {
                    const oldText = el.textContent?.trim() || '';
                    console.log(`💰 PREZZO [egi-card-list]: "${oldText}" → "€${newPrice}"`);
                    el.textContent = `€${newPrice}`;

                    // Evidenziazione visiva
                    el.style.backgroundColor = '#fef3c7';
                    el.style.fontWeight = 'bold';
                    el.style.color = '#d97706';
                    setTimeout(() => {
                        el.style.backgroundColor = '';
                        el.style.fontWeight = '';
                        el.style.color = '';
                    }, 2000);
                }
            });
        }

        // 👤 Gestisce la sostituzione della sezione "Da Attivare" con avatar+attivatore
        const availableSection = egiCard.querySelector('[data-activation-status="available"]');

        if (availableSection) {
            console.log('✅ TROVATA SEZIONE DA ATTIVARE - Sostituisco con avatar+attivatore');

            // 📋 PRENDI I DATI DELL'UTENTE DALLA RESPONSE
            const userDetails = response.data?.user;
            console.log('👤 User details per egi-card-list:', userDetails);

            // 🎯 CALCOLA IL NOME DELL'ATTIVATORE
            let userName = 'Utente'; // Fallback generico
            if (userDetails?.name) {
                userName = `${userDetails.name}`;
            } else if (userDetails?.wallet) {
                userName = userDetails.wallet.substring(0, 12) + '...';
            } else {
                // 🔄 Fallback: prova a prendere l'utente autenticato attuale
                const currentUser = (window as any).user || (window as any).Laravel?.user;
                if (currentUser?.name && currentUser?.last_name) {
                    userName = `${currentUser.name} ${currentUser.last_name}`;
                }
            }

            // 👤 Avatar e status commissioner
            const isCommissioner = userDetails?.is_commissioner || false;
            const avatarUrl = userDetails?.avatar || null;

            console.log('� DEBUG AVATAR (egi-card-list):', {
                isCommissioner,
                avatarUrl,
                userName
            });

            // Crea la nuova sezione con avatar+attivatore
            const newActivatorSection = document.createElement('div');
            newActivatorSection.className = 'flex items-center gap-2 mb-1 text-sm';
            newActivatorSection.setAttribute('data-activation-status', 'activated');

            // Avatar con logica corretta
            let avatarElement = '';
            if (avatarUrl) {
                avatarElement = `<img src="${avatarUrl}" alt="${userName}" class="object-cover w-4 h-4 border rounded-full shadow-sm border-green-400/30">`;
            } else if (isCommissioner) {
                avatarElement = `
                    <div class="flex items-center justify-center w-4 h-4 bg-green-500 rounded-full shadow-sm">
                        <svg class="w-2 h-2 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                        </svg>
                    </div>
                `;
            } else {
                avatarElement = `
                    <div class="flex items-center justify-center w-4 h-4 bg-gray-600 rounded-full">
                        <svg class="w-2 h-2 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                        </svg>
                    </div>
                `;
            }

            newActivatorSection.innerHTML = `
                ${avatarElement}
                <span class="font-medium text-green-300" data-activator-name>${userName}</span>
                <span class="text-xs text-gray-400">(Attivatore)</span>
            `;

            // Sostituisci la sezione "Da Attivare" con quella dell'attivatore
            availableSection.parentNode?.replaceChild(newActivatorSection, availableSection);

            console.log('✅ SEZIONE "DA ATTIVARE" SOSTITUITA CON AVATAR+ATTIVATORE');

            // Evidenziazione visiva temporanea
            newActivatorSection.style.backgroundColor = '#dcfce7';
            newActivatorSection.style.border = '1px solid #16a34a';
            setTimeout(() => {
                newActivatorSection.style.backgroundColor = '';
                newActivatorSection.style.border = '';
            }, 3000);

        } else {
            console.log('❌ Non trovata sezione [data-activation-status="available"] in egi-card-list');
        }

        // 🎯 AGGIORNA ANCHE IL BOTTONE NELL'EGI-CARD-LIST
        console.log('🔄 Aggiornamento bottone in egi-card-list...');
        const reserveButton = egiCard.querySelector('.reserve-button');

        if (reserveButton) {
            console.log('✅ Trovato bottone in egi-card-list, aggiornamento...');

            // Aggiorna HTML con icona "Rilancia"
            reserveButton.innerHTML = `
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                </svg>
                Rilancia
            `;

            // Aggiorna le classi CSS per il colore amber/orange
            const currentClassName = reserveButton.className;
            const newClassName = currentClassName
                .replace(/bg-gradient-to-r from-purple-500 to-purple-600/, 'bg-gradient-to-r from-amber-500 to-orange-600')
                .replace(/hover:from-purple-600 hover:to-purple-700/, 'hover:from-amber-600 hover:to-orange-700');

            reserveButton.className = newClassName;

            console.log('✅ BOTTONE EGI-CARD-LIST AGGIORNATO: "Prenota" → "Rilancia"');
        } else {
            console.log('❌ Bottone non trovato in egi-card-list');
        }
    }
}
