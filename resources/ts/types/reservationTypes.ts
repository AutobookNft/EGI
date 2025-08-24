/**
 * 📜 Reservation Types Module
 * 🎯 Purpose: Centralized type definitions for reservation system
 *
 * @version 1.0.0
 * @date 2025-08-21
 * @author Refactored by GitHub Copilot
 */

// ============================================================================
// CORE RESERVATION TYPES
// ============================================================================

export interface ReservationFormData {
    offer_amount_fiat: number;
    terms_accepted: boolean;
    contact_data?: {
        name?: string;
        email?: string;
        message?: string;
    };
    wallet?: string;
}

export interface ReservationResponse {
    success: boolean;
    message: string;
    data?: {
        user?: {
            id: number;
            name?: string;
            last_name?: string;
            wallet?: string;
            avatar?: string;
            is_commissioner?: boolean;
        };
        reservation?: {
            id: number;
            type: 'strong' | 'weak';
            offer_amount_fiat: number;
            offer_amount_algo: number;
            amount_eur?: number;
            status: string;
            is_current: boolean;
            fegi_code?: string;
        };
    };
    reservation?: {
        id: number;
        type: 'strong' | 'weak';
        offer_amount_fiat: number;
        offer_amount_algo: number;
        amount_eur?: number;
        status: string;
        is_current: boolean;
        fegi_code?: string;
        user?: {
            id: number;
            name?: string;
            last_name?: string;
            wallet?: string;
            avatar?: string;
            is_commissioner?: boolean;
        };
    };
    certificate?: {
        uuid: string;
        url: string;
        verification_url: string;
        pdf_url: string;
    };
    error_code?: string;
}

export interface ReservationStatusResponse {
    success: boolean;
    data?: {
        egi_id: number;
        is_reserved: boolean;
        total_reservations: number;
        user_has_reservation: boolean;
        highest_priority_reservation?: {
            type: 'strong' | 'weak';
            offer_amount_fiat: number;
            belongs_to_current_user: boolean;
        };
        user_reservation?: {
            id: number;
            type: 'strong' | 'weak';
            offer_amount_fiat: number;
            offer_amount_algo: number;
            is_highest_priority: boolean;
            created_at: string;
            certificate?: {
                uuid: string;
                url: string;
            };
        };
    };
    message?: string;
    error_code?: string;
}

// ============================================================================
// EXCHANGE RATE TYPES
// ============================================================================

export interface AlgoExchangeRateResponse {
    success: boolean;
    message?: string;
    data?: {
        fiat_currency: string;
        rate_to_algo: number;
        timestamp: string;
        is_cached: boolean;
    };
    rate?: number; // Backward compatibility
    updated_at?: string; // Backward compatibility
}

// ============================================================================
// PRE-LAUNCH TYPES
// ============================================================================

export interface PreLaunchReservationData {
    egi_id: number;
    amount_eur: number;
}

export interface PreLaunchReservationResponse {
    success: boolean;
    message: string;
    data?: {
        reservation_id: number;
        egi_id: number;
        amount_eur: number;
        rank_position: number;
        is_highest: boolean;
        created_at: string;
        updated_at: string;
    };
}

export interface RankingEntry {
    rank_position: number;
    amount_eur: number;
    is_highest: boolean;
    is_mine: boolean;
    user?: {
        name: string;
    };
    created_at: string;
}

export interface RankingsResponse {
    success: boolean;
    data?: {
        egi_id: number;
        egi_title: string;
        total_reservations: number;
        rankings: RankingEntry[];
        stats: {
            total_reservations: number;
            highest_amount: number;
            lowest_amount: number;
            average_amount: number;
            median_amount: number;
        };
    };
}
