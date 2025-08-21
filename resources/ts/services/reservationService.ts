// File: resources/ts/services/reservationService.ts

/**
 * 📜 Oracode TypeScript Module: ReservationService
 * 🎯 Purpose: Handle EGI reservation operations across the frontend
 * 🧱 Core Logic: Manage reservation form, API interactions, and state management
 * 🛡️ GDPR: Ensures minimal data collection and proper handling of wallet information
 *
 * @version 1.0.0
 * @date 2025-05-16
 * @author Padmin D. Curtis (for Fabio Cherici)
 */

// ✅ IMPORT del PortfolioManager per triggering updates
// import { getPortfolioManager } from '../features/portfolio/portfolioManager'; // RIMOSSO - non serve più!

/**
 * PRE-LAUNCH RESERVATION FUNCTIONS
 *
 * ADD these functions to your existing reservationService.ts file
 * Place them BEFORE the final export default statement
 *
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @date 2025-08-15
 * @version 1.0.0 (FlorenceEGI - Pre-Launch Addon)
 */

import { UEM_Client_TS_Placeholder as UEM } from './uemClientService';
import { getAppConfig, route, appTranslate, ServerErrorResponse } from '../config/appConfig';
import { getCsrfTokenTS } from '../utils/csrf';
import { getAuthStatus } from '../features/auth/authService';
import { getAlgoExchangeRate, getCachedAlgoRate, setCachedAlgoRate } from './reservation/ExchangeRateService';
import type {
    ReservationFormData,
    ReservationResponse,
    ReservationStatusResponse,
    PreLaunchReservationResponse,
    RankingsResponse
} from '../types/reservationTypes';

// --- STATE ---
let reservationModalInstance: ReservationFormModal | null = null;

/**
 * 📜 ReservationFormModal Class
 * 🎯 Purpose: Manages the reservation modal UI and form interaction
 *
 * @accessibility-trait Manages focus trap in modal for keyboard navigation
 * @privacy-safe Handles minimal contact data with user consent
 */
class ReservationFormModal {
    private egiId: number;
    private modal: HTMLElement | null = null;
    private form: HTMLFormElement | null = null;
    private closeButton: HTMLElement | null = null;
    private offerInput: HTMLInputElement | null = null;
    private algoEquivalentText: HTMLElement | null = null;
    private submitButton: HTMLButtonElement | null = null;
    private lastFocusedElement: HTMLElement | null = null;

    /**
     * Initialize a new ReservationFormModal instance
     *
     * @param egiId The ID of the EGI being reserved
     */
    constructor(egiId: number) {
        this.egiId = egiId;
        this.initModal();
    }

    /**
     * Initialize the modal by creating the necessary DOM elements
     *
     * @private
     */
    private async initModal(): Promise<void> {
        // Create modal if it doesn't exist yet
        if (!document.getElementById('reservation-modal')) {
            const modalHtml = this.generateModalHTML();
            const modalContainer = document.createElement('div');
            modalContainer.innerHTML = modalHtml;
            document.body.appendChild(modalContainer.firstElementChild as HTMLElement);
        }

        // Cache DOM elements
        this.modal = document.getElementById('reservation-modal');
        this.form = document.getElementById('reservation-form') as HTMLFormElement;
        this.closeButton = document.getElementById('close-reservation-modal');
        this.offerInput = document.getElementById('offer_amount_fiat') as HTMLInputElement;
        this.algoEquivalentText = document.getElementById('algo-equivalent-text');
        this.submitButton = document.querySelector('#reservation-form button[type="submit"]') as HTMLButtonElement;

        // Set up event listeners
        this.setupEventListeners();

        // Fetch current ALGO rate
        await this.updateAlgoRate();
    }

    /**
     * Set up event listeners for the modal
     *
     * @private
     */
    private setupEventListeners(): void {
        // Close button click
        this.closeButton?.addEventListener('click', () => this.close());

        // Click outside to close
        this.modal?.addEventListener('click', (e: MouseEvent) => {
            if (e.target === this.modal) {
                this.close();
            }
        });

        // Escape key to close
        document.addEventListener('keydown', (e: KeyboardEvent) => {
            if (e.key === 'Escape' && this.isOpen()) {
                this.close();
            }
        });

        // Update ALGO equivalent when offer amount changes
        this.offerInput?.addEventListener('input', () => this.updateAlgoEquivalent());

        // Validate numeric input for offer amount
        this.offerInput?.addEventListener('input', (e: Event) => {
            const target = e.target as HTMLInputElement;
            let value = target.value;

            // Remove any non-numeric characters except decimal point
            value = value.replace(/[^0-9.]/g, '');

            // Ensure only one decimal point
            const parts = value.split('.');
            if (parts.length > 2) {
                value = parts[0] + '.' + parts.slice(1).join('');
            }

            // Limit to 2 decimal places
            if (parts[1] && parts[1].length > 2) {
                value = parts[0] + '.' + parts[1].substring(0, 2);
            }

            // Update the input value if it changed
            if (target.value !== value) {
                target.value = value;
            }
        });

        // Form submission
        this.form?.addEventListener('submit', (e: Event) => this.handleSubmit(e));
    }

    /**
     * Load and display EGI information in the modal
     *
     * @private
     */
    private async loadEgiInfo(): Promise<void> {
        const infoSection = document.getElementById('egi-info-section');
        if (!infoSection) return;

        try {
            // Fetch EGI modal information from our new endpoint
            // Use the route from config with proper parameter replacement
            let url;
            try {
                const config = getAppConfig();
                if (config.routes?.api?.egiModalInfo) {
                    url = config.routes.api.egiModalInfo.replace(':egiId', this.egiId.toString());
                } else {
                    throw new Error('Route not found in config');
                }
            } catch (e) {
                // Fallback to hardcoded URL if route helper fails
                url = `/api/egis/${this.egiId}/modal-info`;
            }
            console.log('Loading EGI info from URL:', url);

            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfTokenTS()
                }
            });

            console.log('Response status:', response.status);

            if (!response.ok) {
                console.error('Response not OK:', response.status, response.statusText);
                throw new Error('HTTP error: ' + response.status);
            }

            const result = await response.json();
            console.log('API result:', result);

            if (result && result.success && result.data) {
                const data = result.data;
                let egiInfoHTML = '';

                // Mostra il titolo dell'EGI se disponibile
                if (data.title) {
                    egiInfoHTML += `
                        <div class="mb-3">
                            <h3 class="text-lg font-semibold text-gray-800">${data.title}</h3>
                        </div>
                    `;
                }

                // Mostra il prezzo corrente
                if (data.has_reservations && data.current_price) {
                    egiInfoHTML += `
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-sm font-medium text-green-700">Offerta Attuale più Alta:</span>
                            <span class="text-lg font-bold text-green-800">€${parseFloat(data.current_price).toFixed(2)}</span>
                        </div>
                    `;
                } else if (data.base_price) {
                    egiInfoHTML += `
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-sm font-medium text-amber-700">Prezzo Base:</span>
                            <span class="text-lg font-bold text-amber-800">€${parseFloat(data.base_price).toFixed(2)}</span>
                        </div>
                    `;
                }

                // Mostra informazioni sull'attivatore se esiste
                if (data.activator) {
                    egiInfoHTML += `
                        <div class="border-t border-green-200 pt-3">
                            <span class="text-sm font-medium text-green-700">Attuale Attivatore:</span>
                            <div class="flex items-center gap-2 mt-1">
                    `;

                    if (data.activator.type === 'commissioner') {
                        // Mostra nome e avatar per commissioner
                        egiInfoHTML += `
                            ${data.activator.avatar ?
                                `<img src="${data.activator.avatar}" alt="Avatar" class="w-6 h-6 rounded-full">` :
                                `<div class="w-6 h-6 bg-amber-500 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                    </svg>
                                </div>`
                            }
                            <span class="text-sm text-green-800 font-medium">${data.activator.name}</span>
                        `;
                    } else {
                        // Mostra solo icona e wallet per utenti anonimi
                        egiInfoHTML += `
                            <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm text-green-800">Attivatore</span>
                                <span class="text-xs text-green-600 font-mono">${data.activator.wallet_address}</span>
                            </div>
                        `;
                    }

                    egiInfoHTML += `
                            </div>
                        </div>
                    `;
                }

                infoSection.innerHTML = egiInfoHTML;
            } else {
                // Errore nel caricamento
                infoSection.innerHTML = `
                    <div class="text-center text-amber-600">
                        <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.081 15.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <p class="text-sm">Impossibile caricare le informazioni dell'EGI</p>
                    </div>
                `;
            }
        } catch (error) {
            console.error('Error loading EGI info:', error);
            infoSection.innerHTML = `
                <div class="text-center text-red-600">
                    <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm">Errore nel caricamento delle informazioni</p>
                </div>
            `;
        }
    }

    /**
     * Open the reservation modal
     *
     * @returns {void}
     */
    public open(): void {
        if (!this.modal) return;

        // Save last focused element for accessibility
        this.lastFocusedElement = document.activeElement as HTMLElement;

        // Show modal
        this.modal.classList.remove('hidden');
        this.modal.classList.add('flex', 'items-center', 'justify-center');

        // Set focus on the offer input
        this.offerInput?.focus();

        // Prevent background scrolling
        document.body.style.overflow = 'hidden';

        // Load EGI information
        this.loadEgiInfo();
    }

    /**
     * Close the reservation modal
     *
     * @returns {void}
     */
    public close(): void {
        if (!this.modal) return;

        // Hide modal
        this.modal.classList.add('hidden');
        this.modal.classList.remove('flex', 'items-center', 'justify-center');

        // Restore focus to the element that was focused before the modal opened
        if (this.lastFocusedElement) {
            (this.lastFocusedElement as HTMLElement).focus();
        }

        // Restore background scrolling
        document.body.style.overflow = '';

        // Reset form
        this.form?.reset();
    }

    /**
     * Check if the modal is currently open
     *
     * @returns {boolean} True if the modal is open
     */
    public isOpen(): boolean {
        return this.modal ? !this.modal.classList.contains('hidden') : false;
    }

    /**
     * Handle form submission
     *
     * @param {Event} e The submit event
     * @private
     */
    private async handleSubmit(e: Event): Promise<void> {
        e.preventDefault();

        if (!this.form) return;

        try {
            // Disable submit button to prevent double submission
            if (this.submitButton) {
                this.submitButton.disabled = true;
                this.submitButton.innerHTML = '<svg class="animate-spin h-5 w-5 mr-3" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Processing...';
            }

            // Get form data
            const formData = new FormData(this.form);
            const data: ReservationFormData = {
                offer_amount_fiat: parseFloat(formData.get('offer_amount_fiat') as string),
                terms_accepted: formData.get('terms_accepted') === 'on',
                contact_data: {}
            };

            // Add contact data if present
            if (formData.get('contact_data[name]')) {
                data.contact_data!.name = formData.get('contact_data[name]') as string;
            }

            if (formData.get('contact_data[email]')) {
                data.contact_data!.email = formData.get('contact_data[email]') as string;
            }

            if (formData.get('contact_data[message]')) {
                data.contact_data!.message = formData.get('contact_data[message]') as string;
            }

            // If no contact data was provided, set to undefined
            if (Object.keys(data.contact_data!).length === 0) {
                data.contact_data = undefined;
            }

            // Submit reservation
            const response = await reserveEgi(this.egiId, data);

            // Handle response
            if (response.success) {
                // Close modal
                this.close();

                // Show success message
                // 🎯 AGGIORNA LA CARD IMMEDIATAMENTE PRIMA DI SWEETALERT!
                this.updateEgiDisplay(response);

                if (window.Swal) {
                    window.Swal.fire({
                        icon: 'success',
                        title: appTranslate('reservation.success_title'),
                        text: response.message,
                        confirmButtonText: appTranslate('reservation.view_certificate'),
                        showCancelButton: true,
                        cancelButtonText: appTranslate('reservation.close')
                    }).then((result: { isConfirmed: boolean }) => {
                        // Card già aggiornata sopra!

                        if (result.isConfirmed && response.certificate) {
                            window.location.href = response.certificate.url;
                        }
                    });
                } else {
                    // Update EGI display immediately if no SweetAlert
                    // this.updateEgiDisplay(response); // Già chiamata sopra!

                    alert(response.message);
                    if (response.certificate) {
                        window.location.href = response.certificate.url;
                    }
                }
            } else {
                // Show error using UEM
                if (UEM && typeof UEM.handleClientError === 'function') {
                    UEM.handleClientError(
                        response.error_code || 'RESERVATION_UNKNOWN_ERROR',
                        { egiId: this.egiId },
                        undefined,
                        response.message
                    );
                } else {
                    // Fallback if UEM is not available
                    alert(response.message || 'An error occurred during reservation.');
                }
            }
        } catch (error) {
            console.error('Reservation submission error:', error);

            // Show error using UEM
            if (UEM && typeof UEM.handleClientError === 'function') {
                UEM.handleClientError('RESERVATION_SUBMISSION_ERROR', { error }, error instanceof Error ? error : undefined);
            } else {
                // Fallback if UEM is not available
                alert('An error occurred during reservation submission.');
            }
        } finally {
            // Re-enable submit button
            if (this.submitButton) {
                this.submitButton.disabled = false;
                this.submitButton.innerHTML = appTranslate('reservation.form.submit_button');
            }
        }
    }

    /**
     * Update the ALGO equivalent text based on the current EUR amount
     *
     * @private
     */
    private updateAlgoEquivalent(): void {
        if (!this.offerInput || !this.algoEquivalentText) return;

        const currentAlgoRate = getCachedAlgoRate();
        if (!currentAlgoRate) return;

        const eurAmount = parseFloat(this.offerInput.value) || 0;
        const algoAmount = (eurAmount / currentAlgoRate).toFixed(8);

        this.algoEquivalentText.textContent = appTranslate('reservation.form.algo_equivalent', { amount: algoAmount });
    }

    /**
     * Fetch the current ALGO/EUR exchange rate
     *
     * @private
     */
    private async updateAlgoRate(): Promise<void> {
        try {
            // Use the exchange rate if already fetched
            const cachedRate = getCachedAlgoRate();
            if (cachedRate !== null) {
                this.updateAlgoEquivalent();
                return;
            }

            // Otherwise fetch the current rate
            const rate = await getAlgoExchangeRate();
            if (rate !== null) {
                setCachedAlgoRate(rate);
                this.updateAlgoEquivalent();
            }
        } catch (error) {
            console.error('Failed to fetch ALGO exchange rate:', error);

            // Use fallback rate
            setCachedAlgoRate(0.2); // 1 EUR = 5 ALGO (fallback)
            this.updateAlgoEquivalent();
        }
    }

    /**
     * Update EGI display after successful reservation
     * 🎯 SEMPLIFICATO: Usa il sistema di aggiornamento automatico esistente!
     *
     * @private
     * @param response The reservation response
     */
    private updateEgiDisplay(response: ReservationResponse): void {
        try {
            console.log('🎯 AGGIORNAMENTO DIRETTO CARD!');
            console.log('🔍 Cercando EGI ID:', this.egiId);

            // 🔍 DEBUG: STAMPA TUTTA LA RESPONSE
            console.log('📋 RESPONSE COMPLETA:', JSON.stringify(response, null, 2));
            console.log('📋 response.reservation:', response.reservation);
            console.log('📋 response.reservation?.offer_amount_fiat:', response.reservation?.offer_amount_fiat);

            // 🎯 TROVA TUTTI GLI ELEMENTI CON LO STESSO EGI ID!
            const allEgiElements = document.querySelectorAll(`[data-egi-id="${this.egiId}"]`);

            if (allEgiElements.length === 0) {
                console.error('❌ NESSUN ELEMENTO TROVATO per ID:', this.egiId);

                // DEBUG: mostra tutti gli elementi disponibili
                const allCards = document.querySelectorAll('.egi-card, .egi-card-list, [data-egi-id], [data-id]');
                console.log('🔍 Tutti gli elementi trovati:', Array.from(allCards).map(card => ({
                    tagName: card.tagName,
                    className: card.className,
                    dataEgiId: card.getAttribute('data-egi-id'),
                    dataId: card.getAttribute('data-id')
                })));
                return;
            }

            console.log(`✅ Trovati ${allEgiElements.length} elementi con EGI ID ${this.egiId}:`);
            Array.from(allEgiElements).forEach((element, index) => {
                console.log(`  [${index}] ${element.tagName}.${element.className}`);
            });

            // 🎯 AGGIORNA TUTTI GLI ELEMENTI CON LO STESSO EGI ID
            Array.from(allEgiElements).forEach((egiCard, cardIndex) => {
                console.log(`\n🔄 Aggiornando elemento ${cardIndex}: ${egiCard.tagName}.${egiCard.className}`); console.log('✅ Card trovata!', egiCard);
                console.log('🔍 Struttura HTML della card:', egiCard.outerHTML.substring(0, 300) + '...');

                // 🎯 GESTIONE SPECIFICA PER EGI-CARD-LIST
                const isEgiCardList = egiCard.classList.contains('egi-card-list') ||
                    egiCard.querySelector('.egi-card-list') ||
                    egiCard.closest('.egi-card-list');

                if (isEgiCardList) {
                    console.log('🎯 RILEVATO EGI-CARD-LIST - Gestione sostituzione sezione Da Attivare');
                    handleEgiCardListUpdate(egiCard, response);
                    return; // Skip normal processing per egi-card-list
                }

                // 💰 AGGIORNA PREZZO - USA DATA-PRICE-DISPLAY SPECIFICO
                if (response.data?.reservation?.offer_amount_fiat) {
                    const newPrice = parseFloat(response.data.reservation.offer_amount_fiat.toString()).toFixed(2);
                    console.log(`💰 Nuovo prezzo da applicare: €${newPrice}`);

                    // 🎯 USA IL SELETTORE DATA-PRICE-DISPLAY SPECIFICO
                    const priceElements = egiCard.querySelectorAll('[data-price-display]');
                    console.log(`� Trovati ${priceElements.length} elementi con data-price-display`);

                    let priceUpdated = false;
                    Array.from(priceElements).forEach((el, idx) => {
                        if (el instanceof HTMLElement) {
                            const oldText = el.textContent?.trim() || '';
                            console.log(`💰 AGGIORNAMENTO PREZZO [${idx}]: "${oldText}" → "€${newPrice}"`);

                            el.textContent = `€${newPrice}`;
                            priceUpdated = true;

                            // Evidenziazione visiva
                            el.style.backgroundColor = '#fef3c7';
                            el.style.fontWeight = 'bold';
                            el.style.color = '#d97706';
                            setTimeout(() => {
                                el.style.backgroundColor = '';
                                el.style.fontWeight = '';
                                el.style.color = '';
                            }, 2000);

                            console.log(`✅ PREZZO AGGIORNATO: "${oldText}" → "${el.textContent}"`);
                        }
                    });

                    if (!priceUpdated) {
                        console.log('❌ NESSUN ELEMENTO [data-price-display] TROVATO!');
                        // Fallback con metodo precedente se il data-attribute non è ancora renderizzato
                        const fallbackElements = egiCard.querySelectorAll('.currency-display');
                        console.log(`🔄 Fallback: trovati ${fallbackElements.length} elementi .currency-display`);

                        Array.from(fallbackElements).forEach((el, idx) => {
                            if (el instanceof HTMLElement && el.textContent?.includes('€')) {
                                const oldText = el.textContent.trim();
                                console.log(`💰 FALLBACK [${idx}]: "${oldText}" → "€${newPrice}"`);
                                el.textContent = `€${newPrice}`;
                                priceUpdated = true;

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
                }

                // 👤 AGGIORNA ATTIVATORE - USA DATA-ACTIVATOR-NAME SPECIFICO
                console.log('👤 Aggiornamento informazioni attivatore...');
                const activatorElements = egiCard.querySelectorAll('[data-activator-name]');
                console.log(`� Trovati ${activatorElements.length} elementi con data-activator-name`);

                let activatorUpdated = false;

                // 📋 PRENDI I DATI DELL'UTENTE DALLA RESPONSE
                const userDetails = response.data?.user;
                console.log('👤 DEBUG COMPLETO - Response structure:');
                console.log('  response.data:', response.data);
                console.log('  response.reservation:', response.reservation);
                console.log('  userDetails found:', userDetails);

                // 🎯 CALCOLA IL NOME DELL'ATTIVATORE
                let userName = 'Utente'; // Fallback generico
                let isGenericName = true;

                if (userDetails?.name) {
                    userName = `${userDetails.name}`;
                    isGenericName = false;
                } else if (userDetails?.wallet_address) {
                    userName = userDetails.wallet_address.substring(0, 12) + '...';
                    isGenericName = false;
                } else {
                    // 🔄 Fallback: prova a prendere l'utente autenticato attuale
                    const currentUser = (window as any).user || (window as any).Laravel?.user;
                    if (currentUser?.name && currentUser?.last_name) {
                        userName = `${currentUser.name} ${currentUser.last_name}`;
                        isGenericName = false;
                        console.log('👤 Usando utente autenticato:', userName);
                    }
                }

                console.log('👤 Nome attivatore finale:', userName);
                console.log('👤 È nome generico?', isGenericName);

                if (activatorElements.length > 0) {
                    // ✅ Aggiorna elementi esistenti
                    Array.from(activatorElements).forEach((el, idx) => {
                        if (el instanceof HTMLElement) {
                            const oldText = el.textContent?.trim() || '';
                            console.log(`👤 AGGIORNAMENTO ATTIVATORE [${idx}]: "${oldText}" → "${userName}"`);

                            el.textContent = userName;
                            activatorUpdated = true;

                            // Evidenziazione visiva
                            el.style.backgroundColor = '#dcfce7';
                            el.style.fontWeight = 'bold';
                            el.style.border = '1px solid #16a34a';

                            setTimeout(() => {
                                el.style.backgroundColor = '';
                                el.style.fontWeight = '';
                                el.style.border = '';
                            }, 3000);

                            console.log(`✅ ATTIVATORE AGGIORNATO: "${oldText}" → "${el.textContent}"`);
                        }
                    });
                } else {
                    // 🆕 PER EGI-CARD: AGGIUNGI SOTTOSEZIONE ATTIVATORE DENTRO IL BOX PREZZO
                    console.log('👤 RICERCA SEZIONE PREZZO per aggiungere sottosezione attivatore...');

                    // 👤 Determina se è un commissioner e avatar - DEVONO ESSERE QUI!
                    const isCommissioner = userDetails?.is_commissioner || false;
                    const avatarUrl = userDetails?.avatar || null;

                    console.log('🔍 DEBUG AVATAR (egi-card):', {
                        isCommissioner,
                        avatarUrl,
                        userDetails: userDetails
                    });

                    // Cerca il div del prezzo (quello con border-green-500/30 e bg-gradient-to-r)
                    const priceSection = egiCard.querySelector('.border-green-500\\/30');

                    if (priceSection) {
                        console.log('✅ TROVATA SEZIONE PREZZO - AGGIUNGO SOTTOSEZIONE ATTIVATORE!');

                        // Controlla se esiste già una sottosezione attivatore
                        const existingActivatorSection = priceSection.querySelector('[data-activator-section]');

                        if (existingActivatorSection) {
                            // Aggiorna la sezione esistente
                            console.log('🔄 Aggiornando sezione attivatore esistente...');

                            const activatorNameSpan = existingActivatorSection.querySelector('[data-activator-name]');
                            const activatorAvatar = existingActivatorSection.querySelector('.activator-avatar');

                            if (activatorNameSpan) {
                                activatorNameSpan.textContent = userName;
                            }

                            if (activatorAvatar && avatarUrl) {
                                activatorAvatar.outerHTML = `<img src="${avatarUrl}" alt="${userName}" class="object-cover w-4 h-4 border rounded-full border-white/20 activator-avatar">`;
                            }

                        } else {
                            // Crea nuova sottosezione attivatore
                            console.log('🆕 Creando nuova sottosezione attivatore...');

                            const activatorSubsection = document.createElement('div');
                            activatorSubsection.className = 'flex items-center gap-2 pt-2 border-t border-green-500/20';
                            activatorSubsection.setAttribute('data-activator-section', 'true');

                            // Avatar con logica corretta
                            let avatarElement = '';
                            if (avatarUrl) {
                                avatarElement = `<img src="${avatarUrl}" alt="${userName}" class="object-cover w-4 h-4 border rounded-full border-white/20 activator-avatar">`;
                            } else if (isCommissioner) {
                                avatarElement = `
                                    <div class="flex items-center justify-center flex-shrink-0 w-4 h-4 bg-green-600 rounded-full activator-avatar">
                                        <svg class="w-2 h-2 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                `;
                            } else {
                                avatarElement = `
                                    <div class="flex items-center justify-center flex-shrink-0 w-4 h-4 bg-green-600 rounded-full activator-avatar">
                                        <svg class="w-2 h-2 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                `;
                            }

                            activatorSubsection.innerHTML = `
                                ${avatarElement}
                                <span class="text-xs text-green-200 truncate">
                                    Attivatore: <span class="font-semibold" data-activator-name>${userName}</span>
                                </span>
                            `;

                            // Aggiungi la sottosezione alla fine del box prezzo
                            priceSection.appendChild(activatorSubsection);
                        }

                        console.log('✅ SOTTOSEZIONE ATTIVATORE AGGIUNTA/AGGIORNATA NEL BOX PREZZO!');
                        activatorUpdated = true;

                    } else {
                        console.log('❌ Non riesco a trovare la sezione prezzo (.border-green-500\\/30)');
                    }
                }

                if (!activatorUpdated) {
                    console.log('👤 NESSUN ELEMENTO [data-activator-name] TROVATO - probabilmente non ci sono attivatori nella card!');
                }

                console.log('🎉 CARD AGGIORNATA!');            // ✅ USA LA FUNZIONE DI REFRESH AUTOMATICO ESISTENTE
                // Simile a quella in collection-badge.blade.php che aggiorna ogni 5 secondi
                setTimeout(() => {
                    console.log('� Triggering automatic refresh of EGI data...');

                    // ✅ USA GLI EVENTI CHE IL COLLECTION-BADGE GIÀ ASCOLTA!
                    // 1. collection-changed event
                    const collectionChangedEvent = new CustomEvent('collection-changed', {
                        detail: {
                            egiId: this.egiId,
                            reason: 'reservation-completed'
                        }
                    });
                    document.dispatchEvent(collectionChangedEvent);

                    // 2. collection-updated event
                    const collectionUpdatedEvent = new CustomEvent('collection-updated', {
                        detail: {
                            egiId: this.egiId,
                            reason: 'reservation-completed'
                        }
                    });
                    document.dispatchEvent(collectionUpdatedEvent);

                    // Forza anche il refresh della pagina se necessario per aggiornare le cifre
                    if (typeof window !== 'undefined' && window.location) {
                        console.log('� Scheduling page data refresh...');
                        setTimeout(() => {
                            // NO RELOAD! Questa è una SPA, non PHP anni 90!
                        }, 2000); // Aspetta 2 secondi prima del refresh
                    }

                }, 1000); // Aspetta 1 secondo per permettere al server di processare

                console.log('✅ Eventi ESISTENTI lanciati! Il collection-badge dovrebbe reagire');

                // 🎯 Eventi già lanciati sopra per aggiornare il sistema
                console.log('✅ Aggiornamento completato via eventi DOM');
            }); // CHIUDI IL FOREACH
        } catch (error) {
            console.error('❌ Errore nell\'aggiornamento EGI:', error);
        }
    }

    /**
     * Generate the HTML for the reservation modal
     *
     * @private
     * @returns {string} The modal HTML
     */
    private generateModalHTML(): string {

        const egiId = this.egiId;

        const authStatus = getAuthStatus(getAppConfig());

        if (authStatus === 'disconnected') {
            // Mostra messaggio o apri modal wallet connect
            if (window.Swal) {
                window.Swal.fire({
                    icon: 'info',
                    title: appTranslate('reservation.unauthorized'),
                    text: appTranslate('reservation.auth_required'),
                    confirmButtonText: appTranslate('wallet_connect_button'),
                    confirmButtonColor: '#3085d6'
                }).then((result: any) => {
                    if (result.isConfirmed) {
                        // Trigger apertura modale wallet
                        document.dispatchEvent(new CustomEvent('open-wallet-modal'));
                    }
                });
            }
            return '';
        }


        return `
        <div id="reservation-modal" class="fixed inset-0 z-[100] backdrop-blur-sm bg-black/60 bg-opacity-60 hidden" role="dialog" aria-modal="true" aria-hidden="true" tabindex="-1" aria-labelledby="reservation-modal-title">
            <div class="relative bg-gradient-to-b from-white to-amber-50 rounded-xl shadow-2xl max-w-2xl w-11/12 md:w-3/4 lg:w-2/5 max-h-[90vh] overflow-y-auto border border-amber-200" role="document" style="border-image: linear-gradient(45deg, #D4A574, #2D5016) 1;">
                <button id="close-reservation-modal" class="absolute w-8 h-8 flex items-center justify-center text-2xl leading-none text-amber-700 top-4 right-4 hover:text-amber-900 hover:bg-amber-100 rounded-full transition-all duration-200" aria-label="${appTranslate('reservation.form.close_button')}">&times;</button>

                <!-- Header con stile rinascimentale -->
                <div class="bg-gradient-to-r from-amber-600 to-amber-700 text-white p-6 rounded-t-xl">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                        </div>
                        <h2 id="reservation-modal-title" class="text-xl font-bold">${appTranslate('reservation.form.title')}</h2>
                    </div>
                </div>

                <!-- Contenuto principale con padding elegante -->
                <div class="p-6 md:p-8">
                    <!-- Sezione informazioni EGI -->
                    <div id="egi-info-section" class="mb-6 p-4 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-lg">
                        <div class="animate-pulse">
                            <div class="h-4 bg-green-200 rounded w-3/4 mb-2"></div>
                            <div class="h-3 bg-green-100 rounded w-1/2"></div>
                        </div>
                    </div>

                                    <!-- Form di prenotazione -->
                    <form id="reservation-form" method="POST" action="#" class="space-y-6">
                        <input type="hidden" name="_token" value="${getCsrfTokenTS()}">

                        <div>
                            <label for="offer_amount_fiat" class="block text-sm font-medium text-gray-800 mb-2">
                                <span class="text-amber-700 font-semibold">${appTranslate('reservation.form.offer_amount_label')}</span>
                            </label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-amber-600 font-medium text-lg">€</span>
                                </div>
                                <input type="text" name="offer_amount_fiat" id="offer_amount_fiat"
                                       class="block w-full pl-12 pr-12 py-3 text-lg border-2 border-amber-300 rounded-md focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-white placeholder-gray-400 transition-all duration-200"
                                       placeholder="${appTranslate('reservation.form.offer_amount_placeholder')}"
                                       pattern="[0-9]+(\.[0-9]{1,2})?" inputmode="decimal" required>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-amber-600 text-sm font-medium">EUR</span>
                                </div>
                            </div>
                            <p class="mt-2 text-sm text-green-700 font-medium" id="algo-equivalent-text">
                                ${appTranslate('reservation.form.algo_equivalent', { amount: '0.00' })}
                            </p>
                        </div>

                        <div class="flex items-start p-4 bg-amber-50 border border-amber-200 rounded-lg">
                            <div class="flex items-center h-5">
                                <input id="terms_accepted" name="terms_accepted" type="checkbox" required
                                       class="focus:ring-amber-500 h-4 w-4 text-amber-600 border-amber-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="terms_accepted" class="font-medium text-gray-800">
                                    ${appTranslate('reservation.form.terms_accepted')}
                                </label>
                            </div>
                        </div>

                        <div class="pt-4">
                            <button type="submit"
                                    class="w-full inline-flex justify-center items-center px-6 py-3 border border-transparent rounded-lg shadow-lg text-lg font-semibold text-white bg-gradient-to-r from-amber-600 to-amber-700 hover:from-amber-700 hover:to-amber-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-all duration-200 transform hover:scale-105">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                ${appTranslate('reservation.form.submit_button')}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>`;
    }
}

/**
 * Initialize the reservation modal for a specific EGI
 *
 * @param {number} egiId The ID of the EGI to reserve
 * @returns {ReservationFormModal} The reservation modal instance
 */
export function initReservationModal(egiId: number): ReservationFormModal {
    // Create a new instance or return the existing one if for the same EGI
    if (reservationModalInstance && reservationModalInstance['egiId'] === egiId) {
        return reservationModalInstance;
    }

    reservationModalInstance = new ReservationFormModal(egiId);
    return reservationModalInstance;
}

/**
 * Reserve an EGI with the provided form data
 *
 * @param {number} egiId The ID of the EGI to reserve
 * @param {ReservationFormData} data The reservation form data
 * @returns {Promise<ReservationResponse>} The reservation response
 */
export async function reserveEgi(egiId: number, data: ReservationFormData): Promise<ReservationResponse> {
    try {
        const config = getAppConfig();

        // Use the API route for reservations with safety check
        let reserveUrl;
        if (config.routes?.api?.egisReserve) {
            reserveUrl = config.routes.api.egisReserve.replace(':egiId', egiId.toString());
        } else {
            // Fallback to hardcoded URL
            reserveUrl = `/api/egis/${egiId}/reserve`;
        }

        const response = await fetch(reserveUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfTokenTS()
            },
            body: JSON.stringify(data)
        });

        if (!response.ok && !response.headers.get('content-type')?.includes('application/json')) {
            throw new Error('HTTP error: ' + response.status + ' ' + response.statusText);
        }

        return await response.json();
    } catch (error: any) {
        console.error('Reservation API error:', error);
        return {
            success: false,
            message: (error instanceof Error) ? error.message : 'An unknown error occurred',
            error_code: 'RESERVATION_API_ERROR'
        };
    }
}

/**
 * Get the reservation status for an EGI
 *
 * @param {number} egiId The ID of the EGI to check
 * @returns {Promise<ReservationStatusResponse>} The reservation status response
 * @return {Promise<ServerErrorResponse>} The reservation status response
 */
export async function getEgiReservationStatus(egiId: number): Promise<ReservationStatusResponse | ServerErrorResponse> {

    try {
        // Use UEM.safeFetch if available, otherwise use regular fetch
        const statusUrl = route('api.egis.reservation-status', { egi: egiId })

        console.log('getEgiReservationStatus: route:', statusUrl)

        if (UEM && typeof UEM.safeFetch === 'function') {
            const response = await UEM.safeFetch(statusUrl, {
                headers: {
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error('HTTP error: ' + response.status + ' ' + response.statusText);
            }

            return await response.json();
        } else {
            const response = await fetch(statusUrl, {
                headers: {
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error('HTTP error: ' + response.status + ' ' + response.statusText);
            }

            return await response.json();
        }
    } catch (error: any) {
        console.error('Reservation status API error:', error);

        return {
            success: false,
            message: (error instanceof Error) ? error.message : 'An unknown error occurred',
            error_code: 'RESERVATION_STATUS_API_ERROR'
        };
    }
}

/**
 * Cancel a reservation
 *
 * @param {number} reservationId The ID of the reservation to cancel
 * @returns {Promise<{success: boolean, message: string}>} The cancellation response
 */
export async function cancelReservation(reservationId: number): Promise<{ success: boolean, message: string }> {
    try {
        const cancelUrl = route('api.reservations.cancel', { id: reservationId });

        const response = await fetch(cancelUrl, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfTokenTS()
            }
        });

        if (!response.ok && !response.headers.get('content-type')?.includes('application/json')) {
            throw new Error("HTTP error: " + response.status + " " + response.statusText);
        }

        return await response.json();
    } catch (error) {
        console.error('Reservation cancellation API error:', error);

        return {
            success: false,
            message: (error instanceof Error) ? error.message : 'An unknown error occurred'
        };
    }
}

// ============================================================================
// ADD THESE FUNCTIONS BEFORE THE export default STATEMENT
// ============================================================================

/**
 * Create or update a pre-launch reservation
 *
 * @param {number} egiId The EGI ID
 * @param {number} amountEur The amount in EUR
 * @returns {Promise<PreLaunchReservationResponse>} The reservation response
 */
export async function createPreLaunchReservation(
    egiId: number,
    amountEur: number
): Promise<PreLaunchReservationResponse> {
    try {
        const response = await fetch('/api/reservations/pre-launch/create', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfTokenTS()
            },
            body: JSON.stringify({
                egi_id: egiId,
                amount_eur: amountEur
            })
        });

        if (!response.ok && !response.headers.get('content-type')?.includes('application/json')) {
            throw new Error("HTTP error: " + response.status + " " + response.statusText);
        }

        const data = await response.json();

        // Handle success with UI feedback
        if (data.success && data.data) {
            showPreLaunchSuccessModal(data.data);
        }

        return data;
    } catch (error) {
        console.error('Pre-launch reservation error:', error);
        // Ensure we always throw a proper Error object
        if (error instanceof Error) {
            throw error;
        } else {
            throw new Error(typeof error === 'string' ? error : 'An unknown error occurred during reservation');
        }
    }
}

/**
 * Get current rankings for an EGI
 *
 * @param {number} egiId The EGI ID
 * @returns {Promise<RankingsResponse>} The rankings response
 */
export async function getPreLaunchRankings(egiId: number): Promise<RankingsResponse> {
    try {
        const response = await fetch(`/ api / reservations / pre - launch / rankings / ${egiId} `, {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error("HTTP error: " + response.status + " " + response.statusText);
        }

        return await response.json();
    } catch (error) {
        console.error('Error fetching rankings:', error);
        return {
            success: false,
            data: undefined
        };
    }
}

/**
 * Withdraw a pre-launch reservation
 *
 * @param {number} reservationId The reservation ID
 * @returns {Promise<{success: boolean, message: string}>} The withdrawal response
 */
export async function withdrawPreLaunchReservation(
    reservationId: number
): Promise<{ success: boolean, message: string }> {
    try {
        const response = await fetch(`/ api / reservations / pre - launch / ${reservationId}/withdraw`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfTokenTS()
            }
        });

        if (!response.ok && !response.headers.get('content-type')?.includes('application/json')) {
            throw new Error("HTTP error: " + response.status + " " + response.statusText);
        }

        return await response.json();
    } catch (error) {
        console.error('Withdrawal error:', error);
        return {
            success: false,
            message: 'Failed to withdraw reservation'
        };
    }
}

/**
 * Show success modal after pre-launch reservation
 *
 * @param {any} data The reservation data
 */
function showPreLaunchSuccessModal(data: any): void {
    const isHighest = data.is_highest;
    const position = data.rank_position;

    let title = '';
    let message = '';
    let icon = '';

    if (isHighest) {
        title = '🎉 Sei il Primo!';
        message = `Complimenti! La tua offerta di €${data.amount_eur} è la più alta!`;
        icon = '🏆';
    } else {
        title = '✅ Prenotazione Registrata';
        message = `La tua offerta di €${data.amount_eur} ti posiziona al #${position} posto`;
        icon = '📊';
    }

    // Create and show modal
    const modalHtml = `
        <div class="fixed inset-0 z-50 overflow-y-auto" id="success-modal">
            <div class="fixed inset-0 bg-black opacity-50"></div>
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="relative bg-white rounded-lg max-w-md w-full p-6">
                    <div class="text-center">
                        <div class="text-6xl mb-4">${icon}</div>
                        <h3 class="text-2xl font-bold mb-2">${title}</h3>
                        <p class="text-gray-600 mb-6">${message}</p>
                        <button onclick="document.getElementById('success-modal').remove(); location.reload();"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                            Chiudi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);
}

/**
 * Handle update for egi-card-list component
 * Sostituisce la sezione "Da Attivare" con avatar+attivatore
 */
function handleEgiCardListUpdate(egiCard: Element, response: ReservationResponse): void {
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
        } else if (userDetails?.wallet_address) {
            userName = userDetails.wallet_address.substring(0, 12) + '...';
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

        console.log('🔍 DEBUG AVATAR (egi-card-list):', {
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
}

// Export the main service functions and types
export default {
    initReservationModal,
    reserveEgi,
    getEgiReservationStatus,
    getAlgoExchangeRate,
    cancelReservation,
    // ADD THESE NEW FUNCTIONS:
    createPreLaunchReservation,
    getPreLaunchRankings,
    withdrawPreLaunchReservation
};

