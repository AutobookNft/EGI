// File: resources/ts/features/auth/walletDropdown.ts

/**
 * 📜 Oracode TypeScript Module: WalletDropdownHandler
 * Gestisce le interazioni con il dropdown del wallet per utenti connessi/loggati,
 * includendo l'apertura/chiusura del menu, la copia dell'indirizzo wallet
 * e la gestione della disconnessione/logout.
 *
 * @version 1.0.1 (Padmin Corrected Translation Calls, No Placeholders)
 * @date 2025-05-11
 * @author Padmin D. Curtis (for Fabio Cherici)
 */

import { AppConfig, appTranslate } from '../../config/appConfig';
import * as DOMElements from '../../dom/domElements';
import { UEM_Client_TS_Placeholder as UEM } from '../../services/uemClientService';
import { getConnectedWalletAddress, getAuthStatus, setWeakAuthWallet } from './authService';
import { closeCollectionListDropdown } from '../collections/collectionUI'; // Per chiudere l'altro dropdown
import { getCsrfTokenTS } from '../../utils/csrf'; // Necessario per la chiamata API opzionale in handleDisconnect

// --- STATO INTERNO DEL MODULO ---
let isWalletDropdownMenuOpenState: boolean = false; // Rinominato per chiarezza rispetto a variabili globali
let walletOutsideClickHandlerInstance: ((event: MouseEvent) => void) | null = null;
let walletKeydownHandlerInstance: ((event: KeyboardEvent) => void) | null = null;

// --- FUNZIONI HELPER INTERNE ---

/**
 * @private
 * Controlla e restituisce gli elementi DOM necessari per il dropdown wallet.
 * Se mancano, logga un errore UEM.
 */
function getRequiredWalletDropdownDOMElements(
    DOM: typeof DOMElements,
    uem: typeof UEM,
    config: AppConfig
): { walletDropdownMenuEl: HTMLDivElement, walletDropdownButtonEl: HTMLButtonElement } | null {
    const { walletDropdownMenuEl, walletDropdownButtonEl } = DOM;
    if (!walletDropdownMenuEl || !walletDropdownButtonEl) {
        uem.handleClientError('CLIENT_DOM_MISSING_WALLET_DROPDOWN_CORE', {}, undefined, appTranslate('errorWalletDropdownMissing', config.translations));
        return null;
    }
    return { walletDropdownMenuEl, walletDropdownButtonEl };
}

// --- FUNZIONI ESPORTATE ---

/**
 * 📜 Oracode Function: closeWalletDropdownMenu
 * 🎯 Chiude il menu dropdown del wallet.
 * Rimuove i listener di eventi globali associati a questo dropdown (click esterni, keydown).
 *
 * @export
 * @param {typeof DOMElements} DOM Riferimenti agli elementi DOM.
 */
export function closeWalletDropdownMenu(DOM: typeof DOMElements): void {
    // Non servono config e uem solo per chiudere, ma le altre funzioni potrebbero
    const { walletDropdownMenuEl, walletDropdownButtonEl } = DOM;
    if (!walletDropdownMenuEl || !walletDropdownButtonEl || walletDropdownMenuEl.classList.contains('hidden')) {
        return;
    }

    walletDropdownMenuEl.classList.add('hidden');
    walletDropdownButtonEl.setAttribute('aria-expanded', 'false');

    if (walletOutsideClickHandlerInstance) {
        document.removeEventListener('click', walletOutsideClickHandlerInstance);
        walletOutsideClickHandlerInstance = null;
    }
    if (walletKeydownHandlerInstance && walletDropdownMenuEl) {
        walletDropdownMenuEl.removeEventListener('keydown', walletKeydownHandlerInstance);
        walletKeydownHandlerInstance = null;
    }
    isWalletDropdownMenuOpenState = false;
    // console.log('Padmin WalletDropdown: Menu closed.');
}

/**
 * @private
 * Gestisce i click fuori dal dropdown del wallet per chiuderlo.
 */
function handleOutsideWalletDropdownClick(event: MouseEvent, DOM: typeof DOMElements): void {
    const target = event.target as Node;
    // Non serve uem o config qui
    const { walletDropdownButtonEl, walletDropdownMenuEl } = DOM;
    if (walletDropdownButtonEl && !walletDropdownButtonEl.contains(target) &&
        walletDropdownMenuEl && !walletDropdownMenuEl.contains(target)) {
        closeWalletDropdownMenu(DOM);
    }
}

/**
 * @private
 * Gestisce la pressione del tasto Escape nel dropdown del wallet per chiuderlo.
 */
export function handleWalletDropdownKeydown(event: KeyboardEvent, DOM: typeof DOMElements): void {
    // Non serve uem o config qui
    const { walletDropdownButtonEl } = DOM;
    if (event.key === 'Escape') {
        closeWalletDropdownMenu(DOM);
        if (walletDropdownButtonEl) walletDropdownButtonEl.focus();
    }
    // TODO: [PADMIN_ACCESSIBILITY] Implementare navigazione con frecce UP/DOWN tra gli item del menu.
}

/**
 * 📜 Oracode Function: toggleWalletDropdownMenu
 * 🎯 Apre o chiude il menu dropdown del wallet.
 * Assicura che l'altro dropdown principale (lista collection) sia chiuso.
 * Gestisce l'aggiunta/rimozione dei listener per la chiusura automatica.
 *
 * @export
 * @param {AppConfig} config L'oggetto di configurazione dell'applicazione.
 * @param {typeof DOMElements} DOM Riferimenti agli elementi DOM.
 * @param {typeof UEM} uem Istanza del gestore errori UEM.
 */
export function toggleWalletDropdownMenu(config: AppConfig, DOM: typeof DOMElements, uem: typeof UEM): void {
    const elements = getRequiredWalletDropdownDOMElements(DOM, uem, config);
    if (!elements) return; // Errore già gestito da getRequired...

    const { walletDropdownMenuEl, walletDropdownButtonEl } = elements;

    if (isWalletDropdownMenuOpenState) {
        closeWalletDropdownMenu(DOM);
    } else {
        // Chiudi l'altro dropdown (collection list) prima di aprire questo
        closeCollectionListDropdown(config, DOM, uem); // Da collectionUI.ts

        walletDropdownMenuEl.classList.remove('hidden');
        walletDropdownButtonEl.setAttribute('aria-expanded', 'true');
        isWalletDropdownMenuOpenState = true;

        // Crea e assegna nuove istanze dei gestori per questa apertura, passando DOM
        walletOutsideClickHandlerInstance = (e: MouseEvent) => handleOutsideWalletDropdownClick(e, DOM);
        walletKeydownHandlerInstance = (e: KeyboardEvent) => handleWalletDropdownKeydown(e, DOM);

        document.addEventListener('click', walletOutsideClickHandlerInstance);
        walletDropdownMenuEl.addEventListener('keydown', walletKeydownHandlerInstance);

        const firstMenuItem = walletDropdownMenuEl.querySelector<HTMLButtonElement>('button[role="menuitem"]');
        if (firstMenuItem) {
            firstMenuItem.focus();
        } else {
            walletDropdownMenuEl.focus(); // Fallback
        }
        // console.log('Padmin WalletDropdown: Menu opened.');
    }
}

/**
 * 📜 Oracode Function: copyWalletAddress
 * 🎯 Copia l'indirizzo del wallet connesso corrente negli appunti dell'utente.
 * Mostra un feedback visivo temporaneo sul bottone.
 *
 * @export
 * @param {AppConfig} config L'oggetto di configurazione dell'applicazione.
 * @param {typeof DOMElements} DOM Riferimenti agli elementi DOM.
 * @param {typeof UEM} uem Istanza del gestore errori UEM.
 */
export function copyWalletAddress(config: AppConfig, DOM: typeof DOMElements, uem: typeof UEM): void {
    const walletAddress = getConnectedWalletAddress(config); // Da authService
    if (!walletAddress || !DOM.walletCopyAddressButtonEl) {
        if (!walletAddress) {
            uem.handleClientError("CLIENT_WALLET_ACTION_FAIL_NO_ADDRESS_COPY", { action: "copyAddress" }, undefined, appTranslate('errorNoWalletToCopy', config.translations));
        }
        // Se il bottone manca, è un errore DOM che dovrebbe essere loggato, ma l'azione non può procedere.
        return;
    }

    navigator.clipboard.writeText(walletAddress)
        .then(() => {
            const originalHTML = DOM.walletCopyAddressButtonEl!.innerHTML;
            // Assicurati che `copied` sia una chiave di traduzione valida
            DOM.walletCopyAddressButtonEl!.textContent = appTranslate('copied', config.translations) + ' ✓';
            DOM.walletCopyAddressButtonEl!.disabled = true;
            setTimeout(() => {
                if (DOM.walletCopyAddressButtonEl) {
                    DOM.walletCopyAddressButtonEl.innerHTML = originalHTML;
                    DOM.walletCopyAddressButtonEl.disabled = false;
                }
            }, 1500);
        })
        .catch(err => {
            uem.handleClientError('CLIENT_CLIPBOARD_ERROR_WALLET', { errorDetails: (err as Error).message || String(err) }, (err instanceof Error ? err : undefined), appTranslate('errorCopyAddress', config.translations));
        });
    closeWalletDropdownMenu(DOM); // Chiude il dropdown dopo l'azione
}

/**
 * 📜 Oracode Function: handleDisconnect
 * 🎯 Gestisce la richiesta di disconnessione del wallet o di logout dell'utente.
 * Se l'utente è 'logged-in', esegue il submit del form di logout.
 * Se l'utente è solo 'connected' (weak auth), pulisce il `localStorage` e aggiorna l'UI.
 *
 * @export
 * @param {AppConfig} config L'oggetto di configurazione dell'applicazione.
 * @param {typeof DOMElements} DOM Riferimenti agli elementi DOM.
 * @param {typeof UEM} uem Istanza del gestore errori UEM.
 * @param {() => void} uiUpdateCallback Funzione callback per notificare all'UI principale
 *                                     (es. navbarManager) che lo stato di autenticazione è cambiato.
 */
export async function handleDisconnect(
    config: AppConfig,
    DOM: typeof DOMElements,
    uem: typeof UEM,
    uiUpdateCallback: () => void
): Promise<void> {
    const authStatus = getAuthStatus(config); // Da authService
    closeWalletDropdownMenu(DOM); // Chiudi il menu prima di qualsiasi azione

    if (authStatus === 'logged-in') {
        // Se il form di logout non è stato trovato, prova a re-inizializzare i DOM elements
        if (!DOM.logoutFormEl) {
            console.warn('🔄 [LOGOUT] Form di logout non trovato, re-inizializzazione DOM...');
            const logoutForm = document.getElementById('logout-form') as HTMLFormElement;
            if (logoutForm) {
                DOM.logoutFormEl = logoutForm;
                console.log('✅ [LOGOUT] Form di logout trovato dopo re-inizializzazione');
            }
        }

        if (DOM.logoutFormEl) {
            DOM.logoutFormEl.submit(); // Questo causerà un ricaricamento della pagina
        } else {
            uem.handleClientError('CLIENT_DOM_MISSING_LOGOUT_FORM_DISCONNECT', {}, undefined, appTranslate('errorLogoutFormMissing', config.translations));
        }
    } else if (authStatus === 'connected') {
        try {
            // Chiamata API per disconnettere lato server per utenti "connected"
            if (config.routes?.walletDisconnect) {
                const response = await fetch(config.routes.walletDisconnect, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': getCsrfTokenTS(),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                console.log('✅ [WALLET] Server-side disconnect successful for connected user');
            } else {
                console.warn('⚠️ [WALLET] walletDisconnect route not configured, proceeding with client-side disconnect only');
            }
        } catch (apiError: any) {
            console.error('❌ [WALLET] Server-side disconnect failed:', apiError);
            uem.handleClientError('CLIENT_API_ERROR_WEAK_DISCONNECT', { 
                endpoint: 'walletDisconnect', 
                errorDetails: apiError.message 
            }, apiError, appTranslate('errorApiDisconnect', config.translations));
        }
        
        // Client-side cleanup sempre eseguito (anche se API fallisce)
        setWeakAuthWallet(null, uiUpdateCallback);
        console.log('Padmin WalletDropdown: Disconnected locally (weak auth). UI update triggered.');

        if (window.Swal) {
            window.Swal.fire({
                title: appTranslate('disconnectedTitle', config.translations),
                text: appTranslate('disconnectedTextWeak', config.translations),
                icon: 'success',
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            }).then(() => {
                // Refresh della pagina dopo il toast per assicurare aggiornamento completo UI
                setTimeout(() => {
                    window.location.reload();
                }, 300);
            });
        } else {
            // Se non c'è Swal, refresh immediato
            setTimeout(() => {
                window.location.reload();
            }, 500);
        }
    } else {
        // Stato 'disconnected', non c'è nulla da disconnettere. Log anomalia?
        console.warn("Padmin WalletDropdown: HandleDisconnect called while already in 'disconnected' state.");
    }
}
