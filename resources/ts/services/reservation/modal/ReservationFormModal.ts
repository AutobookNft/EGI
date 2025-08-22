/**
 * 
 * Extracted from reservationService.ts as part of SOLID refactoring
 * Handles modal UI, form interaction, and accessibility features
 * 
 * @author Fabio Cherici
 * @extracted 2025-01-22 - Phase 12 SOLID Migration
 */

import { getAppConfig, route, appTranslate, ServerErrorResponse } from '../../../config/appConfig';
import { getCsrfTokenTS } from '../../../utils/csrf';
import { getAuthStatus } from '../../../features/auth/authService';
import { getAlgoExchangeRate, getCachedAlgoRate, setCachedAlgoRate } from '../ExchangeRateService';
import { ReservationApiClient } from '../api/ReservationApiClient';
import type { ReservationFormData } from '../../../types/reservationTypes';

/**
 * 📜 ReservationFormModal Class
 * 🎯 Purpose: Manages the reservation modal UI and form interaction
 *
 * @accessibility-trait Manages focus trap in modal for keyboard navigation
 * @privacy-safe Handles minimal contact data with user consent
 */
export class ReservationFormModal {
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
            document.body.insertAdjacentHTML('beforeend', modalHtml);
        }

        // Get references to modal elements
        this.modal = document.getElementById('reservation-modal');
        this.form = document.getElementById('reservation-form') as HTMLFormElement;
        this.closeButton = document.getElementById('close-reservation-modal');
        this.offerInput = document.getElementById('offer') as HTMLInputElement;
        this.algoEquivalentText = document.getElementById('algo-equivalent');
        this.submitButton = document.getElementById('submit-reservation') as HTMLButtonElement;

        // Set up event listeners
        this.setupEventListeners();

        // Update algo equivalent display on load
        await this.updateAlgoEquivalent();
    }

    /**
     * Set up all modal event listeners
     *
     * @private
     */
    private setupEventListeners(): void {
        if (!this.modal || !this.form || !this.closeButton || !this.offerInput) return;

        // Close modal events
        this.closeButton.addEventListener('click', () => this.close());

        // Close modal on outside click
        this.modal.addEventListener('click', (e) => {
            if (e.target === this.modal) {
                this.close();
            }
        });

        // Close modal on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.modal?.style.display === 'flex') {
                this.close();
            }
        });

        // Offer input change listener
        this.offerInput.addEventListener('input', () => this.updateAlgoEquivalent());

        // Form submission
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));

        // Focus trap for accessibility
        this.setupFocusTrap();
    }

    /**
     * Set up focus trap for accessibility
     *
     * @private
     */
    private setupFocusTrap(): void {
        if (!this.modal) return;

        this.modal.addEventListener('keydown', (e) => {
            if (e.key === 'Tab') {
                const focusableElements = this.modal!.querySelectorAll(
                    'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
                );
                const firstElement = focusableElements[0] as HTMLElement;
                const lastElement = focusableElements[focusableElements.length - 1] as HTMLElement;

                if (e.shiftKey) {
                    if (document.activeElement === firstElement) {
                        lastElement.focus();
                        e.preventDefault();
                    }
                } else {
                    if (document.activeElement === lastElement) {
                        firstElement.focus();
                        e.preventDefault();
                    }
                }
            }
        });
    }

    /**
     * Show the modal
     */
    public show(): void {
        if (!this.modal) return;

        // Store the currently focused element
        this.lastFocusedElement = document.activeElement as HTMLElement;

        this.modal.style.display = 'flex';
        
        // Focus the first input
        setTimeout(() => {
            this.offerInput?.focus();
        }, 100);

        // Prevent body scroll
        document.body.style.overflow = 'hidden';
    }

    /**
     * Close the modal
     */
    public close(): void {
        if (!this.modal) return;

        this.modal.style.display = 'none';
        
        // Restore body scroll
        document.body.style.overflow = 'auto';

        // Restore focus to the last focused element
        if (this.lastFocusedElement) {
            this.lastFocusedElement.focus();
        }
    }

    /**
     * Update the Algorand equivalent display
     *
     * @private
     */
    private async updateAlgoEquivalent(): Promise<void> {
        if (!this.offerInput || !this.algoEquivalentText) return;

        const eurAmount = parseFloat(this.offerInput.value);
        
        if (isNaN(eurAmount) || eurAmount <= 0) {
            this.algoEquivalentText.textContent = '0 ALGO';
            return;
        }

        try {
            // Try to get cached rate first
            let rate = getCachedAlgoRate();
            
            if (!rate) {
                // If no cached rate, fetch new one
                rate = await getAlgoExchangeRate();
            }

            if (rate) {
                const algoAmount = eurAmount / rate;
                this.algoEquivalentText.textContent = `≈ ${algoAmount.toFixed(2)} ALGO`;
            } else {
                this.algoEquivalentText.textContent = 'Rate unavailable';
            }
        } catch (error) {
            console.error('Error updating ALGO equivalent:', error);
            this.algoEquivalentText.textContent = 'Rate unavailable';
        }
    }

    /**
     * Handle form submission
     *
     * @param e The form submit event
     * @private
     */
    private async handleSubmit(e: Event): Promise<void> {
        e.preventDefault();
        
        if (!this.form || !this.submitButton) return;

        // Disable submit button to prevent double submission
        this.submitButton.disabled = true;
        this.submitButton.textContent = appTranslate('reservation.form.submitting');

        try {
            const formData = new FormData(this.form);
            const data: ReservationFormData = {
                offer_amount_fiat: parseFloat(formData.get('offer_amount_fiat') as string),
                terms_accepted: formData.get('terms_accepted') === 'on',
                contact_data: {}
            };

            // Add contact data if present (fields are currently commented out)
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

            // Validate required fields
            if (!data.offer_amount_fiat || data.offer_amount_fiat <= 0) {
                throw new Error(appTranslate('reservation.form.error.invalid_offer'));
            }

            if (!data.terms_accepted) {
                throw new Error(appTranslate('reservation.form.error.privacy_required'));
            }

            // Submit the reservation
            const apiClient = new ReservationApiClient();
            const response = await apiClient.createReservation(this.egiId, data);

            if (response.success) {
                // Show success message
                alert(appTranslate('reservation.form.success'));
                this.close();
                
                // Refresh the page to update the reservation status
                window.location.reload();
            } else {
                throw new Error(response.message || appTranslate('reservation.form.error.generic'));
            }

        } catch (error: any) {
            console.error('Reservation submission error:', error);
            alert(error.message || appTranslate('reservation.form.error.generic'));
        } finally {
            // Re-enable submit button
            this.submitButton.disabled = false;
            this.submitButton.textContent = appTranslate('reservation.form.submit_button');
        }
    }

    /**
     * Generate the modal HTML structure
     *
     * @private
     * @returns The modal HTML string
     */
    private generateModalHTML(): string {
        return `
        <div id="reservation-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-gray-900">
                            ${appTranslate('reservation.form.title')}
                        </h2>
                        <button id="close-reservation-modal" 
                                class="text-gray-400 hover:text-gray-600 transition-colors" 
                                aria-label="${appTranslate('modal.close')}">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form id="reservation-form" class="space-y-4">
                        <div>
                            <label for="offer" class="block text-sm font-medium text-gray-700 mb-1">
                                ${appTranslate('reservation.form.offer_label')} (EUR)
                            </label>
                            <input type="number" 
                                   id="offer" 
                                   name="offer" 
                                   step="0.01" 
                                   min="1" 
                                   required 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="${appTranslate('reservation.form.offer_placeholder')}">
                            <p class="text-sm text-gray-500 mt-1">
                                <span id="algo-equivalent">0 ALGO</span>
                            </p>
                        </div>

                        <div>
                            <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-1">
                                ${appTranslate('reservation.form.email_label')}
                            </label>
                            <input type="email" 
                                   id="contact_email" 
                                   name="contact_email" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="${appTranslate('reservation.form.email_placeholder')}">
                        </div>

                        <div>
                            <label for="contact_telegram" class="block text-sm font-medium text-gray-700 mb-1">
                                ${appTranslate('reservation.form.telegram_label')}
                            </label>
                            <input type="text" 
                                   id="contact_telegram" 
                                   name="contact_telegram" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="${appTranslate('reservation.form.telegram_placeholder')}">
                        </div>

                        <div class="text-xs text-gray-600 bg-gray-50 p-3 rounded">
                            ${appTranslate('reservation.form.contact_note')}
                        </div>

                        <div class="flex items-start">
                            <input type="checkbox" 
                                   id="privacy_accepted" 
                                   name="privacy_accepted" 
                                   required 
                                   class="mt-1 mr-2">
                            <label for="privacy_accepted" class="text-sm text-gray-600">
                                ${appTranslate('reservation.form.privacy_label')}
                            </label>
                        </div>

                        <div class="flex gap-3 pt-4">
                            <button type="button" 
                                    id="close-reservation-modal-btn" 
                                    class="flex-1 px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                                ${appTranslate('modal.cancel')}
                            </button>
                            <button type="submit" 
                                    id="submit-reservation" 
                                    class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                ${appTranslate('reservation.form.submit_button')}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>`;
    }
}
