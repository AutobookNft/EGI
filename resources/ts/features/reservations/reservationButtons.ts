// File: resources/ts/features/reservations/reservationButtons.ts

/**
 * 📜 Oracode TypeScript Module: ReservationButtons
 * 🎯 Purpose: Initialize and manage reservation buttons on EGI display pages
 * 🧱 Core Logic: Attaches event handlers to reservation buttons and updates their state
 *
 * @version 1.0.0
 * @date 2025-05-16
 * @author Padmin D. Curtis (for Fabio Cherici)
 */

import reservationService, {
    initReservationModal,
    getEgiReservationStatus
} from '../../services/reservationService';
import { UEM_Client_TS_Placeholder as UEM } from '../../services/uemClientService';
import { appTranslate } from '../../config/appConfig';

// --- State ---
const reservationButtons = new Map<HTMLElement, number>();
let isInitialized = false;

/**
 * 📜 Oracode Function: initialize
 * 🎯 Purpose: Find and initialize all reservation buttons on the page
 *
 * @export
 * @returns {Promise<void>}
 */
export async function initialize(): Promise<void> {
    console.log('🎯 ReservationButtons: CLICK-ONLY MODE - Visual styling managed by server');

    if (isInitialized) return;

    try {
        // Find all reservation buttons
        const buttons = document.querySelectorAll<HTMLElement>('.reserve-button');

        if (buttons.length === 0) {
            console.log('Padmin ReservationButtons: No reservation buttons found on the page');
            return;
        }

        console.log(`Padmin ReservationButtons: Found ${buttons.length} reservation buttons`);

        // Attach ONLY click handlers - no visual updates
        buttons.forEach(button => {
            const egiId = button.dataset.egiId ? parseInt(button.dataset.egiId, 10) : null;

            if (!egiId) {
                console.warn('Padmin ReservationButtons: Button missing data-egi-id attribute', button);
                return;
            }

            // Store button for later updates
            reservationButtons.set(button, egiId);

            // Attach click handler
            button.addEventListener('click', (e) => handleButtonClick(e, egiId));

            // Ensure the button is keyboard accessible
            if (!button.hasAttribute('tabindex')) {
                button.setAttribute('tabindex', '0');
            }

            // Add keyboard handler for accessibility
            button.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    handleButtonClick(e, egiId);
                }
            });
        });

        // DO NOT update button states - server handles this
        console.log('🚫 Skipping button state updates - managed by server');

        isInitialized = true;
        console.log('Padmin ReservationButtons: Click-only initialization complete');

    } catch (error) {
        console.error('Padmin ReservationButtons: Initialization error', error);

        if (UEM && typeof UEM.handleClientError === 'function') {
            UEM.handleClientError('RESERVATION_BUTTONS_INIT_ERROR', { error }, error instanceof Error ? error : undefined);
        }
    }
}

/**
 * 📜 Oracode Function: handleButtonClick
 * 🎯 Purpose: Handle click events on reservation buttons
 *
 * @param {Event} e The click event
 * @param {number} egiId The ID of the EGI to reserve
 * @returns {Promise<void>}
 *
 * @private
 */
async function handleButtonClick(e: Event, egiId: number): Promise<void> {
    e.preventDefault();

    try {
        console.log(`Padmin ReservationButtons: Button clicked for EGI ${egiId}`);

        // Check if user already has a reservation for this EGI
        const status = await getEgiReservationStatus(egiId);

        if (status.error_code) {
            console.error(`Padmin ReservationButtons: Error fetching reservation status for EGI ${egiId}`, status.error_code);

            if (UEM && typeof UEM.handleServerErrorResponse === 'function') {
                UEM.handleServerErrorResponse(status);
                return;
            }

            // Still try to open the modal as fallback
            openReservationModal(egiId);
            return;
        }

        if (status.success && status.data && status.data.user_has_reservation) {
            // User already has a reservation
            const reservation = status.data.user_reservation;

            if (window.Swal) {
                window.Swal.fire({
                    icon: 'info',
                    title: appTranslate('reservation.already_reserved.title'),
                    html: `
                        <p>${appTranslate('reservation.already_reserved.text')}</p>
                        <div class="mt-4 text-left">
                            <p><strong>${appTranslate('reservation.already_reserved.details')}</strong></p>
                            <ul class="mt-2 text-sm">
                                <li><strong>${appTranslate('reservation.already_reserved.type')}:</strong> ${appTranslate(`reservation.type.${reservation?.type}`)}</li>
                                <li><strong>${appTranslate('reservation.already_reserved.amount')}:</strong> €${(+(reservation?.offer_amount_eur ?? 0)).toFixed(2)}</li>
                                <li><strong>${appTranslate('reservation.already_reserved.status')}:</strong> ${reservation?.is_highest_priority
                                    ? appTranslate('reservation.priority.highest')
                                    : appTranslate('reservation.priority.superseded')}</li>
                            </ul>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: reservation?.certificate
                        ? appTranslate('reservation.already_reserved.view_certificate')
                        : appTranslate('reservation.already_reserved.ok'),
                    cancelButtonText: appTranslate('reservation.already_reserved.new_reservation')
                }).then((result: { isConfirmed: boolean }) => {
                    if (result.isConfirmed) {
                        // View certificate if available
                        if (reservation?.certificate) {
                            window.location.href = reservation.certificate.url;
                        }
                    } else {
                        // Make a new reservation
                        openReservationModal(egiId);
                    }
                });

                return;
            }
        }

        // Open reservation modal
        openReservationModal(egiId);

    } catch (error) {
        console.error('Padmin ReservationButtons: Error handling button click', error);

        if (UEM && typeof UEM.handleClientError === 'function') {
            UEM.handleClientError('RESERVATION_BUTTON_CLICK_ERROR', { egiId }, error instanceof Error ? error : undefined);
        }

        // Still try to open the modal as fallback
        openReservationModal(egiId);
    }
}

/**
 * 📜 Oracode Function: openReservationModal
 * 🎯 Purpose: Open the reservation modal for a specific EGI
 *
 * @param {number} egiId The ID of the EGI to reserve
 * @returns {void}
 *
 * @private
 */
function openReservationModal(egiId: number): void {
    const modal = initReservationModal(egiId);
    modal.open();
}

/**
 * 📜 Oracode Function: updateButtonStates
 * 🎯 Purpose: Update the visual state of all reservation buttons based on their reservation status
 * ⚠️ DISABLED: Server-side rendering controls visual appearance
 *
 * @export
 * @returns {Promise<void>}
 */
export async function updateButtonStates(): Promise<void> {
    console.log("🚫 updateButtonStates: DISABLED - Server-side rendering controls visual appearance");
    return;
}

export default {
    initialize,
    updateButtonStates
};
