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
    if (isInitialized) return;

    try {
        // Find all reservation buttons
        const buttons = document.querySelectorAll<HTMLElement>('.reserve-button');

        if (buttons.length === 0) {
            console.log('Padmin ReservationButtons: No reservation buttons found on the page');
            return;
        }

        console.log(`Padmin ReservationButtons: Found ${buttons.length} reservation buttons`);

        // Attach event handlers
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

        // Update button states
        await updateButtonStates();

        isInitialized = true;
        console.log('Padmin ReservationButtons: Initialization complete');

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
                                <li><strong>${appTranslate('reservation.already_reserved.amount')}:</strong> €${reservation?.offer_amount_eur.toFixed(2)}</li>
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
                }).then((result) => {
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
 *
 * @export
 * @returns {Promise<void>}
 */
export async function updateButtonStates(): Promise<void> {
    if (reservationButtons.size === 0) return;

    // Group buttons by EGI ID to avoid duplicate API calls
    const egiButtonMap = new Map<number, HTMLElement[]>();

    for (const [button, egiId] of reservationButtons.entries()) {
        if (!egiButtonMap.has(egiId)) {
            egiButtonMap.set(egiId, []);
        }
        egiButtonMap.get(egiId)?.push(button);
    }

    // Update buttons for each EGI
    for (const [egiId, buttons] of egiButtonMap.entries()) {
        try {
            const status = await getEgiReservationStatus(egiId);

            if (status.success && status.data) {
                const { is_reserved, user_has_reservation, user_reservation } = status.data;

                buttons.forEach(button => {
                    // Reset button state
                    button.classList.remove('bg-green-600', 'bg-green-700', 'bg-yellow-600', 'bg-gray-500');
                    button.classList.add('bg-green-600', 'hover:bg-green-700');

                    // Remove any badge
                    const existingBadge = button.querySelector('.reservation-badge');
                    if (existingBadge) {
                        existingBadge.remove();
                    }

                    // Update button text and style based on reservation status
                    if (user_has_reservation) {
                        // User has a reservation
                        button.innerHTML = `
                            <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                            </svg>
                            ${appTranslate('reservation.button.reserved')}
                        `;

                        // Add badge if appropriate
                        if (user_reservation) {
                            const badgeClass = user_reservation.is_highest_priority
                                ? 'bg-green-100 text-green-800 border-green-200'
                                : 'bg-yellow-100 text-yellow-800 border-yellow-200';

                            const badgeText = user_reservation.is_highest_priority
                                ? appTranslate('reservation.badge.highest')
                                : appTranslate('reservation.badge.superseded');

                            const badge = document.createElement('span');
                            badge.className = `reservation-badge absolute -top-2 -right-2 py-1 px-2 rounded-full text-xs font-medium ${badgeClass} border`;
                            badge.textContent = badgeText;

                            // Set position relative on button if not already
                            if (window.getComputedStyle(button).position === 'static') {
                                button.style.position = 'relative';
                            }

                            button.appendChild(badge);

                            // Change button color for superseded reservations
                            if (!user_reservation.is_highest_priority) {
                                button.classList.remove('bg-green-600', 'hover:bg-green-700');
                                button.classList.add('bg-yellow-600', 'hover:bg-yellow-700');
                            }
                        }
                    } else if (is_reserved) {
                        // Reserved by someone else
                        button.innerHTML = `
                            <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.25 2A1.75 1.75 0 0 0 2.5 3.75v14.5a.75.75 0 0 0 1.218.582l5.534-4.426a.75.75 0 0 1 .496 0l5.534 4.427A.75.75 0 0 0 17.5 18.25V3.75A1.75 1.75 0 0 0 15.75 2h-11.5Z" clip-rule="evenodd" />
                            </svg>
                            ${appTranslate('reservation.button.make_offer')}
                        `;
                    } else {
                        // Not reserved
                        button.innerHTML = `
                            <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.25 2A1.75 1.75 0 0 0 2.5 3.75v14.5a.75.75 0 0 0 1.218.582l5.534-4.426a.75.75 0 0 1 .496 0l5.534 4.427A.75.75 0 0 0 17.5 18.25V3.75A1.75 1.75 0 0 0 15.75 2h-11.5Z" clip-rule="evenodd" />
                            </svg>
                            ${appTranslate('reservation.button.reserve')}
                        `;
                    }
                });
            }
        } catch (error) {
            console.error(`Padmin ReservationButtons: Error updating buttons for EGI ${egiId}`, error);
        }
    }
}

export default {
    initialize,
    updateButtonStates
};
