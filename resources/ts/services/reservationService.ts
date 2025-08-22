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
 * RESERVATION SERVICE - SOLID Architecture Implementation
 * 
 * This service has been refactored following SOLID principles with:
 * - ReservationApiClient: API communication layer (SRP)
 * - ReservationModalUI: UI component management (SRP)
 * - Factory patterns for singleton management
 * 
 * @author Fabio Cherici
 * @refactored 2025-01-20 - SOLID Architecture Implementation
 */
import { ServerErrorResponse } from '../config/appConfig';
import { getAlgoExchangeRate, getCachedAlgoRate, setCachedAlgoRate } from './reservation/ExchangeRateService';
import { ReservationApiClient } from './reservation/api/ReservationApiClient';
import { ReservationModalUI } from './reservation/ui/ReservationModalUI';
import { ReservationFormModal } from './reservation/modal/ReservationFormModal';
import type {
    ReservationFormData,
    ReservationResponse,
    ReservationStatusResponse,
    PreLaunchReservationResponse,
    RankingsResponse
} from '../types/reservationTypes';

// 🎯 SOLID Architecture Instances - gradual migration
const reservationApiClient = new ReservationApiClient();

// --- STATE ---
let reservationModalInstance: ReservationFormModal | null = null;



/**
 * Initialize the reservation modal for a specific EGI - Delegates to modal factory
 *
 * @param {number} egiId The ID of the EGI to reserve
 * @returns {ReservationFormModal} The reservation modal instance
 */
export function initReservationModal(egiId: number): ReservationFormModal {
    return getOrCreateReservationModal(egiId);
}

/**
 * Factory function for reservation modal singleton
 */
function getOrCreateReservationModal(egiId: number): ReservationFormModal {
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
    return reservationApiClient.createReservation(egiId, data);
}

/**
 * Get the reservation status for an EGI
 *
 * @param {number} egiId The ID of the EGI to check
 * @returns {Promise<ReservationStatusResponse>} The reservation status response
 * @return {Promise<ServerErrorResponse>} The reservation status response
 */
export async function getEgiReservationStatus(egiId: number): Promise<ReservationStatusResponse | ServerErrorResponse> {
    return reservationApiClient.getReservationStatus(egiId);
}

/**
 * Cancel a reservation
 *
 * @param {number} reservationId The ID of the reservation to cancel
 * @returns {Promise<{success: boolean, message: string}>} The cancellation response
 */
export async function cancelReservation(reservationId: number): Promise<{ success: boolean, message: string }> {
    return reservationApiClient.cancelReservation(reservationId);
}

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
    const data = await reservationApiClient.createPreLaunchReservation(egiId, amountEur);

    // Handle success with UI feedback
    if (data.success && data.data) {
        // TODO: Fix ReservationModalUI.showPreLaunchSuccessModal to accept data parameter
        const modalUI = new ReservationModalUI();
        // modalUI.showPreLaunchSuccessModal(); // Needs data parameter
        console.log('Pre-launch success modal needed:', data.data);
    }

    return data;
}

/**
 * Get current rankings for an EGI
 *
 * @param {number} egiId The EGI ID
 * @returns {Promise<RankingsResponse>} The rankings response
 */
export async function getPreLaunchRankings(egiId: number): Promise<RankingsResponse> {
    return reservationApiClient.getPreLaunchRankings(egiId);
}

/**
 * Withdraw a pre-launch reservation - Delegates to ReservationApiClient
 *
 * @param {number} reservationId The reservation ID
 * @returns {Promise<{success: boolean, message: string}>} The withdrawal response
 */
export async function withdrawPreLaunchReservation(
    reservationId: number
): Promise<{ success: boolean, message: string }> {
    return reservationApiClient.withdrawPreLaunchReservation(reservationId);
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

