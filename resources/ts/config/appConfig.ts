// File: resources/ts/config/appConfig.ts

/**
 * 📜 Oracode TypeScript Module: AppConfig
 * Gestisce il caricamento e l'accesso alla configurazione dell'applicazione
 * e ai dati iniziali iniettati da Blade nell'elemento #app-config.
 * Contiene definizioni di tipo cruciali e utility per traduzioni e routing client-side.
 *
 * @version 1.4.0 (Padmin Stabilized with route() and appTranslate())
 * @date 2025-05-11
 * @author Padmin D. Curtis (for Fabio Cherici)
 */

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

export interface AppRoutesApi {
    baseUrl?: string; // Es. https://florenceegi.com/api o solo /api
    accessibleCollections: string; // Path relativo a api.baseUrl o assoluto
    setCurrentCollectionBase: string; // Path relativo o assoluto, con placeholder :id
    // Aggiungere altre rotte API qui asseconda necessità
    // uemConfigEndpoint?: string;
    // checkUploadAuth?: string;
    // walletDisconnect?: string;
}

export interface AppRoutes {
    baseUrl: string; // URL base del sito, es. https://florenceegi.com
    walletConnect: string; // Path relativo a baseUrl o assoluto
    collectionsCreate: string;
    register: string;
    logout: string;
    homeCollectionsIndex: string; // Path per la lista pubblica delle collection
    viewCollectionBase: string; // Path con placeholder :id, es. /home/collections/:id
    editCollectionBase: string; // Path con placeholder :id, es. /collections/:id/edit
    api: AppRoutesApi;
}

export interface AppTranslations {
    [key: string]: string; // Dizionario chiave-valore per le traduzioni
}

export interface AppConfig {
    isAuthenticatedByBackend: boolean;
    loggedInUserWallet: string | null;
    initialUserData: InitialUserData;
    routes: AppRoutes;
    translations: AppTranslations;
}

// --- ⚙️ ISTANZA DI CONFIGURAZIONE ---
let loadedConfig: AppConfig | null = null;

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
        console.log("Padmin Config: Application configuration parsed successfully from #app-config.");
        return parsedConfig;
    } catch (e: any) {
        const parseErrorMessage = 'FATAL: Failed to parse application configuration JSON from #app-config. Invalid JSON structure or content.';
        console.error(parseErrorMessage, { rawJsonContent: configElement.textContent, error: e.message });
        document.body.innerHTML = `<div style="position:fixed; top:0; left:0; width:100%; height:100%; background:white; color:red; padding:40px; font-family:sans-serif; z-index:9999; text-align:center;"><h1>Application Initialization Error</h1><p>${parseErrorMessage}</p><p>Details: ${e.message}</p><p>Please contact support.</p></div>`;
        throw new Error(`${parseErrorMessage} Details: ${e.message}`);
    }
}

/**
 * 📜 Oracode Function: getAppConfig
 * 🎯 Fornisce l'istanza globale della configurazione dell'applicazione.
 * Implementa un pattern singleton "lazy-loaded".
 *
 * 🛡️ Gestione Errori: Propaga errori da `loadAndParseAppConfig`.
 *
 * @export
 * @returns {AppConfig} L'oggetto di configurazione.
 * @throws {Error} Se la configurazione non può essere caricata.
 * @author Padmin D. Curtis (for Fabio Cherici)
 * @version 1.0.0
 */
export function getAppConfig(): AppConfig {
    if (!loadedConfig) {
        loadedConfig = loadAndParseAppConfig();
    }
    return loadedConfig;
}

/**
 * 📜 Oracode Function: appTranslate
 * 🎯 Fornisce una stringa tradotta, con supporto per placeholder.
 *
 * @export
 * @param {string} key Chiave della traduzione.
 * @param {AppTranslations} [translationsObject] Oggetto traduzioni; se omesso, usa `getAppConfig().translations`.
 * @param {{ [placeholder: string]: string | number }} [replacements] Valori per i placeholder.
 * @returns {string} Stringa tradotta o la chiave stessa.
 * @example appTranslate("greeting", config.translations, { name: "Padmin" });
 * @author Padmin D. Curtis (for Fabio Cherici)
 * @version 1.1.0
 */
export function appTranslate(
    key: string,
    translationsObject?: AppTranslations,
    replacements?: { [placeholder: string]: string | number }
): string {
    const effectiveTranslations = translationsObject || getAppConfig()?.translations;
    if (!effectiveTranslations) {
        console.warn(`Padmin Translate: Translations object (either provided or global) not available for key "${key}". Returning key.`);
        return key;
    }
    let translatedString = effectiveTranslations[key];
    if (translatedString === undefined) {
        // console.warn(`Padmin Translate: Translation key "${key}" not found. Returning key.`);
        translatedString = key;
    }
    if (replacements && typeof translatedString === 'string') {
        for (const placeholder in replacements) {
            if (Object.prototype.hasOwnProperty.call(replacements, placeholder)) {
                const regex = new RegExp(`:${placeholder}|\\{${placeholder}\\}`, 'g');
                translatedString = translatedString.replace(regex, String(replacements[placeholder]));
            }
        }
    }
    return translatedString;
}

/**
 * 📜 Oracode Function: route
 * 🎯 Costruisce un URL completo o un path partendo da una chiave di rotta definita
 *    in `AppConfig.routes` e un oggetto opzionale di parametri.
 *    Sostituisce i placeholder nel template di rotta (es. `:id`) con i valori forniti.
 *    Aggiunge il `baseUrl` appropriato (generale o API) se il path risultante non è assoluto.
 *
 * @export
 * @param {keyof AppRoutes | keyof AppRoutesApi | string} routeKey La chiave della rotta come definita
 *        in `AppConfig.routes` o `AppConfig.routes.api` (es. 'editCollectionBase', 'accessibleCollections').
 *        Può anche essere un path diretto se non si usa una chiave e inizia con '/' o 'http'.
 * @param {{ [key: string]: string | number }} [params] Un oggetto opzionale contenente
 *        i valori per i placeholder nella rotta (es. `{ id: 123 }`).
 * @returns {string} L'URL costruito e completo. Se la chiave non è trovata e non è un path valido,
 *          restituisce un path di fallback basato sulla chiave.
 *
 * @example
 *  // Assumendo config.routes.editCollectionBase = '/collections/:id/edit'
 *  // e config.routes.baseUrl = 'https://florenceegi.com'
 *  route('editCollectionBase', { id: 42 });
 *  // -> "https://florenceegi.com/collections/42/edit"
 *
 *  // Assumendo config.routes.api.accessibleCollections = '/api/user/accessible-collections'
 *  route('accessibleCollections');
 *  // -> "https://florenceegi.com/api/user/accessible-collections" (se api.baseUrl eredita da routes.baseUrl)
 *
 * @author Padmin D. Curtis (for Fabio Cherici)
 * @version 1.0.0
 */
export function route(
    routeKey: keyof AppRoutes | keyof AppRoutesApi | string,
    params?: { [key: string]: string | number }
): string {
    const config = getAppConfig(); // Assicura che la configurazione sia caricata
    let pathTemplate: string;
    let baseForRoute: string = config.routes.baseUrl; // Default base URL

    // Determina il template del path e il baseUrl corretto
    if (typeof routeKey === 'string' && routeKey in config.routes) {
        pathTemplate = (config.routes as any)[routeKey] as string;
    } else if (typeof routeKey === 'string' && config.routes.api && routeKey in config.routes.api) {
        pathTemplate = (config.routes.api as any)[routeKey] as string;
        baseForRoute = config.routes.api.baseUrl || config.routes.baseUrl;
    } else if (typeof routeKey === 'string' && (routeKey.startsWith('/') || routeKey.startsWith('http'))) {
        // Se routeKey è già un path relativo che inizia con / o un URL completo
        pathTemplate = routeKey;
        // In questo caso, non anteponiamo baseUrl se è già un URL completo
        if (pathTemplate.startsWith('http')) {
            baseForRoute = ''; // Non serve baseUrl
        }
    } else {
        console.warn(`Padmin Route: Route key "${String(routeKey)}" not found in appConfig.routes. Returning fallback path.`);
        // Fallback a un path relativo basato sulla chiave, assicurandosi che inizi con /
        const fallbackPath = String(routeKey).startsWith('/') ? String(routeKey) : `/${String(routeKey)}`;
        // Non possiamo costruire un URL completo affidabile qui, quindi restituiamo solo il path relativo
        // o la chiave stessa se non si vuole un path di fallback. Per ora, path relativo.
        return fallbackPath;
    }

    let populatedPath = pathTemplate;
    if (params) {
        for (const key in params) {
            if (Object.prototype.hasOwnProperty.call(params, key)) {
                const regex = new RegExp(`:${key}|\\{${key}\\}`, 'g');
                populatedPath = populatedPath.replace(regex, String(params[key]));
            }
        }
    }

    // Aggiungi il baseForRoute solo se populatedPath non è già un URL completo
    // e baseForRoute non è vuoto (caso in cui pathTemplate era già un URL completo)
    if (baseForRoute && !populatedPath.startsWith('http') && !/^[a-z]+:/i.test(populatedPath)) {
        const cleanBase = baseForRoute.endsWith('/') ? baseForRoute.slice(0, -1) : baseForRoute;
        const cleanPath = populatedPath.startsWith('/') ? populatedPath.slice(1) : populatedPath;
        return `${cleanBase}/${cleanPath}`;
    }

    return populatedPath; // Restituisce il path (che potrebbe essere già un URL completo)
}