/**
 * 📜 Reservation API Client
 * 🎯 Purpose: Handle all HTTP communication with reservation endpoints
 * 🧱 Core Logic: Single responsibility for API calls only
 * 🛡️ Security: CSRF token handling and error management
 * 
 * @version 1.0.0
 * @date 2025-08-22
 * @author GitHub Copilot for Fabio Cherici
 */

import {
    ReservationFormData,
    ReservationResponse,
    ReservationStatusResponse,
    AlgoExchangeRateResponse,
    PreLaunchReservationData,
    PreLaunchReservationResponse,
    RankingsResponse
} from '../../../types/reservationTypes';
import { getAppConfig, route, ServerErrorResponse } from '../../../config/appConfig';
import { getCsrfTokenTS } from '../../../utils/csrf';

/**
 * Base API client for reservation operations
 */
export class ReservationApiClient {

    /**
     * Create a new reservation (reserve EGI)
     * @param {number} egiId The ID of the EGI to reserve
     * @param {ReservationFormData} data The reservation form data
     * @returns {Promise<ReservationResponse>} The reservation response
     */
    async createReservation(egiId: number, data: ReservationFormData): Promise<ReservationResponse> {
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
     * Get reservation status for an EGI
     */
    async getReservationStatus(egiId: number): Promise<ReservationStatusResponse | ServerErrorResponse> {
        try {
            // Use UEM.safeFetch if available, otherwise use regular fetch
            const statusUrl = route('api.egis.reservation-status', { egi: egiId });

            console.log('getEgiReservationStatus: route:', statusUrl);

            if ((window as any).UEM && typeof (window as any).UEM.safeFetch === 'function') {
                const response = await (window as any).UEM.safeFetch(statusUrl, {
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
     */
    async cancelReservation(reservationId: number): Promise<{ success: boolean; message: string }> {
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

    /**
     * Get current ALGO exchange rate
     */
    async getAlgoExchangeRate(): Promise<AlgoExchangeRateResponse> {
        // TODO: Move implementation from original file
        throw new Error('Implementation needed');
    }

    /**
     * Create pre-launch reservation
     * @param {number} egiId The EGI ID
     * @param {number} amountEur The amount in EUR
     * @returns {Promise<PreLaunchReservationResponse>} The reservation response
     */
    async createPreLaunchReservation(
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

            return await response.json();
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
     * Get pre-launch rankings
     */
    async getPreLaunchRankings(egiId: number): Promise<RankingsResponse> {
        try {
            const response = await fetch(`/api/reservations/pre-launch/rankings/${egiId}`, {
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
     * Withdraw pre-launch reservation
     * @param {number} reservationId The reservation ID
     * @returns {Promise<{success: boolean, message: string}>} The withdrawal response
     */
    async withdrawPreLaunchReservation(reservationId: number): Promise<{ success: boolean; message: string }> {
        try {
            const response = await fetch(`/api/reservations/pre-launch/${reservationId}/withdraw`, {
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
     * Common request helper with CSRF and error handling
     */
    private async makeRequest<T>(url: string, options: RequestInit = {}): Promise<T> {
        // TODO: Implement common request logic
        throw new Error('Implementation needed');
    }
}
