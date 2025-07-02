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
    handleClientError: (code: string, context?: any) => void;
}

// Like Manager Interface
interface LikeManager {
    initialize: () => void;
    toggleLike: (resourceType: string, resourceId: number) => Promise<boolean>;
}

// --- 📊 NOTIFICATION STATUS TYPES ---
export type NotificationStatusValue =
    | 'pending'
    | 'done'
    | 'expired'
    | 'Archived'
    | 'request'
    | 'Accepted'
    | 'Rejected'
    | 'pending_confirmation'
    | 'confirmed'
    | 'revoked'
    | 'disavowed'
    | 'creation'
    | 'pending_create'
    | 'pending_update'
    | 'update';

// --- 🔧 TIPIZZAZIONI TRANSLATIONS E ENUMS ---
interface TranslationsFunction {
    (key: string, fallback?: string): string;
}

interface EnsureTranslationsLoadedFunction {
    (): Promise<void>;
}

interface GetEnumFunction {
    (enumName: string, key: string): any;
}

interface IsPendingStatusFunction {
    (status: NotificationStatusValue | string): Promise<boolean>;
}

// --- 💰 TIPIZZAZIONI MODULI WALLET ---
interface WalletNotificationModuleConstructor {
    new (config: { apiBaseUrl: string }): any;
}

// Global Window Extensions
declare global {
    interface Window {
        // --- 📦 JQUERY ---
        $: JQueryStatic;
        jQuery: JQueryStatic;

        // --- 🍯 SWEETALERT2 ---
        Swal: any;

        // --- 🔄 FUNCTIONS RESE GLOBALI DA APP.JS ---
        getTranslation: TranslationsFunction;
        ensureTranslationsLoaded: EnsureTranslationsLoadedFunction;
        getEnum: GetEnumFunction;
        isPendingStatus: IsPendingStatusFunction;

        // --- 🎯 LIKE MANAGEMENT ---
        likeManager?: LikeManager;

        // --- 📊 PERSONAL DATA CONFIGURATION ---
        personalDataConfig: PersonalDataConfig;

        // --- ⚠️ ULTRA ERROR MANAGER CLIENT ---
        UEM: UEMClient;

        // --- 🍞 TOAST NOTIFICATION SYSTEM ---
        showToast?: (message: string, type: ToastType) => void;

        // --- 🛤️ LARAVEL HELPERS ---
        route?: (name: string, params?: any) => string;
        csrfToken?: string;
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

// --- 📤 MODULO ULTRA UPLOAD MANAGER ---
declare module '/vendor/ultra/ultra-upload-manager/resources/ts/core/file_upload_manager' {
    export let files: File[];
    export function initializeApp(): void;
    export function handleFileSelect(event: Event): void;
    export function handleDrop(event: DragEvent): void;
    export function cancelUpload(): Promise<void>;
}

// --- 💰 MODULI WALLET NOTIFICATIONS ---
declare module '../js/modules/notifications/init/request-notification-wallet-init' {
    export const RequestCreateNotificationWallet: WalletNotificationModule;
    export const RequestUpdateNotificationWallet: WalletNotificationModule;
    export const RequestWalletDonation: WalletNotificationModule;
}

declare module '../js/modules/notifications/delete-proposal-invitation' {
    export const DeleteProposalInvitation: WalletNotificationModule;
}

declare module '../js/modules/notifications/delete-proposal-wallet' {
    export const DeleteProposalWallet: WalletNotificationModule;
}

// --- 🔄 UTILITIES MODULES ---
declare module '../js/utils/translations' {
    export const fetchTranslations: () => Promise<void>;
    export const ensureTranslationsLoaded: EnsureTranslationsLoadedFunction;
    export const getTranslation: TranslationsFunction;
}

declare module '../js/utils/enums' {
    export const loadEnums: () => Promise<void>;
    export const getEnum: GetEnumFunction;
    export const isPendingStatus: IsPendingStatusFunction;
}

// --- 🎮 ANIMAZIONE THREE.JS ---
declare module '../js/sfera-geodetica' {
    export const initThreeAnimation: () => void;
}

// --- 🔔 MODULI AUTO-ESEGUITI (se hanno export) ---
declare module '../js/notification' {
    // Se ha export, definisci qui
}

declare module '../js/modules/notifications/init/notification-response-init' {
    // Se ha export, definisci qui
}

// --- 📝 EDITOR LEGALE ---
declare module '../js/legal/editor' {
    // Se ha export, definisci qui
}

// Questo export {} è FONDAMENTALE per far funzionare le dichiarazioni globali
export {};
