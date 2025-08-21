// File: resources/ts/config/appConfig.ts

/**
 * 📜 Oracode TypeScript Module: AppConfig
 * Gestisce il caricamento e l'accesso alla configurazione dell'applicazione
 * e ai dati iniziali iniettati da Blade nell'elemento #app-config.
 * Contiene definizioni di tipo cruciali e utility per traduzioni e routing client-side.
 *
 * @version 2.0.0 (Async API Implementation)
 * @date 2025-05-13
 * @author Padmin D. Curtis (for Fabio Cherici)
 */

import { UEM_Client_TS_Placeholder as UEM } from '../services/uemClientService';

// --- 💎 INTERFACCE DI CONFIGURAZIONE ---

export interface OwnedCollection {
    id: number;
    collection_name: string;
}

export interface CollaboratingCollection {
    id: number;
    collection_name: string;
    creator_email: string;
}

export interface UserAccessibleCollections {
    owned_collections: OwnedCollection[];
    collaborating_collections: CollaboratingCollection[];
}

export interface CurrentCollectionDetails {
    id: number | null;
    name: string | null;
    can_edit: boolean;
}

export interface ServerErrorResponse {
    error: string;
    message: string;
    blocking?: string;
    display_mode?: string;
    [key: string]: any;
}

export interface InitialUserData {
    current_collection_id: number | null;
    current_collection_name: string | null;
    can_edit_current_collection: boolean;
}

export interface AppSettings {
    allowedExtensions: string[];
    allowedMimeTypes: string[];
    maxFileSize: number;
    egiSettings: {
        minPrice: number;
        maxPrice: number;
        commissionRate: number;
    };
    features: {
        walletSecretEnabled: boolean;
        auctionsEnabled: boolean;
        reservationsEnabled: boolean;
    };
}

export interface AppRoutesApi {
    baseUrl?: string; // Es. https://florenceegi.com/api o solo /api
    accessibleCollections: string; // Path relativo a api.baseUrl o assoluto
    setCurrentCollectionBase: string; // Path relativo o assoluto, con placeholder :id
    checkUploadAuth: string;
    appConfig: string;
    errorDefinitions: string;
    // Aggiungere altre rotte API qui asseconda necessità
    // uemConfigEndpoint?: string;
    // checkUploadAuth?: string;
    // walletDisconnect?: string;
    [key: string]: string | undefined; // Allow string indexing for dynamic routes
}

export interface AppRoutes {
    baseUrl: string; // URL base del sito, es. https://florenceegi.com
    walletConnect: string; // Path relativo a baseUrl o assoluto
    walletDisconnect: string; // Path per disconnessione wallet
    collectionsCreate: string;
    register: string;
    logout: string;
    homeCollectionsIndex: string; // Path per la lista pubblica delle collection
    viewCollectionBase: string; // Path con placeholder :id, es. /home/collections/:id
    editCollectionBase: string; // Path con placeholder :id, es. /collections/:id/edit
    api: AppRoutesApi;
    [key: string]: string | AppRoutesApi | any; // Support for dynamic property access
}

export interface AppTranslations {
    [key: string]: string; // Dizionario chiave-valore per le traduzioni
}

export interface AppConfig {
    isAuthenticated: boolean;
    isWeakAuth: boolean;
    loggedInUserWallet: string | null;
    initialUserData: InitialUserData;
    routes: AppRoutes;
    translations: AppTranslations;
    appSettings: AppSettings;
    locale: string;
    availableLocales: string[];
    csrf_token: string;
    env: 'production' | 'development';
}

// --- ⚙️ ISTANZA DI CONFIGURAZIONE ---
let loadedConfig: AppConfig | null = null;
let loadingPromise: Promise<AppConfig> | null = null;

/**
 * 📜 Oracode Function: loadAppConfigFromAPI
 * 🎯 Carica la configurazione dal nuovo endpoint unificato
 *
 * @returns {Promise<AppConfig>} Promise che risolve con la configurazione
 * @throws {Error} Se il caricamento fallisce
 */
async function loadAppConfigFromAPI(): Promise<AppConfig> {
    const response = await fetch('/api/app-config', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content || ''
        }
    });

    if (!response.ok) {
        const errorMsg = `Failed to load app configuration: ${response.status} ${response.statusText}`;
        console.error(errorMsg);

        // Usa UEM per gestire l'errore se disponibile
        if (UEM && typeof UEM.handleClientError === 'function') {
            UEM.handleClientError('APP_CONFIG_LOAD_FAILED', {
                status: response.status,
                statusText: response.statusText
            });
        }

        throw new Error(errorMsg);
    }

    // --- LA CORREZIONE È QUI ---loadAppConfigFromAPI
    // 1. Riceviamo la risposta completa (l'involucro)
    const fullResponse = await response.json();

    // 2. Estraiamo l'oggetto AppConfig interno e lo tipizziamo correttamente
    const config = fullResponse.AppConfig as AppConfig;
    // -------------------------

    //console.log('Padmin Config: Application configuration loaded and extracted successfully.');
    return config; // Ora restituiamo l'oggetto con la forma corretta
}

/**
 * 📜 Oracode Function: initializeAppConfig
 * 🎯 Inizializza la configurazione dell'applicazione (chiamata una sola volta)
 *
 * @export
 * @returns {Promise<AppConfig>} Promise che risolve con la configurazione
 */
export async function initializeAppConfig(): Promise<AppConfig> {
    if (loadedConfig) {
        return loadedConfig;
    }

    if (loadingPromise) {
        return loadingPromise;
    }

    loadingPromise = loadAppConfigFromAPI()
        .then(config => {
            loadedConfig = config;
            loadingPromise = null;
            return config;
        })
        .catch(error => {
            loadingPromise = null;
            throw error;
        });

    return loadingPromise;
}

/**
 * 📜 Oracode Function: getAppConfig
 * 🎯 Ottiene la configurazione già caricata (sincrono)
 *
 * @export
 * @returns {AppConfig} La configurazione caricata
 * @throws {Error} Se la configurazione non è stata ancora inizializzata
 */
export function getAppConfig(): AppConfig {
    if (!loadedConfig) {
        throw new Error('App configuration not initialized. Call initializeAppConfig() first.');
    }
    //// console.log('Padmin Config: translations called with key:', 'getAppConfig');
    return loadedConfig;
}

/**
 * 📜 Oracode Function: appTranslate
 * 🎯 Purpose: Translates a key using the loaded application configuration
 * 🧱 Core Logic: Retrieves translation string and replaces placeholders with provided values
 * 🛡️ Backward Compatibility: Supports both legacy and modern function signatures
 *
 * @export
 * @param {string} key - Translation key to look up in config.translations
 * @param {any} [secondParam] - Either AppTranslations object (legacy) or replacements object
 * @param {any} [thirdParam] - Replacements object when using legacy signature
 * @returns {string} Translated string with placeholders replaced, or key if translation not found
 *
 * @oracular-trait Supports dual signature for backward compatibility
 * @error-boundary Returns untranslated key if config not loaded or translation missing
 * @tolerance-trait Gracefully handles undefined config or invalid parameters
 *
 * @example
 * // Modern signature (preferred)
 * appTranslate('errorMessage', { count: 5, name: 'John' })
 *
 * @example
 * // Legacy signature (backward compatibility)
 * appTranslate('errorMessage', config.translations, { count: 5, name: 'John' })
 *
 * @why-dual-signature
 * During migration, codebase contains calls with both signatures:
 * - Legacy: appTranslate(key, config.translations, replacements)
 * - Modern: appTranslate(key, replacements)
 * This function adapts to either pattern, avoiding mass refactoring
 *
 * @implementation-notes
 * - Uses simple equality check to detect config.translations parameter
 * - Assumes second param is replacements if not config.translations
 * - Supports both :placeholder and {placeholder} syntax in translations
 * - Falls back to key string if translation lookup fails
 *
 * @future-migration
 * Once all legacy calls are updated, remove thirdParam and simplify logic
 *
 * @version 2.1.0 - Added backward compatibility
 * @date 2025-05-13
 * @author Padmin D. Curtis for Fabio Cherici
 * @oracode-signature [appTranslate::v2.1] adaptive-signature-handler
 */
export function appTranslate(
    key: string,
    secondParam?: any,
    thirdParam?: any
): string {
    try {
        const config = getAppConfig();

        const translations = config.translations;

        // The second parameter is replacements IF it's not config.translations
        // This check enables backward compatibility with legacy signature
        const replacements = (secondParam === config.translations) ? thirdParam : secondParam;

        let translatedString = translations[key] || key;

        if (replacements && typeof translatedString === 'string') {
            for (const placeholder in replacements) {
                if (Object.prototype.hasOwnProperty.call(replacements, placeholder)) {
                    // Support both :placeholder and {placeholder} syntax
                    const regex = new RegExp(`:${placeholder}|\\{${placeholder}\\}`, 'g');
                    translatedString = translatedString.replace(regex, String(replacements[placeholder]));
                }
            }
        }

        //// console.log('Padmin Config: translations called with key:', translatedString);

        return translatedString;
    } catch (error) {
        console.warn(`Translation failed for key "${key}". Config not loaded?`);
        return key; // Fallback to key for debugging
    }
}

/**
 * 📜 Oracode Function: route
 * 🎯 Costruisce un URL dalle route configurate
 *
 * @export
 * @param {string} routeKey Chiave della route
 * @param {{[key: string]: string | number}} [params] Parametri per placeholder
 * @returns {string} URL costruito
 */
export function route(
    routeKey: string,
    params?: { [key: string]: string | number }
): string {
    try {
        const config = getAppConfig();

        // Mappa di conversione dei nomi delle route da stile TypeScript a stile PHP
        const routeKeyMap: Record<string, string> = {
            // Mappa route API
            'api.egis.reservation-status': 'egiReservationStatus',
            'api.egis.reserve': 'egisReserve',
            'api.reservations.cancel': 'reservationsCancel',
            'api.my-reservations': 'myReservations',
            'api.toggle.collection.like': 'toggleCollectionLike',
            'api.toggle.egi.like': 'toggleEgiLike',
            'api.currency.algo-exchange-rate': 'currencyAlgoExchangeRate',

            // Aggiungi altre mappature qui secondo necessità
        };

        // Se è una route API, cerchiamo prima nella mappa, poi nell'oggetto config.routes.api
        if (routeKey.startsWith('api.')) {
            const apiSubKey = routeKeyMap[routeKey] || routeKey.substring(4); // Rimuovi 'api.'

            if (config.routes.api && config.routes.api[apiSubKey]) {
                let pathTemplate = config.routes.api[apiSubKey];

                // Sostituisci i parametri
                if (params) {
                    for (const key in params) {
                        if (Object.prototype.hasOwnProperty.call(params, key)) {
                            // Mappa dei nomi dei parametri (TypeScript -> PHP)
                            const paramMap: Record<string, string> = {
                                'egi': 'egiId',      // usato come api.egis.reservation-status con {egi: 123}
                                'id': 'id',          // generico
                                // Aggiungi altre mappature dei parametri qui
                            };

                            const paramKey = paramMap[key] || key;

                            // Cerca sia :paramKey che {paramKey}
                            const regex1 = new RegExp(`:${paramKey}`, 'g');
                            const regex2 = new RegExp(`\\{${paramKey}\\}`, 'g');

                            pathTemplate = pathTemplate
                                .replace(regex1, String(params[key]))
                                .replace(regex2, String(params[key]));
                        }
                    }
                }

                return pathTemplate;
            } else {
                console.warn(`API route "${routeKey}" not found in config.routes.api`);

                // Fallback: Costruisci un URL basico
                const urlParts = routeKey.split('.');
                urlParts.shift(); // Rimuovi 'api'

                let path = urlParts.join('/');

                // Sostituisci parametri nel percorso di fallback
                if (params) {
                    for (const key in params) {
                        if (Object.prototype.hasOwnProperty.call(params, key)) {
                            path = path.replace(`:${key}`, String(params[key]));
                        }
                    }
                }

                return `/api/${path}`;
            }
        } else {
            // Route non-API (web)
            const routeParts = routeKey.split('.');
            let currentPath: any = config.routes;

            for (const part of routeParts) {
                if (currentPath && typeof currentPath === 'object' && part in currentPath) {
                    currentPath = currentPath[part];
                } else {
                    console.warn(`Route key "${routeKey}" not found in config.routes`);
                    return `/${routeKey.replace(/\./g, '/')}`;
                }
            }

            if (typeof currentPath !== 'string') {
                console.warn(`Route key "${routeKey}" did not resolve to a string`);
                return `/${routeKey.replace(/\./g, '/')}`;
            }

            // Sostituisci parametri
            let pathTemplate = currentPath;

            if (params) {
                for (const key in params) {
                    if (Object.prototype.hasOwnProperty.call(params, key)) {
                        const regex1 = new RegExp(`:${key}`, 'g');
                        const regex2 = new RegExp(`\\{${key}\\}`, 'g');

                        pathTemplate = pathTemplate
                            .replace(regex1, String(params[key]))
                            .replace(regex2, String(params[key]));
                    }
                }
            }

            return pathTemplate;
        }

    } catch (error) {
        console.error(`Route generation failed for key "${routeKey}"`, error);
        return `/${routeKey.replace(/\./g, '/')}`;
    }
}


/**
 * @private
 * 📜 Oracode Function: loadAndParseAppConfig
 * 🎯 Carica e parsa la configurazione JSON dall'elemento DOM `#app-config`.
 * Valida la presenza di proprietà essenziali e normalizza i `baseUrl`.
 *
 * 🛡️ Gestione Errori: Lancia un errore fatale se la configurazione è mancante,
 *    vuota, invalida, o se il parsing JSON fallisce. Modifica il `document.body`
 *    per visualizzare un messaggio di errore critico all'utente.
 *
 * @throws {Error} Se la configurazione non può essere inizializzata.
 * @returns {AppConfig} L'oggetto di configurazione dell'applicazione.
 * @author Padmin D. Curtis (for Fabio Cherici)
 * @version 1.1.0
 */
function loadAndParseAppConfig(): AppConfig {
    const configElement = document.getElementById('app-config');
    if (!configElement || !configElement.textContent || configElement.textContent.trim() === '') {
        const fatalErrorMessage = 'FATAL: Application configuration source (#app-config) not found, empty, or invalid. FlorenceEGI client cannot initialize.';
        console.error(fatalErrorMessage, { elementExists: !!configElement, textContent: configElement?.textContent });
        document.body.innerHTML = `<div style="position:fixed; top:0; left:0; width:100%; height:100%; background:white; color:red; padding:40px; font-family:sans-serif; z-index:9999; text-align:center;"><h1>Application Initialization Error</h1><p>${fatalErrorMessage}</p><p>Please contact support or try again later.</p></div>`;
        throw new Error(fatalErrorMessage);
    }
    try {
        const parsedConfig = JSON.parse(configElement.textContent) as AppConfig;
        if (!parsedConfig || typeof parsedConfig !== 'object' || !parsedConfig.routes?.api || !parsedConfig.translations || parsedConfig.initialUserData === undefined) {
            console.error("Parsed configuration is missing essential properties.", parsedConfig);
            throw new Error("Parsed configuration is missing essential properties (e.g., routes.api, translations, or initialUserData).");
        }
        if (!parsedConfig.routes.baseUrl) {
            parsedConfig.routes.baseUrl = window.location.origin;
            console.warn("Padmin Config: `routes.baseUrl` not found in #app-config, defaulting to `window.location.origin`.");
        }
        if (!parsedConfig.routes.api.baseUrl) {
            parsedConfig.routes.api.baseUrl = parsedConfig.routes.baseUrl;
            console.warn("Padmin Config: `routes.api.baseUrl` not found in #app-config, defaulting to main `routes.baseUrl` for API calls. Adjust if API has a different base.");
        }
        //console.log("Padmin Config: Application configuration parsed successfully from #app-config.");
        return parsedConfig;
    } catch (e: any) {
        const parseErrorMessage = 'FATAL: Failed to parse application configuration JSON from #app-config. Invalid JSON structure or content.';
        console.error(parseErrorMessage, { rawJsonContent: configElement.textContent, error: e.message });
        document.body.innerHTML = `<div style="position:fixed; top:0; left:0; width:100%; height:100%; background:white; color:red; padding:40px; font-family:sans-serif; z-index:9999; text-align:center;"><h1>Application Initialization Error</h1><p>${parseErrorMessage}</p><p>Details: ${e.message}</p><p>Please contact support.</p></div>`;
        throw new Error(`${parseErrorMessage} Details: ${e.message}`);
    }
}

