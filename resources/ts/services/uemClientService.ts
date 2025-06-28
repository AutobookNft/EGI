// File: resources/ts/services/uemClientService.ts

/**
 * UltraErrorManager Client Service OS2.0 - Centralized Architecture
 *
 * Pure client implementation following server-centric error management.
 * Zero client-side decisions, server config is the only source of truth.
 * Fatal initialization - if config loading fails, application cannot proceed.
 *
 * @version 2.0.0-oracode
 * @author Padmin D. Curtis OS2.0 (for Fabio Cherici)
 * @package FlorenceEGI.UEM.Client
 */

import { ServerErrorResponse, appTranslate, getAppConfig } from "../config/appConfig";

// --- GLOBAL TYPE EXTENSIONS ---

// --- TYPES ---
interface ErrorConfig {
    type: 'critical' | 'error' | 'warning' | 'notice';
    blocking: 'blocking' | 'semi-blocking' | 'not';
    dev_message_key?: string;
    user_message_key?: string;
    http_status_code?: number;
    msg_to?: 'sweet-alert' | 'toast' | 'div' | 'modal' | 'log-only';
    devTeam_email_need?: boolean;
    notify_slack?: boolean;
}

interface UEMServerConfig {
    errors: { [errorCode: string]: ErrorConfig };
    ui: {
        default_display_mode: 'sweet-alert' | 'toast' | 'div' | 'modal' | 'log-only';
        error_container_id: string;
        error_message_id: string;
        show_error_codes: boolean;
    };
}

interface UltraErrorEventDetail {
    errorCode: string;
    message: string;
    blocking: string;
    displayMode: string;
    context?: any;
    timestamp: string;
}

// --- STATE MANAGEMENT ---
let serverConfig: UEMServerConfig | null = null;
let isInitialized = false;
let isInitializing = false;
let initializationPromise: Promise<void> | null = null;

// --- CORE FUNCTIONS ---

/**
 * Load error configuration from server - FATAL if fails
 * This is the ONLY source of truth for error handling behavior
 */
async function loadServerConfiguration(): Promise<void> {
    if (isInitialized || isInitializing) {
        return initializationPromise || Promise.resolve();
    }

    isInitializing = true;
    console.log('UEM Client: Loading server configuration...');

    initializationPromise = fetch('/api/error-definitions', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content || ''
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`Server config load failed: HTTP ${response.status} ${response.statusText}`);
        }
        return response.json();
    })
    .then((data: UEMServerConfig) => {
        if (!data || !data.errors) {
            throw new Error('Invalid server configuration: missing errors definition');
        }

        serverConfig = data;
        isInitialized = true;
        console.log('UEM Client: Server configuration loaded successfully');
        console.log('UEM Client: Available error codes:', Object.keys(data.errors).length);
    })
    .catch(error => {
        isInitializing = false;
        console.error('UEM Client: FATAL - Cannot load server configuration:', error);

        // FATAL ERROR - Application cannot proceed without error config
        const fatalMessage = 'Critical system error: Unable to load error management configuration. The application cannot start safely.';

        if (window.Swal) {
            window.Swal.fire({
                icon: 'error',
                title: 'System Error',
                text: fatalMessage,
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: true,
                confirmButtonText: 'Reload Page',
                confirmButtonColor: '#dc2626'
            }).then(() => {
                window.location.reload();
            });
        } else {
            alert(fatalMessage + '\n\nThe page will reload.');
            window.location.reload();
        }

        throw error; // Re-throw to prevent further initialization
    })
    .finally(() => {
        isInitializing = false;
    });

    return initializationPromise;
}

/**
 * Get error configuration from server config
 * Only works after successful initialization
 */
function getErrorConfig(errorCode: string): ErrorConfig | null {
    if (!isInitialized || !serverConfig) {
        console.error('UEM Client: Cannot get error config - not initialized');
        return null;
    }

    return serverConfig.errors[errorCode] ||
           serverConfig.errors['UNDEFINED_ERROR_CODE'] ||
           null;
}

/**
 * Display error according to server configuration
 * Respects blocking levels and display modes strictly:
 * - "not": NO UI display, console only
 * - msg_to empty or "log-only": NO UI display
 * - "semi-blocking": Non-invasive notification
 * - "blocking": Blocking modal/alert
 */
function displayErrorByServerConfig(
    errorCode: string,
    message: string,
    errorConfig: ErrorConfig,
    context?: any
): void {
    const blocking = errorConfig.blocking;
    const displayMode = errorConfig.msg_to || serverConfig?.ui.default_display_mode || 'sweet-alert';

    // STRICT blocking level enforcement - NO display for non-blocking
    if (blocking === 'not') {
        console.warn(`[UEM] ${errorCode}: ${message}`, context);
        return;
    }

    // STRICT display mode enforcement - NO display for log-only or empty
    if (!displayMode || displayMode === 'log-only') {
        console.warn(`[UEM] ${errorCode}: ${message} (log-only)`, context);
        return;
    }

    // Log for all displayed errors
    console.error(`[UEM] ${errorCode}: ${message}`, context);

    try {
        switch (displayMode) {
            case 'sweet-alert':
                if (window.Swal) {
                    window.Swal.fire({
                        icon: blocking === 'blocking' ? 'error' : 'warning',
                        title: blocking === 'blocking' ? 'Error' : 'Warning',
                        text: message,
                        confirmButtonColor: blocking === 'blocking' ? '#dc2626' : '#3085d6',
                        allowOutsideClick: blocking !== 'blocking',
                        allowEscapeKey: blocking !== 'blocking'
                    });
                } else {
                    // Fallback to native alert
                    alert(message);
                }
                break;

            case 'toast':
                if (window.showToast && typeof window.showToast === 'function') {
                    window.showToast(message, blocking === 'blocking' ? 'error' : 'warning');
                } else if (window.Swal) {
                    // Fallback to SweetAlert toast
                    window.Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: blocking === 'blocking' ? 'error' : 'warning',
                        title: message,
                        showConfirmButton: false,
                        timer: blocking === 'blocking' ? 8000 : 5000
                    });
                } else {
                    console.error('UEM Display: No toast implementation available');
                    alert(message); // Ultimate fallback
                }
                break;

            case 'div':
                const containerId = serverConfig?.ui.error_container_id || 'error-container';
                const messageId = serverConfig?.ui.error_message_id || 'error-message';
                const container = document.getElementById(containerId);
                const messageEl = document.getElementById(messageId);

                if (container && messageEl) {
                    messageEl.textContent = message;
                    container.classList.remove('hidden');
                    container.classList.add('error', blocking === 'blocking' ? 'error-blocking' : 'error-warning');

                    // Auto-hide non-blocking errors
                    if (blocking !== 'blocking') {
                        setTimeout(() => {
                            container.classList.add('hidden');
                            container.classList.remove('error', 'error-warning');
                        }, 6000);
                    }
                } else {
                    console.error(`UEM Display: DOM elements not found (${containerId}, ${messageId})`);
                    alert(message); // Fallback
                }
                break;

            case 'modal':
                // Custom modal implementation would go here
                console.warn('UEM Display: Custom modal not implemented, using SweetAlert');
                if (window.Swal) {
                    window.Swal.fire({
                        icon: blocking === 'blocking' ? 'error' : 'warning',
                        title: blocking === 'blocking' ? 'Error' : 'Warning',
                        text: message,
                        confirmButtonColor: blocking === 'blocking' ? '#dc2626' : '#3085d6',
                        allowOutsideClick: blocking !== 'blocking'
                    });
                } else {
                    alert(message);
                }
                break;

            case 'log-only':
                // Explicit log-only mode - no UI display
                console.log(`[UEM Log-Only] ${errorCode}: ${message}`, context);
                break;

            default:
                console.error(`UEM Display: Unknown display mode '${displayMode}', using alert fallback`);
                alert(message);
        }
    } catch (displayError) {
        console.error('UEM Display: Error in display function:', displayError);
        alert(message); // Ultimate fallback
    }
}

/**
 * Dispatch custom event for error tracking/analytics
 */
function dispatchErrorEvent(detail: UltraErrorEventDetail): void {
    try {
        const event = new CustomEvent('ultraError', {
            detail: detail,
            bubbles: true,
            cancelable: false
        });
        document.dispatchEvent(event);
    } catch (error) {
        console.warn('UEM Event: Failed to dispatch error event:', error);
    }
}

/**
 * Get user-friendly message for error code
 */
function resolveUserMessage(errorCode: string, errorConfig: ErrorConfig, fallbackMessage?: string): string {
    // Try to get translated message
    if (errorConfig.user_message_key) {
        try {
            const appConfig = getAppConfig();
            const translatedMessage = appTranslate(errorConfig.user_message_key, appConfig?.translations);
            if (translatedMessage && translatedMessage !== errorConfig.user_message_key) {
                return translatedMessage;
            }
        } catch (e) {
            console.warn('UEM Message: Translation failed for', errorConfig.user_message_key);
        }
    }

    // Use fallback message if provided
    if (fallbackMessage) {
        return fallbackMessage;
    }

    // Generate generic message based on error type
    switch (errorConfig.type) {
        case 'critical':
            return 'A critical system error has occurred. Please contact support.';
        case 'error':
            return 'An error occurred while processing your request.';
        case 'warning':
            return 'Please check your input and try again.';
        case 'notice':
            return 'Please note: some functionality may be limited.';
        default:
            return `Error: ${errorCode}`;
    }
}

// --- PUBLIC API ---
export const UEM = {
    /**
     * Initialize UEM Client - MUST succeed for application to work
     * Fatal failure if server configuration cannot be loaded
     */
    initialize: async (): Promise<void> => {
        if (isInitialized) {
            return; // Already initialized
        }

        if (isInitializing && initializationPromise) {
            await initializationPromise; // Wait for ongoing initialization
            return;
        }

        await loadServerConfiguration(); // Fatal if this fails
    },

    /**
     * Handle error responses from server API calls
     * Uses server-provided configuration for display behavior
     */
    handleServerErrorResponse: (errorData: ServerErrorResponse, fallbackMessage?: string): void => {
        if (!isInitialized) {
            console.error('UEM: Cannot handle server error - not initialized', errorData);
            return;
        }

        const errorCode = errorData.error_code || 'UNEXPECTED_ERROR';
        const errorConfig = getErrorConfig(errorCode);

        if (!errorConfig) {
            console.error(`UEM: No configuration found for error code: ${errorCode}`);
            return;
        }

        // Determine effective configuration with server overrides
        const effectiveConfig: ErrorConfig = {
            ...errorConfig,
            ...(errorData.display_mode && { msg_to: errorData.display_mode as ErrorConfig['msg_to'] }),
            ...(errorData.blocking && {
                blocking: (errorData.blocking === 'blocking' ||
                          errorData.blocking === 'semi-blocking' ||
                          errorData.blocking === 'not')
                    ? errorData.blocking as ErrorConfig['blocking']
                    : errorConfig.blocking
            })
        };

        // Check if error should be displayed to user
        const shouldDisplay = effectiveConfig.msg_to &&
                             effectiveConfig.msg_to !== 'log-only' &&
                             effectiveConfig.blocking !== 'not';

        let message = '';

        if (shouldDisplay) {
            // Only process message if we're going to display it
            message = errorData.message ||
                     resolveUserMessage(errorCode, effectiveConfig, fallbackMessage);

            displayErrorByServerConfig(errorCode, message, effectiveConfig, errorData);
        } else {
            // No display - console log only
            console.warn(`[UEM] ${errorCode}: Server error (no display)`, errorData);
        }

        // Always dispatch event for tracking (but with appropriate message)
        dispatchErrorEvent({
            errorCode,
            message: shouldDisplay ? message : `[Log Only] ${errorCode}`,
            blocking: effectiveConfig.blocking,
            displayMode: effectiveConfig.msg_to || 'log-only',
            context: errorData,
            timestamp: new Date().toISOString()
        });
    },

    /**
     * Handle client-side JavaScript errors
     * Uses server configuration to determine display behavior
     */
    handleClientError: (
        errorCode: string,
        context: object = {},
        originalError?: Error,
        userMessage?: string
    ): void => {
        if (!isInitialized) {
            console.error('UEM: Cannot handle client error - not initialized', { errorCode, context });
            return;
        }

        const errorConfig = getErrorConfig(errorCode);

        if (!errorConfig) {
            console.error(`UEM: No configuration found for client error code: ${errorCode}`, context);
            return;
        }

        // Check if error should be displayed to user
        const shouldDisplay = errorConfig.msg_to &&
                             errorConfig.msg_to !== 'log-only' &&
                             errorConfig.blocking !== 'not';

        let message = '';

        if (shouldDisplay) {
            // Only process message if we're going to display it
            message = userMessage || resolveUserMessage(errorCode, errorConfig);

            displayErrorByServerConfig(errorCode, message, errorConfig, {
                ...context,
                originalError: originalError?.message,
                stack: originalError?.stack
            });
        } else {
            // No display - console log only
            console.warn(`[UEM] ${errorCode}: Client error (no display)`, context, originalError);
        }

        // Always dispatch event for tracking (but with appropriate message)
        dispatchErrorEvent({
            errorCode,
            message: shouldDisplay ? message : `[Log Only] ${errorCode}`,
            blocking: errorConfig.blocking,
            displayMode: errorConfig.msg_to || 'log-only',
            context: { ...context, originalError: originalError?.message },
            timestamp: new Date().toISOString()
        });
    },

    /**
     * Safe fetch wrapper with automatic UEM error handling
     */
    safeFetch: async (url: string, options: RequestInit = {}): Promise<Response> => {
        if (!isInitialized) {
            throw new Error('UEM: Cannot perform safe fetch - not initialized');
        }

        try {
            const response = await fetch(url, options);

            if (!response.ok) {
                const contentType = response.headers.get('content-type');

                if (contentType && contentType.includes('application/json')) {
                    try {
                        const errorData = await response.json();
                        if (errorData && errorData.error_code) {
                            UEM.handleServerErrorResponse(errorData as ServerErrorResponse);
                        } else {
                            UEM.handleClientError('API_UNEXPECTED_RESPONSE', {
                                status: response.status,
                                statusText: response.statusText,
                                url: url,
                                contentType: contentType
                            });
                        }
                    } catch (jsonError) {
                        UEM.handleClientError('API_JSON_PARSE_ERROR', {
                            status: response.status,
                            url: url,
                            parseError: jsonError instanceof Error ? jsonError.message : String(jsonError)
                        });
                    }
                } else {
                    UEM.handleClientError('API_NON_JSON_ERROR_RESPONSE', {
                        status: response.status,
                        statusText: response.statusText,
                        url: url,
                        contentType: contentType
                    });
                }
            }

            return response;

        } catch (networkError: any) {
            UEM.handleClientError('NETWORK_ERROR', {
                url: url,
                errorMessage: networkError?.message
            }, networkError instanceof Error ? networkError : undefined);
            throw networkError;
        }
    },

    /**
     * Register event listener for UEM error events
     */
    onError: (callback: (event: CustomEvent<UltraErrorEventDetail>) => void): (() => void) => {
        const handler = (e: Event) => callback(e as CustomEvent<UltraErrorEventDetail>);
        document.addEventListener('ultraError', handler);
        return () => document.removeEventListener('ultraError', handler);
    },

    // --- UTILITY METHODS ---

    /**
     * Get error configuration for specific error code
     */
    getErrorConfig: (errorCode: string): ErrorConfig | null => {
        return getErrorConfig(errorCode);
    },

    /**
     * Get user message for error code
     */
    getUserMessage: (errorCode: string): string => {
        const errorConfig = getErrorConfig(errorCode);
        return errorConfig ? resolveUserMessage(errorCode, errorConfig) : `Error: ${errorCode}`;
    },

    /**
     * Get notification type for error code
     */
    getNotificationType: (errorCode: string): string => {
        const errorConfig = getErrorConfig(errorCode);
        return errorConfig?.msg_to || serverConfig?.ui.default_display_mode || 'sweet-alert';
    },

    /**
     * Check if error is blocking
     */
    isBlockingError: (errorCode: string): boolean => {
        const errorConfig = getErrorConfig(errorCode);
        return errorConfig?.blocking === 'blocking';
    },

    /**
     * Get complete server configuration (read-only)
     */
    getServerConfig: (): UEMServerConfig | null => {
        return serverConfig;
    },

    /**
     * Check if UEM is ready to handle errors
     */
    isReady: (): boolean => {
        return isInitialized && serverConfig !== null;
    },

    /**
     * Check if UEM is currently initializing
     */
    isInitializing: (): boolean => {
        return isInitializing;
    }
};

// Backward compatibility export
export const UEM_Client_TS_Placeholder = UEM;
