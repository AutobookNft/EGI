// File: resources/ts/features/reservations/reservationFeature.ts

/**
 * 📜 Oracode TypeScript Module: ReservationFeature
 * 🎯 Purpose: Integrates reservation functionality with UI elements
 * 🧱 Core Logic: Attaches event handlers to reservation buttons and manages reservation flow
 * 🛡️ GDPR: Ensures privacy in the reservation process
 *
 * @version 1.0.0
 * @date 2025-05-16
 * @author Padmin D. Curtis (for Fabio Cherici)
 */

import reservationService, {
    initReservationModal,
    getEgiReservationStatus,
    ReservationStatusResponse
} from '../../services/reservationService';
import { UEM_Client_TS_Placeholder as UEM } from '../../services/uemClientService';
import { appTranslate } from '../../config/appConfig';

// --- STATE ---
const reservationButtons = new Map<HTMLElement, number>();
let isInitialized = false;

/**
 * 📜 Oracode Function: initialize
 * 🎯 Purpose: Initialize the reservation feature by finding and attaching event handlers to reservation buttonsSee console.
 *
 * @returns {Promise<void>}
 *
 * @accessibility-trait Ensures keyboard navigation for reservation actions
 */
export async function initialize(): Promise<void> {
    console.log('🚫 ReservationFeature: DISABLED - Using server-side rendering');
    return; // EXIT EARLY - Disabilita completamente questo sistema

    if (isInitialized) return;

    try {
        // Find all reservation buttons in the document
        const buttons = document.querySelectorAll<HTMLElement>('.reserve-button');

        if (buttons.length === 0) {
            console.log('Padmin ReservationFeature: No reservation buttons found in the document');
            return;
        }

        console.log(`Padmin ReservationFeature: Found ${buttons.length} reservation buttons`);

        // Attach event handlers to each button
        buttons.forEach(button => {
            const egiId = button.dataset.egiId ? parseInt(button.dataset.egiId, 10) : null;

            if (!egiId) {
                console.warn('Padmin ReservationFeature: Reservation button missing data-egi-id attribute', button);
                return;
            }

            // Store button reference for later updates
            reservationButtons.set(button, egiId);

            // Attach click handler
            button.addEventListener('click', (e) => handleReservationButtonClick(e, egiId));

            // Make sure the button is keyboard accessible
            if (!button.hasAttribute('tabindex')) {
                button.setAttribute('tabindex', '0');
            }

            // Add keyboard handler for accessibility
            button.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    handleReservationButtonClick(e, egiId);
                }
            });
        });

        // Fetch initial reservation status for all EGIs
        await updateReservationButtonStates();

        isInitialized = true;
        console.log('Padmin ReservationFeature: Initialization complete');

    } catch (error) {
        console.error('Padmin ReservationFeature: Initialization error', error);

        if (UEM && typeof UEM.handleClientError === 'function') {
            UEM.handleClientError('RESERVATION_FEATURE_INIT_ERROR', { error }, error instanceof Error ? error : undefined);
        }
    }
}

/**
 * 📜 Oracode Function: handleReservationButtonClick
 * 🎯 Purpose: Handle click events on reservation buttons
 *
 * @param {Event} e The click event
 * @param {number} egiId The ID of the EGI to reserve
 * @returns {Promise<void>}
 *
 * @private
 */
async function handleReservationButtonClick(e: Event, egiId: number): Promise<void> {
    e.preventDefault();

    try {
        console.log(`Padmin ReservationFeature: Reservation button clicked for EGI ${egiId}`);

        // Check if the EGI is already reserved by the current user
        const statusResponse = await getEgiReservationStatus(egiId);

        if (statusResponse.success && statusResponse.data) {
            const { user_has_reservation, user_reservation } = statusResponse.data;

            // If user already has a reservation, show a message with options
            if (user_has_reservation && user_reservation) {
                const certificate = user_reservation.certificate;

                if (window.Swal) {
                    window.Swal.fire({
                        icon: 'info',
                        title: appTranslate('reservation.already_reserved.title'),
                        html: `
                            <p>${appTranslate('reservation.already_reserved.text')}</p>
                            <div class="mt-4 text-left">
                                <p><strong>${appTranslate('reservation.already_reserved.details')}</strong></p>
                                <ul class="mt-2 text-sm">
                                    <li><strong>${appTranslate('reservation.already_reserved.type')}:</strong> ${appTranslate(`reservation.type.${user_reservation.type}`)}</li>
                                    <li><strong>${appTranslate('reservation.already_reserved.amount')}:</strong> €${user_reservation.offer_amount_fiat.toFixed(2)}</li>
                                    <li><strong>${appTranslate('reservation.already_reserved.status')}:</strong> ${user_reservation.is_highest_priority
                                        ? appTranslate('reservation.priority.highest')
                                        : appTranslate('reservation.priority.superseded')}</li>
                                </ul>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: certificate
                            ? appTranslate('reservation.already_reserved.view_certificate')
                            : appTranslate('reservation.already_reserved.ok'),
                        cancelButtonText: appTranslate('reservation.already_reserved.new_reservation')
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // View certificate if available
                            if (certificate) {
                                window.location.href = certificate.url;
                            }
                        } else {
                            // Create a new reservation
                            openReservationModal(egiId);
                        }
                    });
                } else {
                    const message = appTranslate('reservation.already_reserved.text');
                    const confirmNew = confirm(`${message}\n\n${appTranslate('reservation.already_reserved.confirm_new')}`);

                    if (confirmNew) {
                        openReservationModal(egiId);
                    } else if (certificate) {
                        window.location.href = certificate.url;
                    }
                }

                return;
            }
        }

        // Open the reservation modal for a new reservation
        openReservationModal(egiId);

    } catch (error) {
        console.error('Padmin ReservationFeature: Error handling reservation button click', error);

        if (UEM && typeof UEM.handleClientError === 'function') {
            UEM.handleClientError('RESERVATION_BUTTON_CLICK_ERROR', { egiId }, error instanceof Error ? error : undefined);
        } else {
            // Fallback if UEM is not available
            alert(appTranslate('reservation.errors.button_click_error'));

            // Still try to open the modal as a fallback
            openReservationModal(egiId);
        }
    }
}

/**
 * 📜 Oracode Function: openReservationModal
 * 🎯 Purpose: Open the reservation modal for a specific EGI
 *
 * @param {number} egiId The ID of the EGI to reserve
 * @returns {void}
 */
function openReservationModal(egiId: number): void {
    const modal = initReservationModal(egiId);
    modal.open();
}

/**
 * 📜 Oracode Function: updateReservationButtonStates
 * 🎯 Purpose: Update the states of all reservation buttons based on their current reservation status
 *
 * @package FlorenceEGI
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.1.0 (FlorenceEGI - Reservation Feature TypeScript Fix)
 * @date 2025-07-30
 * @purpose Fix type safety and validation in reservation button state updates
 *
 * @returns {Promise<void>}
 */
export async function updateReservationButtonStates(): Promise<void> {
    if (reservationButtons.size === 0) return;

    const updatedEgis = new Set<number>();

    // Group buttons by EGI ID to avoid duplicate API calls
    const egiButtonMap = new Map<number, HTMLElement[]>();

    for (const [button, egiId] of reservationButtons.entries()) {
        // 🎯 FIX 1: Validate egiId before processing
        if (!egiId || isNaN(egiId) || egiId <= 0) {
            console.warn('Padmin ReservationFeature: Invalid egiId found, skipping button', { egiId, button });
            continue; // Skip to next iteration
        }

        if (!egiButtonMap.has(egiId)) {
            egiButtonMap.set(egiId, []);
        }
        egiButtonMap.get(egiId)?.push(button);
    }

    // Process each unique EGI
    for (const [egiId, buttons] of egiButtonMap.entries()) {
        // 🎯 FIX 2: Double-check egiId validity before API call
        if (!egiId || isNaN(egiId) || egiId <= 0) {
            console.warn('Padmin ReservationFeature: Invalid egiId in processing loop, skipping', { egiId });
            continue; // Skip to next iteration
        }

        try {
            const statusResponse = await getEgiReservationStatus(egiId);

            // 🎯 FIX 3: Type guard to handle both response types
            if (isReservationStatusResponse(statusResponse)) {
                if (statusResponse.success && statusResponse.data) {
                    updateButtonsForEgi(buttons, statusResponse);
                    updatedEgis.add(egiId);
                } else {
                    console.warn(`Padmin ReservationFeature: API returned unsuccessful response for EGI ${egiId}`, statusResponse);
                }
            } else {
                // Handle ServerErrorResponse
                console.error(`Padmin ReservationFeature: Server error for EGI ${egiId}`, statusResponse);

                if (UEM && typeof UEM.handleClientError === 'function') {
                    UEM.handleClientError('RESERVATION_STATUS_SERVER_ERROR', {
                        egiId,
                        error: statusResponse
                    });
                }
            }

        } catch (error) {
            console.error(`Padmin ReservationFeature: Error updating buttons for EGI ${egiId}`, error);

            if (UEM && typeof UEM.handleClientError === 'function') {
                UEM.handleClientError('RESERVATION_STATUS_FETCH_ERROR', {
                    egiId,
                    error
                }, error instanceof Error ? error : undefined);
            }
        }
    }

    console.log(`Padmin ReservationFeature: Updated buttons for ${updatedEgis.size} EGIs`);
}

function isReservationStatusResponse(response: any): response is ReservationStatusResponse {
    return response &&
           typeof response === 'object' &&
           'success' in response &&
           typeof response.success === 'boolean';
}

/**
 * 📜 Oracode Function: updateButtonsForEgi
 * 🎯 Purpose: Update the state of all buttons for a specific EGI based on its reservation status
 *
 * @param {HTMLElement[]} buttons The buttons to update
 * @param {ReservationStatusResponse} statusResponse The reservation status response
 * @returns {void}
 *
 * @private
 */
function updateButtonsForEgi(buttons: HTMLElement[], statusResponse: ReservationStatusResponse): void {
    if (!statusResponse.success || !statusResponse.data) return;

    const { is_reserved, user_has_reservation, highest_priority_reservation, user_reservation } = statusResponse.data;

    buttons.forEach(button => {
        // Reset button state first
        button.classList.remove('bg-green-600', 'bg-green-700', 'bg-yellow-600', 'bg-gray-500');
        button.classList.add('bg-green-600', 'hover:bg-green-700');

        // Remove any badge
        const existingBadge = button.querySelector('.reservation-badge');
        if (existingBadge) {
            button.removeChild(existingBadge);
        }

        // Update button text
        if (user_has_reservation) {
            // User has a reservation for this EGI
            button.innerHTML = `
                <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                </svg>
                ${appTranslate('reservation.button.reserved')}
            `;

            // Add badge for priority status
            if (user_reservation) {
                let badgeClass = user_reservation.is_highest_priority
                    ? 'bg-green-100 text-green-800 border-green-200'
                    : 'bg-yellow-100 text-yellow-800 border-yellow-200';

                let badgeText = user_reservation.is_highest_priority
                    ? appTranslate('reservation.badge.highest')
                    : appTranslate('reservation.badge.superseded');

                const badge = document.createElement('span');
                badge.className = `reservation-badge absolute -top-2 -right-2 py-1 px-2 rounded-full text-xs font-medium ${badgeClass} border`;
                badge.textContent = badgeText;
                button.appendChild(badge);

                // Make button yellow if superseded
                if (!user_reservation.is_highest_priority) {
                    button.classList.remove('bg-green-600', 'hover:bg-green-700');
                    button.classList.add('bg-yellow-600', 'hover:bg-yellow-700');
                }
            }
        } else if (is_reserved) {
            // EGI is reserved by someone else
            button.innerHTML = `
                <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.25 2A1.75 1.75 0 0 0 2.5 3.75v14.5a.75.75 0 0 0 1.218.582l5.534-4.426a.75.75 0 0 1 .496 0l5.534 4.427A.75.75 0 0 0 17.5 18.25V3.75A1.75 1.75 0 0 0 15.75 2h-11.5Z" clip-rule="evenodd" />
                </svg>
                ${appTranslate('reservation.button.make_offer')}
            `;

            // Add badge for existing reservation
            if (highest_priority_reservation) {
                const badge = document.createElement('span');
                badge.className = 'absolute px-2 py-1 text-xs font-medium text-blue-800 bg-blue-100 border border-blue-200 rounded-full reservation-badge -top-2 -right-2';
                badge.textContent = appTranslate('reservation.badge.has_offers');
                button.appendChild(badge);
            }
        } else {
            // No reservations for this EGI
            button.innerHTML = `
                <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.25 2A1.75 1.75 0 0 0 2.5 3.75v14.5a.75.75 0 0 0 1.218.582l5.534-4.426a.75.75 0 0 1 .496 0l5.534 4.427A.75.75 0 0 0 17.5 18.25V3.75A1.75 1.75 0 0 0 15.75 2h-11.5Z" clip-rule="evenodd" />
                </svg>
                ${appTranslate('reservation.button.reserve')}
            `;
        }
    });
}

export default {
    initialize,
    updateReservationButtonStates
};
