// File: resources/ts/features/collections/collectionUI.ts

/**
 * 📜 Oracode TypeScript Module: CollectionUIManager
 * Gestisce l'interfaccia utente per il dropdown "My Galleries" (o come tradotto)
 * e il badge della collection corrente. Include il caricamento dei dati delle collection,
 * il rendering del menu dropdown, la gestione della selezione della collection corrente,
 * l'aggiornamento del badge e il reset dello stato al logout.
 *
 * @version 1.0.1 (Padmin Corrected Translation Calls, No Placeholders, Refined Logic)
 * @date 2025-05-11
 * @author Padmin D. Curtis (for Fabio Cherici)
 */

import { AppConfig, UserAccessibleCollections, CurrentCollectionDetails, OwnedCollection, CollaboratingCollection, appTranslate } from '../../config/appConfig';
import * as DOMElements from '../../dom/domElements';
import { UEM_Client_TS_Placeholder as UEM } from '../../services/uemClientService';
import { fetchUserAccessibleCollectionsAPI, setCurrentUserCollectionAPI } from './collectionService';
import { closeWalletDropdownMenu } from '../auth/walletDropdown'; // Per chiudere l'altro dropdown

// --- STATO INTERNO DEL MODULO ---
let currentUserCollectionsDataState: UserAccessibleCollections | null = null;
let currentCollectionDetailsState: CurrentCollectionDetails = { id: null, name: null, can_edit: false };
let isCollectionListDropdownOpenState: boolean = false;
let hasInitializedCollectionStateOnceFlag: boolean = false;

let collectionListOutsideClickHandlerInstance: ((event: MouseEvent) => void) | null = null;
let collectionListKeydownHandlerInstance: ((event: KeyboardEvent) => void) | null = null;

// --- FUNZIONI HELPER INTERNE ---

/**
 * @private
 * Controlla e restituisce gli elementi DOM necessari per il dropdown delle collection.
 */
function getRequiredCollectionListDOMElements(
    DOM: typeof DOMElements,
    uem: typeof UEM,
    config: AppConfig
): { collectionListDropdownMenuEl: HTMLDivElement, collectionListDropdownButtonEl: HTMLButtonElement, collectionListLoadingEl: HTMLDivElement, collectionListEmptyEl: HTMLDivElement, collectionListErrorEl: HTMLDivElement } | null {
    const { collectionListDropdownMenuEl, collectionListDropdownButtonEl, collectionListLoadingEl, collectionListEmptyEl, collectionListErrorEl } = DOM;
    if (!collectionListDropdownMenuEl || !collectionListDropdownButtonEl || !collectionListLoadingEl || !collectionListEmptyEl || !collectionListErrorEl) {
        uem.handleClientError('CLIENT_DOM_MISSING_COLLECTION_DROPDOWN_CORE', {}, undefined, appTranslate('errorGalleriesListUIDOM', config.translations));
        return null;
    }
    return { collectionListDropdownMenuEl, collectionListDropdownButtonEl, collectionListLoadingEl, collectionListEmptyEl, collectionListErrorEl };
}

/**
 * @private
 * 🎯 Recupera le collection accessibili dall'API e aggiorna il dropdown.
 */
async function _fetchAndRenderAccessibleCollections(config: AppConfig, DOM: typeof DOMElements, uem: typeof UEM): Promise<void> {
    const elements = getRequiredCollectionListDOMElements(DOM, uem, config);
    if (!elements) return;
    const { collectionListLoadingEl, collectionListDropdownMenuEl, collectionListEmptyEl, collectionListErrorEl } = elements;

    collectionListLoadingEl.classList.remove('hidden');
    collectionListEmptyEl.classList.add('hidden');
    collectionListErrorEl.classList.add('hidden');
    Array.from(collectionListDropdownMenuEl.querySelectorAll('.collection-list-item, .collection-list-header, .collection-list-separator')).forEach(el => el.remove());

    const data = await fetchUserAccessibleCollectionsAPI(config);
    if (data) {
        currentUserCollectionsDataState = data;
        _renderCollectionListMenu(config, DOM, uem);
    } else {
        // L'errore API è già gestito da fetchUserAccessibleCollectionsAPI tramite UEM,
        // qui mostriamo solo un messaggio specifico nel contesto del dropdown.
        collectionListErrorEl.textContent = appTranslate('errorLoadingGalleries', config.translations);
        collectionListErrorEl.classList.remove('hidden');
        currentUserCollectionsDataState = null;
    }
    collectionListLoadingEl.classList.add('hidden');
}

/**
 * @private
 * 🎨 Renderizza il menu del dropdown "My Galleries" con i dati caricati.
 */
function _renderCollectionListMenu(config: AppConfig, DOM: typeof DOMElements, uem: typeof UEM): void {
    const elements = getRequiredCollectionListDOMElements(DOM, uem, config);
    if (!elements) return;
    const { collectionListDropdownMenuEl, collectionListEmptyEl, collectionListErrorEl, collectionListLoadingEl } = elements;

    // Pulisci nuovamente per sicurezza, anche se _fetchAndRender lo fa già
    Array.from(collectionListDropdownMenuEl.querySelectorAll('.collection-list-item, .collection-list-header, .collection-list-separator')).forEach(el => el.remove());

    if (!currentUserCollectionsDataState || (currentUserCollectionsDataState.owned_collections.length === 0 && currentUserCollectionsDataState.collaborating_collections.length === 0)) {
        collectionListEmptyEl.classList.remove('hidden');
        collectionListLoadingEl.classList.add('hidden');
        collectionListErrorEl.classList.add('hidden');
        return;
    }
    collectionListEmptyEl.classList.add('hidden');

    const createItem = (collection: OwnedCollection | CollaboratingCollection, isOwned: boolean): HTMLAnchorElement => {
        const item = document.createElement('a');
        item.href = '#'; // L'azione è gestita dal click, href="#" previene il default
        item.className = 'collection-list-item block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-200';
        item.setAttribute('role', 'menuitem');
        item.tabIndex = -1; // Rende l'elemento focusabile programmaticamente ma non con Tab normale
        item.dataset.collectionId = collection.id.toString();

        const nameSpan = document.createElement('span');
        nameSpan.textContent = collection.collection_name;
        item.appendChild(nameSpan);

        if (!isOwned && 'creator_email' in collection) {
            const creatorSpan = document.createElement('span');
            creatorSpan.className = 'text-xs text-gray-400 ml-1';
            creatorSpan.textContent = appTranslate('byCreator', config.translations, { creator: collection.creator_email });
            item.appendChild(creatorSpan);
        }
        item.addEventListener('click', (e) => {
            e.preventDefault();
            _handleSetCurrentCollection(config, DOM, collection.id, uem);
        });
        return item;
    };

    const createHeader = (translationKey: string): HTMLDivElement => {
        const header = document.createElement('div');
        header.className = 'collection-list-header block px-4 pt-2 pb-1 text-xs text-gray-500 uppercase tracking-wider';
        header.setAttribute('role', 'none');
        header.textContent = appTranslate(translationKey, config.translations);
        return header;
    };

    if (currentUserCollectionsDataState.owned_collections.length > 0) {
        collectionListDropdownMenuEl.appendChild(createHeader('myGalleriesOwned')); // Assicurati che 'myGalleriesOwned' sia in config.translations
        currentUserCollectionsDataState.owned_collections.forEach(c => collectionListDropdownMenuEl!.appendChild(createItem(c, true)));
    }

    if (currentUserCollectionsDataState.collaborating_collections.length > 0) {
        if (currentUserCollectionsDataState.owned_collections.length > 0) {
            const separator = document.createElement('div');
            separator.className = 'collection-list-separator my-1 border-t border-gray-200';
            separator.setAttribute('role', 'separator');
            collectionListDropdownMenuEl.appendChild(separator);
        }
        collectionListDropdownMenuEl.appendChild(createHeader('myGalleriesCollaborations')); // Assicurati che 'myGalleriesCollaborations' sia in config.translations
        currentUserCollectionsDataState.collaborating_collections.forEach(c => collectionListDropdownMenuEl!.appendChild(createItem(c, false)));
    }

    collectionListLoadingEl.classList.add('hidden');
    collectionListErrorEl.classList.add('hidden');
}

/**
 * @private
 * 🚀 Gestisce il click su un item del dropdown per impostare la collection corrente.
 */
async function _handleSetCurrentCollection(config: AppConfig, DOM: typeof DOMElements, collectionId: number, uem: typeof UEM): Promise<void> {
    const elements = getRequiredCollectionListDOMElements(DOM, uem, config);
    if (!elements) return;
    const { collectionListDropdownButtonEl } = elements;

    const originalButtonHTML = collectionListDropdownButtonEl.innerHTML;
    collectionListDropdownButtonEl.disabled = true;
    collectionListDropdownButtonEl.innerHTML = `<span class="animate-pulse">${appTranslate('switchingGallery', config.translations)}</span>`;

    const newDetails = await setCurrentUserCollectionAPI(config, collectionId);
    if (newDetails) {
        currentCollectionDetailsState = newDetails;
        updateCurrentCollectionBadge(config, DOM);
        closeCollectionListDropdown(config, DOM, uem);

        if (window.Swal) {
            window.Swal.fire({
                icon: 'success',
                title: appTranslate('gallerySwitchedTitle', config.translations),
                text: appTranslate('gallerySwitchedText', config.translations, { galleryName: newDetails.name }),
                timer: 2500,
                timerProgressBar: true,
                showConfirmButton: false,
            }).then(() => { window.location.reload(); });
        } else {
            alert(appTranslate('gallerySwitchedText', config.translations, { galleryName: newDetails.name }) + " " + appTranslate('pageWillReload', config.translations));
            window.location.reload();
        }
    } else {
        // L'errore è già stato gestito da setCurrentUserCollectionAPI via UEM
        collectionListDropdownButtonEl.innerHTML = originalButtonHTML; // Ripristina testo bottone
    }
    collectionListDropdownButtonEl.disabled = false;
}


/**
 * @private
 * Gestisce i click fuori dal dropdown delle collection per chiuderlo.
 */
function _handleOutsideCollectionListDropdownClick(event: MouseEvent, DOM: typeof DOMElements, config: AppConfig, uem: typeof UEM): void {
    const target = event.target as Node;
    const elements = getRequiredCollectionListDOMElements(DOM, uem, config);
    if (!elements) return;

    if (!elements.collectionListDropdownButtonEl.contains(target) && !elements.collectionListDropdownMenuEl.contains(target)) {
        closeCollectionListDropdown(config, DOM, uem);
    }
}

// --- FUNZIONI ESPORTATE ---

/**
 * 📜 Oracode Function: initializeUserCollectionState
 * 🎯 Inizializza lo stato delle collection per l'utente loggato.
 * Carica i dati iniziali della collection corrente da `AppConfig` e poi
 * recupera la lista completa delle collection accessibili per popolare il dropdown.
 * Aggiorna il badge della collection corrente.
 *
 * @export
 * @param {AppConfig} config L'oggetto di configurazione dell'applicazione.
 * @param {typeof DOMElements} DOM Collezione dei riferimenti agli elementi DOM.
 * @param {typeof UEM} uem Istanza del gestore errori UEM.
 */
export async function initializeUserCollectionState(config: AppConfig, DOM: typeof DOMElements, uem: typeof UEM): Promise<void> {
    currentCollectionDetailsState = {
        id: config.initialUserData.current_collection_id,
        name: config.initialUserData.current_collection_name,
        can_edit: config.initialUserData.can_edit_current_collection,
    };

    if (hasInitializedCollectionStateOnceFlag && currentUserCollectionsDataState) {
        updateCurrentCollectionBadge(config, DOM);
        return;
    }
    // console.log('Padmin CollectionUI: Initializing user collection state (dropdown & badge)...');
    await _fetchAndRenderAccessibleCollections(config, DOM, uem);
    updateCurrentCollectionBadge(config, DOM);
    hasInitializedCollectionStateOnceFlag = true;
}

/**
 * 📜 Oracode Function: updateCurrentCollectionBadge
 * 💅 Aggiorna il badge della collection corrente visualizzato nella navbar.
 * Mostra il nome della collection e configura il link (edit o view) in base ai permessi.
 *
 * @export
 * @param {AppConfig} config L'oggetto di configurazione dell'applicazione.
 * @param {typeof DOMElements} DOM Collezione dei riferimenti agli elementi DOM.
 */
export function updateCurrentCollectionBadge(config: AppConfig, DOM: typeof DOMElements): void {
    const { currentCollectionBadgeContainerEl, currentCollectionBadgeNameEl, currentCollectionBadgeLinkEl } = DOM;
    if (!currentCollectionBadgeContainerEl || !currentCollectionBadgeNameEl || !currentCollectionBadgeLinkEl) {
        // console.warn("Padmin CollectionUI: Current collection badge DOM elements not fully available for update.");
        return;
    }
    const { id, name, can_edit } = currentCollectionDetailsState;

    if (id && name) {
        currentCollectionBadgeNameEl.textContent = name;
        const viewUrl = appTranslate(config.routes.viewCollectionBase, config.translations, { id: id.toString() }); // Usa appTranslate per le rotte se necessario per baseUrl
        const editUrl = appTranslate(config.routes.editCollectionBase, config.translations, { id: id.toString() });

        if (can_edit) {
            currentCollectionBadgeLinkEl.href = editUrl; // Dovrebbe essere già un URL completo da appConfig/route helper
            currentCollectionBadgeLinkEl.title = appTranslate('editCurrentGalleryTitle', config.translations, { galleryName: name });
            currentCollectionBadgeLinkEl.classList.remove('pointer-events-none', 'opacity-60', 'cursor-default');
            currentCollectionBadgeLinkEl.classList.add('hover:bg-sky-100', 'hover:border-sky-400');
        } else {
            currentCollectionBadgeLinkEl.href = viewUrl;
            currentCollectionBadgeLinkEl.title = appTranslate('viewCurrentGalleryTitle', config.translations, { galleryName: name });
            currentCollectionBadgeLinkEl.classList.add('opacity-75'); // Meno prominente ma cliccabile per vedere
            currentCollectionBadgeLinkEl.classList.remove('pointer-events-none', 'cursor-default', 'hover:bg-sky-100', 'hover:border-sky-400');
        }
        currentCollectionBadgeContainerEl.classList.remove('hidden');
    } else {
        currentCollectionBadgeContainerEl.classList.add('hidden');
    }
}

/**
 * 📜 Oracode Function: closeCollectionListDropdown
 * 🎯 Chiude il menu dropdown "My Galleries".
 * Rimuove i listener di eventi globali associati.
 *
 * @export
 * @param {AppConfig} config L'oggetto di configurazione dell'applicazione.
 * @param {typeof DOMElements} DOM Collezione dei riferimenti agli elementi DOM.
 * @param {typeof UEM} uem Istanza del gestore errori UEM.
 */
export function closeCollectionListDropdown(config: AppConfig, DOM: typeof DOMElements, uem: typeof UEM): void {
    const elements = getRequiredCollectionListDOMElements(DOM, uem, config);
    if (!elements || elements.collectionListDropdownMenuEl.classList.contains('hidden')) return;

    elements.collectionListDropdownMenuEl.classList.add('hidden');
    elements.collectionListDropdownButtonEl.setAttribute('aria-expanded', 'false');

    if (collectionListOutsideClickHandlerInstance) {
        document.removeEventListener('click', collectionListOutsideClickHandlerInstance);
        collectionListOutsideClickHandlerInstance = null;
    }
    if (collectionListKeydownHandlerInstance && elements.collectionListDropdownMenuEl) {
        elements.collectionListDropdownMenuEl.removeEventListener('keydown', collectionListKeydownHandlerInstance);
        collectionListKeydownHandlerInstance = null;
    }
    isCollectionListDropdownOpenState = false;
    // console.log('Padmin CollectionUI: Dropdown closed.');
}

/**
 * 📜 Oracode Function: handleCollectionListDropdownKeydown
 * 🎯 Gestisce la pressione di tasti (Escape) nel dropdown "My Galleries".
 * @param {KeyboardEvent} event L'evento keydown.
 * @param {typeof DOMElements} DOM Riferimenti agli elementi DOM.
 * @param {AppConfig} config Configurazione dell'applicazione.
 * @param {typeof UEM} uem Istanza del gestore errori UEM.
 */
export function handleCollectionListDropdownKeydown(event: KeyboardEvent, DOM: typeof DOMElements, config: AppConfig, uem: typeof UEM): void {
    const elements = getRequiredCollectionListDOMElements(DOM, uem, config);
    if (!elements) return;
    if (event.key === 'Escape') {
        closeCollectionListDropdown(config, DOM, uem);
        if (elements.collectionListDropdownButtonEl) elements.collectionListDropdownButtonEl.focus();
    }
    // TODO: [PADMIN_ACCESSIBILITY] Implementare navigazione con frecce UP/DOWN tra gli item del menu.
}

/**
 * 📜 Oracode Function: toggleCollectionListDropdown
 * 🎯 Apre o chiude il menu dropdown "My Galleries".
 * Carica i dati delle collection se non ancora fatto.
 * Assicura che il dropdown del wallet sia chiuso.
 *
 * @export
 * @param {AppConfig} config L'oggetto di configurazione dell'applicazione.
 * @param {typeof DOMElements} DOM Collezione dei riferimenti agli elementi DOM.
 * @param {typeof UEM} uem Istanza del gestore errori UEM.
 */
export function toggleCollectionListDropdown(config: AppConfig, DOM: typeof DOMElements, uem: typeof UEM): void {
    const elements = getRequiredCollectionListDOMElements(DOM, uem, config);
    if (!elements) return;

    if (isCollectionListDropdownOpenState) {
        closeCollectionListDropdown(config, DOM, uem);
    } else {
        closeWalletDropdownMenu(DOM); // Da walletDropdown.ts - la sua firma deve essere (DOM: typeof DOMElements)

        if (!currentUserCollectionsDataState && DOM.collectionListLoadingEl && !DOM.collectionListLoadingEl.classList.contains('hidden')) {
            // Già in caricamento, non fare nulla
        } else if (!currentUserCollectionsDataState) {
            _fetchAndRenderAccessibleCollections(config, DOM, uem); // Carica i dati se non presenti
        }
        // Se i dati ci sono già, semplicemente apre il menu
        elements.collectionListDropdownMenuEl.classList.remove('hidden');
        elements.collectionListDropdownButtonEl.setAttribute('aria-expanded', 'true');
        isCollectionListDropdownOpenState = true;

        // Crea e assegna nuove istanze dei gestori per questa apertura
        collectionListOutsideClickHandlerInstance = (e: MouseEvent) => _handleOutsideCollectionListDropdownClick(e, DOM, config, uem);
        collectionListKeydownHandlerInstance = (e: KeyboardEvent) => handleCollectionListDropdownKeydown(e, DOM, config, uem);

        document.addEventListener('click', collectionListOutsideClickHandlerInstance);
        elements.collectionListDropdownMenuEl.addEventListener('keydown', collectionListKeydownHandlerInstance);

        const firstMenuItem = elements.collectionListDropdownMenuEl.querySelector<HTMLAnchorElement>('a[role="menuitem"]');
        if (firstMenuItem) {
            firstMenuItem.focus();
        } else {
            elements.collectionListDropdownMenuEl.focus(); // Fallback
        }
        // console.log('Padmin CollectionUI: Dropdown opened.');
    }
}

/**
 * 📜 Oracode Function: resetCollectionStateOnLogout
 * 🎯 Resetta lo stato UI e i dati relativi alle collection quando l'utente
 * effettua il logout o la sua sessione "logged-in" termina.
 * Pulisce il menu dropdown, nasconde il badge, e resetta i flag di stato.
 *
 * @export
 * @param {typeof DOMElements} DOM Collezione dei riferimenti agli elementi DOM.
 */
export function resetCollectionStateOnLogout(DOM: typeof DOMElements): void {
    currentUserCollectionsDataState = null;
    // Non resettare currentCollectionDetailsState qui, perché AppConfig.initialUserData
    // potrebbe contenere i dati corretti per il prossimo utente o per lo stato guest.
    // Verrà reinizializzato da initializeUserCollectionState al prossimo login.
    isCollectionListDropdownOpenState = false;
    hasInitializedCollectionStateOnceFlag = false;

    const { collectionListDropdownMenuEl, collectionListLoadingEl, collectionListEmptyEl, collectionListErrorEl, currentCollectionBadgeContainerEl, collectionListDropdownButtonEl } = DOM;

    if (collectionListDropdownMenuEl) {
        Array.from(collectionListDropdownMenuEl.querySelectorAll('.collection-list-item, .collection-list-header, .collection-list-separator')).forEach(el => el.remove());
        if (collectionListLoadingEl) {
            // Assicura che lo spinner sia il primo figlio e visibile per la prossima apertura
            if (!collectionListDropdownMenuEl.contains(collectionListLoadingEl)) {
                 collectionListDropdownMenuEl.insertBefore(collectionListLoadingEl, collectionListDropdownMenuEl.firstChild);
            }
            collectionListLoadingEl.classList.remove('hidden');
        }
        if (collectionListEmptyEl) collectionListEmptyEl.classList.add('hidden');
        if (collectionListErrorEl) collectionListErrorEl.classList.add('hidden');
    }
    if (currentCollectionBadgeContainerEl) {
        currentCollectionBadgeContainerEl.classList.add('hidden');
    }
    // Assicura che il dropdown sia visivamente chiuso
    if (collectionListDropdownButtonEl && collectionListDropdownMenuEl && !collectionListDropdownMenuEl.classList.contains('hidden')) {
        collectionListDropdownMenuEl.classList.add('hidden');
        collectionListDropdownButtonEl.setAttribute('aria-expanded', 'false');
    }
    console.log("Padmin CollectionUI: State and UI reset for logout.");
}