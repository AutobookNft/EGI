// File: resources/ts/services/uemClientService.ts

/**
 * 📜 Oracode TypeScript Module: UltraErrorManager Client Service
 * Cliente TypeScript per la gestione unificata degli errori lato frontend.
 * Integra perfettamente con il backend UEM di FlorenceEGI.
 *
 * @version 2.0.1 (Route & Config Fixed)
 * @date 2025-05-13
 * @author Padmin D. Curtis (for Fabio Cherici)
 */

import { ServerErrorResponse, appTranslate, getAppConfig } from "../config/appConfig";

// --- TYPES ---
interface ErrorConfig {
    [errorCode: string]: {
        type: 'critical' | 'error' | 'warning' | 'notice';
        blocking: 'blocking' | 'semi-blocking' | 'not';
        dev_message_key?: string;
        user_message_key?: string;
        http_status_code?: number;
        msg_to?: 'sweet-alert' | 'toast' | 'div' | 'modal';
    };
}

interface UEMConfig {
    errors: ErrorConfig;
    default_display_mode: 'sweet-alert' | 'toast' | 'div' | 'modal';
    error_container_id: string;
    error_message_id: string;
}

interface UltraErrorEventDetail {
    errorCode: string;
    message: string;
    blocking: string;
    displayMode: string;
    context?: any;
}

// --- STATE ---
let uemConfig: UEMConfig | null = null;
let isInitialized = false;
let isInitializing = false;
let configPromise: Promise<void> | null = null;

// --- HELPER FUNCTIONS ---
function getDefaultConfig(): UEMConfig {
    return {
        errors: {
            'UNEXPECTED_ERROR': {
                type: 'error',
                blocking: 'semi-blocking',
                msg_to: 'sweet-alert'
            },
            'NETWORK_ERROR': {
                type: 'error',
                blocking: 'semi-blocking',
                msg_to: 'toast'
            },
            'CLIENT_ERROR': {
                type: 'warning',
                blocking: 'not',
                msg_to: 'toast'
            }
        },
        default_display_mode: 'toast',
        error_container_id: 'error-container',
        error_message_id: 'error-message'
    };
}

async function loadUEMConfig(): Promise<void> {
    if (isInitialized || isInitializing) {
        return configPromise || Promise.resolve();
    }

    isInitializing = true;

    // Usa l'URL corretto come definito nelle route Laravel
    const errorDefinitionsUrl = '/api/error-definitions';

    configPromise = fetch(errorDefinitionsUrl, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content || ''
        }
    })
    .then(response => {
        if (!response.ok) {
            console.warn(`UEM Config: Failed to load from API (${response.status}). Using defaults.`);
            throw new Error('Config load failed');
        }
        return response.json();
    })
    .then(data => {
        uemConfig = data;
        isInitialized = true;
        console.log('UEM Config: Loaded successfully from API');
    })
    .catch(error => {
        console.warn('UEM Config: Using default configuration', error);
        uemConfig = getDefaultConfig();
        isInitialized = true;
    })
    .finally(() => {
        isInitializing = false;
    });

    return configPromise;
}

function displayError(message: string, displayMode: string, blocking: string): void {
    // Non usare getAppConfig() qui se non siamo sicuri che sia inizializzato
    try {
        const config = getAppConfig();

        switch (displayMode) {
            case 'sweet-alert':
                if (window.Swal) {
                    window.Swal.fire({
                        icon: blocking === 'blocking' ? 'error' : 'warning',
                        title: blocking === 'blocking' ? appTranslate('errorTitle') : appTranslate('warningTitle'),
                        text: message,
                        confirmButtonColor: '#3085d6',
                        allowOutsideClick: blocking !== 'blocking'
                    });
                } else {
                    alert(message);
                }
                break;

            case 'toast':
                if (window.showToast && typeof window.showToast === 'function') {
                    window.showToast(message, blocking === 'blocking' ? 'error' : 'warning');
                } else if (window.Swal) {
                    window.Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: blocking === 'blocking' ? 'error' : 'warning',
                        title: message,
                        showConfirmButton: false,
                        timer: 3000
                    });
                } else {
                    console.error('UEM Display: No toast handler available', message);
                }
                break;

            case 'div':
                const container = document.getElementById(uemConfig?.error_container_id || 'error-container');
                const messageEl = document.getElementById(uemConfig?.error_message_id || 'error-message');

                if (container && messageEl) {
                    messageEl.textContent = message;
                    container.classList.remove('hidden');

                    if (blocking !== 'blocking') {
                        setTimeout(() => {
                            container.classList.add('hidden');
                        }, 5000);
                    }
                } else {
                    console.error('UEM Display: DOM elements not found for div display', message);
                }
                break;

            case 'modal':
                // Implementa modal display se necessario
                console.log('UEM Display: Modal display not implemented, falling back to alert', message);
                alert(message);
                break;

            default:
                console.error('UEM Display: Unknown display mode', displayMode, message);
                alert(message);
        }
    } catch (error) {
        // Fallback se getAppConfig() fallisce
        alert(message);
    }
}

function dispatchErrorEvent(detail: UltraErrorEventDetail): void {
    const event = new CustomEvent('ultraError', {
        detail: detail,
        bubbles: true,
        cancelable: false
    });
    document.dispatchEvent(event);
}

// --- MAIN EXPORT ---
export const UEM_Client_TS_Placeholder = {
    // Mantieni compatibilità con i metodi esistenti
    handleServerErrorResponse: (errorData: ServerErrorResponse, fallbackMessage: string = 'An error occurred.') => {
        const message = errorData.message || fallbackMessage;
        const displayMode = errorData.display_mode || uemConfig?.default_display_mode || 'sweet-alert';
        const blocking = errorData.blocking || 'semi-blocking';

        displayError(message, displayMode, blocking);

        dispatchErrorEvent({
            errorCode: errorData.error,
            message: message,
            blocking: blocking,
            displayMode: displayMode,
            context: errorData
        });
    },

    handleClientError: (errorCode: string, context: object = {}, originalError?: Error, userMessage?: string) => {
        const errorConfig = uemConfig?.errors[errorCode] || uemConfig?.errors['CLIENT_ERROR'] || {
            type: 'warning',
            blocking: 'not',
            msg_to: 'toast'
        };

        let message = userMessage;

        if (!message && errorConfig.user_message_key) {
            try {
                const config = getAppConfig();
                message = appTranslate(errorConfig.user_message_key);
            } catch (e) {
                // Config not ready, use fallback
                message = null;
            }
        }

        if (!message) {
            message = `Client error: ${errorCode}. See console.`;
        }

        console.error(`UEM Client Error [${errorCode}]:`, context, originalError);

        const displayMode = errorConfig.msg_to || uemConfig?.default_display_mode || 'toast';
        displayError(message, displayMode, errorConfig.blocking);

        dispatchErrorEvent({
            errorCode: errorCode,
            message: message,
            blocking: errorConfig.blocking,
            displayMode: displayMode,
            context: { ...context, originalError: originalError?.message }
        });
    },

    // Nuovi metodi per l'implementazione completa
    initialize: async (): Promise<void> => {
        if (!isInitialized && !isInitializing) {
            await loadUEMConfig();
        }
        return configPromise || Promise.resolve();
    },

    safeFetch: async (url: string, options: RequestInit = {}): Promise<Response> => {
        try {
            const response = await fetch(url, options);

            if (!response.ok) {
                const contentType = response.headers.get('content-type');

                if (contentType && contentType.includes('application/json')) {
                    try {
                        const errorData = await response.json();
                        if (errorData && typeof errorData.error === 'string') {
                            UEM_Client_TS_Placeholder.handleServerErrorResponse(errorData as ServerErrorResponse);
                        } else {
                            UEM_Client_TS_Placeholder.handleClientError('SERVER_ERROR', {
                                status: response.status,
                                statusText: response.statusText,
                                url: url
                            });
                        }
                    } catch (jsonError) {
                        UEM_Client_TS_Placeholder.handleClientError('JSON_ERROR', {
                            status: response.status,
                            url: url
                        });
                    }
                } else {
                    UEM_Client_TS_Placeholder.handleClientError('API_UNEXPECTED_RESPONSE', {
                        status: response.status,
                        statusText: response.statusText,
                        url: url
                    });
                }
            }

            return response;

        } catch (networkError: any) {
            UEM_Client_TS_Placeholder.handleClientError('NETWORK_ERROR', {
                url: url,
                errorMessage: networkError?.message
            }, networkError instanceof Error ? networkError : undefined);
            throw networkError;
        }
    },

    onError: (callback: (event: CustomEvent<UltraErrorEventDetail>) => void): (() => void) => {
        const handler = (e: Event) => callback(e as CustomEvent<UltraErrorEventDetail>);
        document.addEventListener('ultraError', handler);
        return () => document.removeEventListener('ultraError', handler);
    },

    getConfig: (): UEMConfig | null => uemConfig,

    isReady: (): boolean => isInitialized
};

// Export anche il nome corretto
export const UEM = UEM_Client_TS_Placeholder;
