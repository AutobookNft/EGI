/**
 * 📜 EgiDisplayUpdater Class
 * 🎯 Purpose: Handles real-time structural UI updates for EGI cards and displays
 *
 * Extracted from ReservationFormModal.updateEgiDisplay() for reusability
 * Used both locally and via real-time broadcasting events
 *
 * @author GitHub Copilot for Fabio Cherici
 * @version 1.0.0
 * @date 2025-08-23
 */

import { ReservationModalUI } from './ReservationModalUI';
import type { ReservationResponse } from '../../../types/reservationTypes';

export interface StructureChanges {
    is_first_reservation: boolean;
    reservation_count: number;
    activator: {
        name: string;
        avatar?: string;
        is_commissioner: boolean;
        wallet?: string;
    };
    button_state: 'prenota' | 'rilancia';
}

export interface PriceUpdateData {
    amount: string;
    currency: string;
    structure_changes?: StructureChanges;
}

/**
 * EgiDisplayUpdater - Handles real-time UI updates for EGI cards
 */
export class EgiDisplayUpdater {

    /**
     * Update a single card from reservation response
     *
     * @private

    /**
     * Update only the price of an element
     */
    private static updatePriceOnly(element: HTMLElement, amount: string, currency: string): void {
        const amountEl = element.querySelector('.amount');
        const currEl = element.querySelector('.currency');

        if (amountEl) amountEl.textContent = amount;
        if (currEl) currEl.textContent = currency;
    }

    /**
     * Update all EGI displays for a specific EGI ID
     *
     * @param egiId The EGI ID to update
     * @param response The reservation response data
     */
    public static updateFromReservationResponse(egiId: number, response: ReservationResponse): void {
        try {
            console.log('🎯 AGGIORNAMENTO DIRETTO CARD!');
            console.log('🔍 Cercando EGI ID:', egiId);

            // 🎯 TROVA TUTTI GLI ELEMENTI CON LO STESSO EGI ID!
            const allEgiElements = document.querySelectorAll(`[data-egi-id="${egiId}"]`);

            if (allEgiElements.length === 0) {
                console.error('❌ NESSUN ELEMENTO TROVATO per ID:', egiId);
                return;
            }

            console.log(`✅ Trovati ${allEgiElements.length} elementi con EGI ID ${egiId}`);

            // 🎯 AGGIORNA TUTTI GLI ELEMENTI CON LO STESSO EGI ID
            Array.from(allEgiElements).forEach((element) => {
                // 🎯 SKIP i bottoni - processiamo solo le card vere
                if (element.tagName === 'BUTTON') {
                    console.log('⏭️ SKIP: È un bottone, non una card');
                    return;
                }

                const egiCard = element as HTMLElement;
                console.log('✅ Card trovata!', egiCard);

                // 🎯 GESTIONE SPECIFICA PER EGI-CARD-LIST
                const isEgiCardList = egiCard.classList.contains('egi-card-list') ||
                    egiCard.querySelector('.egi-card-list') ||
                    egiCard.closest('.egi-card-list');

                if (isEgiCardList) {
                    console.log('🎯 RILEVATO EGI-CARD-LIST - Gestione sostituzione sezione Da Attivare');
                    const modalUI = new ReservationModalUI();
                    modalUI.handleEgiCardListUpdate(egiCard, response);
                    return;
                }

                // Aggiorna la card normale
                this.updateSingleCard(egiCard, response);
            });
        } catch (error) {
            console.error('❌ Errore nell\'aggiornamento EGI:', error);
        }
    }

    /**
     * Update from real-time broadcast data
     *
     * @param egiId The EGI ID to update
     * @param data The price update data with structure changes
     */
    public static updateFromBroadcast(egiId: number, data: PriceUpdateData): void {
        try {
            console.log('📡 AGGIORNAMENTO VIA BROADCAST!');
            console.log('🔍 EGI ID:', egiId);
            console.log('📋 Dati broadcast:', data);

            // Trova tutti gli elementi con questo EGI ID
            const allEgiElements = document.querySelectorAll(`[data-egi-id="${egiId}"]`);

            if (allEgiElements.length === 0) {
                console.warn('❌ NESSUN ELEMENTO TROVATO per broadcast ID:', egiId);
                return;
            }

            console.log(`✅ Trovati ${allEgiElements.length} elementi per broadcast`);

            Array.from(allEgiElements).forEach((element) => {
                if (element.tagName === 'BUTTON') return;

                const egiCard = element as HTMLElement;

                // Aggiorna prezzo
                this.updatePrice(egiCard, data.amount, data.currency);

                // Aggiorna struttura se necessario
                if (data.structure_changes) {
                    this.updateStructure(egiCard, data.structure_changes);
                }
            });
        } catch (error) {
            console.error('❌ Errore nell\'aggiornamento broadcast:', error);
        }
    }

    /**
     * Update a single card from reservation response
     *
     * @private
     */
    private static updateSingleCard(egiCard: HTMLElement, response: ReservationResponse): void {
        // 💰 AGGIORNA PREZZO
        if (response.data?.reservation?.offer_amount_fiat) {
            const newPrice = parseFloat(response.data.reservation.offer_amount_fiat.toString()).toFixed(2);
            this.updatePrice(egiCard, newPrice, 'EUR');
        }

        // 👤 AGGIORNA ATTIVATORE
        this.updateActivator(egiCard, response);

        // 🔄 AGGIORNA BOTTONE
        this.updateButton(egiCard);

        // 📊 AGGIORNA/AGGIUNGI CONTEGGIO PRENOTAZIONI
        this.updateReservationCount(egiCard);
    }

    /**
     * Update price display
     *
     * @private
     */
    private static updatePrice(egiCard: HTMLElement, amount: string, currency: string): void {
        console.log(`💰 Aggiornamento prezzo: €${amount}`);

        // Usa selettore data-price-display specifico
        const priceElements = egiCard.querySelectorAll('[data-price-display]');

        let priceUpdated = false;
        Array.from(priceElements).forEach((el) => {
            if (el instanceof HTMLElement) {
                el.textContent = `€${amount}`;
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

        // Fallback per elementi senza data-price-display
        if (!priceUpdated) {
            const fallbackElements = egiCard.querySelectorAll('.currency-display');
            Array.from(fallbackElements).forEach((el) => {
                if (el instanceof HTMLElement && el.textContent?.includes('€')) {
                    el.textContent = `€${amount}`;

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

    /**
     * Update activator information
     *
     * @private
     */
    private static updateActivator(egiCard: HTMLElement, response: ReservationResponse): void {
        console.log('👤 Aggiornamento informazioni attivatore...');

        const userDetails = response.data?.user;

        // Calcola il nome dell'attivatore
        let userName = 'Utente';
        if (userDetails?.name) {
            userName = userDetails.name;
        } else if (userDetails?.wallet) {
            userName = userDetails.wallet.substring(0, 12) + '...';
        } else {
            // Fallback: prova utente autenticato
            const currentUser = (window as any).user || (window as any).Laravel?.user;
            if (currentUser?.name && currentUser?.last_name) {
                userName = `${currentUser.name} ${currentUser.last_name}`;
            }
        }

        // Aggiorna elementi esistenti
        const activatorElements = egiCard.querySelectorAll('[data-activator-name]');
        let activatorUpdated = false;

        Array.from(activatorElements).forEach((el) => {
            if (el instanceof HTMLElement) {
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
            }
        });

        // Se non ci sono elementi attivatore, crea sezione
        if (!activatorUpdated) {
            this.createActivatorSection(egiCard, userName, userDetails);
        }
    }

    /**
     * Create activator section in price box
     *
     * @private
     */
    private static createActivatorSection(egiCard: HTMLElement, userName: string, userDetails: any): void {
        const priceSection = egiCard.querySelector('.border-green-500\\/30');

        if (priceSection) {
            const existingActivatorSection = priceSection.querySelector('[data-activator-section]');

            if (existingActivatorSection) {
                // Aggiorna sezione esistente
                const activatorNameSpan = existingActivatorSection.querySelector('[data-activator-name]');
                if (activatorNameSpan) {
                    activatorNameSpan.textContent = userName;
                }
            } else {
                // Crea nuova sottosezione attivatore
                const activatorSubsection = document.createElement('div');
                activatorSubsection.className = 'flex items-center gap-2 pt-2 border-t border-green-500/20';
                activatorSubsection.setAttribute('data-activator-section', 'true');

                const isCommissioner = userDetails?.is_commissioner || false;
                const avatarUrl = userDetails?.avatar || null;

                // Avatar - ora usiamo sempre l'avatar dal backend
                let avatarElement = '';
                if (avatarUrl) {
                    avatarElement = `<img src="${avatarUrl}" alt="${userName}" class="object-cover w-4 h-4 border rounded-full border-white/20 activator-avatar">`;
                } else {
                    // Fallback solo se non c'è avatar dal backend (caso molto raro)
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

                priceSection.appendChild(activatorSubsection);
            }
        }
    }

    /**
     * Update button from "Prenota" to "Rilancia"
     *
     * @private
     */
    private static updateButton(egiCard: HTMLElement): void {
        console.log('🔄 Aggiornamento bottone prenotazione...');

        let reserveButton = egiCard.querySelector('.reserve-button') as HTMLElement;

        // Fallback: cerca per testo
        if (!reserveButton) {
            const allButtons = egiCard.querySelectorAll('button');
            reserveButton = Array.from(allButtons).find(btn =>
                btn.textContent?.includes('Prenota') ||
                btn.textContent?.includes('Reserve') ||
                btn.innerHTML.includes('Prenota')
            ) as HTMLElement;
        }

        if (reserveButton) {
            // Aggiorna HTML del bottone per "Rilancia"
            reserveButton.innerHTML = `
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                </svg>
                Rilancia
            `;

            // Cambia colori (da purple a amber/orange)
            reserveButton.className = reserveButton.className
                .replace(/bg-gradient-to-r from-purple-500 to-purple-600/, 'bg-gradient-to-r from-amber-500 to-orange-600')
                .replace(/hover:from-purple-600 hover:to-purple-700/, 'hover:from-amber-600 hover:to-orange-700');

            console.log('✅ BOTTONE AGGIORNATO: "Prenota" → "Rilancia"');
        }
    }

    /**
     * Update or add reservation count section
     *
     * @private
     */
    private static updateReservationCount(egiCard: HTMLElement): void {
        console.log('📊 Aggiornamento conteggio prenotazioni...');

        const existingSection = egiCard.querySelector('[data-reservation-count] .text-gray-300');

        if (existingSection && existingSection instanceof HTMLElement) {
            // Aggiorna conteggio esistente
            const currentCount = existingSection.textContent?.match(/(\d+)/)?.[1] || '0';
            const newCount = parseInt(currentCount) + 1;
            existingSection.textContent = `${newCount} ${newCount === 1 ? 'Prenotazione' : 'Prenotazioni'}`;

            // Evidenziazione visiva
            existingSection.style.backgroundColor = '#dcfce7';
            existingSection.style.fontWeight = 'bold';
            setTimeout(() => {
                existingSection.style.backgroundColor = '';
                existingSection.style.fontWeight = '';
            }, 2000);
        } else {
            // Crea nuova sezione prenotazioni
            this.createReservationSection(egiCard);
        }
    }

    /**
     * Create new reservation count section
     *
     * @private
     */
    private static createReservationSection(egiCard: HTMLElement): void {
        // Trova dove inserire la sezione
        const collectionInfo = egiCard.querySelector('[data-collection-info]') ||
            egiCard.querySelector('.flex.items-center.gap-2:has(.text-purple-500)');

        const insertAfter = collectionInfo ||
            egiCard.querySelector('[data-creator-info]') ||
            egiCard.querySelector('.flex.items-center.gap-2:has(.text-blue-500)');

        if (insertAfter && !egiCard.querySelector('[data-reservation-count]')) {
            const reservationSection = document.createElement('div');
            reservationSection.className = 'flex items-center gap-2 p-2 mb-2 border rounded-lg border-gray-700/50 bg-gray-800/50';
            reservationSection.setAttribute('data-reservation-count', 'true');

            reservationSection.innerHTML = `
                <div class="flex items-center justify-center flex-shrink-0 w-5 h-5 rounded-full bg-gradient-to-r from-green-500 to-emerald-500">
                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 1 1 0 000 2H6a2 2 0 00-2 2v6a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-1a1 1 0 100-2 2 2 0 012 2v8a2 2 0 01-2 2H6a2 2 0 01-2-2V5z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <span class="text-xs font-medium text-gray-300">
                        1 Prenotazione
                    </span>
                </div>
            `;

            insertAfter.parentNode?.insertBefore(reservationSection, insertAfter.nextSibling);

            // Evidenziazione visiva
            reservationSection.style.backgroundColor = '#dcfce7';
            reservationSection.style.borderColor = '#16a34a';
            setTimeout(() => {
                reservationSection.style.backgroundColor = '';
                reservationSection.style.borderColor = '';
            }, 3000);
        }
    }

    /**
     * Update structure from broadcast data
     *
     * @private
     */
    private static updateStructure(egiCard: HTMLElement, changes: StructureChanges): void {
        console.log('🔧 Aggiornamento struttura via broadcast:', changes);

        // Aggiorna attivatore se necessario
        if (changes.activator) {
            this.updateActivatorFromBroadcast(egiCard, changes.activator);
        }

        // Aggiorna bottone se necessario
        if (changes.button_state === 'rilancia') {
            this.updateButton(egiCard);
        }

        // Aggiorna conteggio prenotazioni
        this.updateReservationCountFromBroadcast(egiCard, changes.reservation_count);
    }

    /**
     * Update activator from broadcast data
     *
     * @private
     */
    private static updateActivatorFromBroadcast(egiCard: HTMLElement, activator: StructureChanges['activator']): void {
        const activatorElements = egiCard.querySelectorAll('[data-activator-name]');

        if (activatorElements.length > 0) {
            // Aggiorna elementi esistenti
            Array.from(activatorElements).forEach((el) => {
                if (el instanceof HTMLElement) {
                    el.textContent = activator.name;

                    // Evidenziazione visiva
                    el.style.backgroundColor = '#dcfce7';
                    el.style.fontWeight = 'bold';
                    setTimeout(() => {
                        el.style.backgroundColor = '';
                        el.style.fontWeight = '';
                    }, 2000);
                }
            });
        } else {
            // Crea sezione attivatore
            this.createActivatorSection(egiCard, activator.name, activator);
        }
    }

    /**
     * Update reservation count from broadcast data
     *
     * @private
     */
    private static updateReservationCountFromBroadcast(egiCard: HTMLElement, count: number): void {
        const existingSection = egiCard.querySelector('[data-reservation-count] .text-gray-300');

        if (existingSection && existingSection instanceof HTMLElement) {
            existingSection.textContent = `${count} ${count === 1 ? 'Prenotazione' : 'Prenotazioni'}`;

            // Evidenziazione visiva
            existingSection.style.backgroundColor = '#dcfce7';
            existingSection.style.fontWeight = 'bold';
            setTimeout(() => {
                existingSection.style.backgroundColor = '';
                existingSection.style.fontWeight = '';
            }, 2000);
        } else if (count > 0) {
            // Crea sezione se non esiste e count > 0
            this.createReservationSection(egiCard);
        }
    }
}
