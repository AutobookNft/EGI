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
} from '../types';
import { getAppConfig, route, ServerErrorResponse } from '../../../config/appConfig';
import { getCsrfTokenTS } from '../../../utils/csrf';

/**
 * Base API client for reservation operations
 */
export class ReservationApiClient {

    /**
     * Create a new reservation
     */
    async createReservation(data: ReservationFormData): Promise<ReservationResponse | ServerErrorResponse> {
        // TODO: Move implementation from original file
        throw new Error('Implementation needed');
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
     */
    async createPreLaunchReservation(data: PreLaunchReservationData): Promise<PreLaunchReservationResponse | ServerErrorResponse> {
        // TODO: Move implementation from original file
        throw new Error('Implementation needed');
    }

    /**
     * Get pre-launch rankings
     */
    async getPreLaunchRankings(egiId: number): Promise<RankingsResponse | ServerErrorResponse> {
        // TODO: Move implementation from original file
        throw new Error('Implementation needed');
    }

    /**
     * Withdraw pre-launch reservation
     */
    async withdrawPreLaunchReservation(reservationId: number): Promise<{ success: boolean; message: string }> {
        // TODO: Move implementation from original file
        throw new Error('Implementation needed');
    }

    /**
     * Common request helper with CSRF and error handling
     */
    private async makeRequest<T>(url: string, options: RequestInit = {}): Promise<T> {
        // TODO: Implement common request logic
        throw new Error('Implementation needed');
    }
}
