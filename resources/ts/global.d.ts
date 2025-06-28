// Global type definitions for FlorenceEGI
// Path: resources/ts/global.d.ts

// Personal Data Configuration Interface
interface PersonalDataConfig {
    acceptedCookies: boolean;
    acceptedTerms: boolean;
    acceptedPrivacy: boolean;
    userId?: number;
    timestamp?: string;
}

// Toast Type Definition
type ToastType = 'success' | 'error' | 'warning' | 'info';

// UEM Error Manager Interface (basic structure)
interface UEMClient {
    logError: (code: string, message: string, context?: any) => void;
    handleAjaxError: (xhr: any, textStatus: string, errorThrown: string) => void;
    safeFetch: (url: string, options?: RequestInit) => Promise<Response>;
    initialize: () => void;
}

// Like Manager Interface
interface LikeManager {
    initialize: () => void;
    toggleLike: (resourceType: string, resourceId: number) => Promise<boolean>;
}

// Global Window Extensions
declare global {
    interface Window {
        // Like Management
        likeManager?: LikeManager;

        // Personal Data Configuration
        personalDataConfig: PersonalDataConfig;

        // Ultra Error Manager Client
        UEM: UEMClient;

        // SweetAlert2
        Swal: any;

        // Toast Notification System
        showToast?: (message: string, type: ToastType) => void;

        // Laravel Mix/Vite helpers (se necessari)
        route?: (name: string, params?: any) => string;

        // CSRF Token
        csrfToken?: string;

        // Laravel Echo (se usi broadcasting)
        Echo?: any;
    }

    // Utility Types for FlorenceEGI
    namespace FlorenceEGI {
        interface EGI {
            id: number;
            title: string;
            description?: string;
            price?: number;
            likes_count: number;
            is_liked: boolean;
            collection_id: number;
            user_id: number;
            type?: string;
            extension?: string;
        }

        interface Collection {
            id: number;
            collection_name: string;
            creator_id: number;
            epp_id?: number;
        }

        interface ReservationCertificate {
            id: number;
            egi_id: number;
            user_id: number;
            status: 'active' | 'expired' | 'redeemed';
            created_at: string;
            expires_at: string;
        }
    }
}

// Questo export {} è FONDAMENTALE per far funzionare le dichiarazioni globali
export {};
