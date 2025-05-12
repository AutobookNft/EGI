// File: resources/ts/features/auth/walletConnect.ts

/**
 * 📜 Oracode TypeScript Module: WalletConnectModalHandler
 * Gestisce l'apertura/chiusura della modale di connessione wallet (#connect-wallet-modal)
 * e la logica di submit del form per connettere un wallet, stabilendo una "weak auth".
 * Utilizza authService per gestire lo stato di autenticazione e le azioni pendenti.
 *
 * @version 1.0.1 (Padmin Corrected Translation Calls, No Placeholders)
 * @date 2025-05-11
 * @author Padmin D. Curtis (for Fabio Cherici)
 */

import { AppConfig, ServerErrorResponse, appTranslate } from '../../config/appConfig';
import * as DOMElements from '../../dom/domElements';
import { UEM_Client_TS_Placeholder as UEM } from '../../services/uemClientService';
import { UploadModalManager } from '../../ui/uploadModalManager';
import {
    setPendingAuthAction,
    consumePendingAuthAction,
    setLastFocusedElement,
    consumeLastFocusedElement,
    getAuthStatus,
    setWeakAuthWallet
} from './authService';
import { getCsrfTokenTS } from '../../utils/csrf';

/**
 * 📜 Oracode Function: openConnectWalletModal
 * 🎯 Apre la modale di connessione wallet.
 * Salva l'azione pendente e l'elemento con focus precedente.
 *
 * @export
 * @param {AppConfig} config L'oggetto di configurazione dell'applicazione (usato per le traduzioni dei messaggi di errore).
 * @param {typeof DOMElements} DOM Collezione dei riferimenti agli elementi DOM.
 * @param {('create-egi' | 'create-collection' | null)} [pendingAction=null] L'azione che l'utente
 *        intendeva compiere e che ha triggerato l'apertura di questa modale.
 */
export function openConnectWalletModal(
    config: AppConfig,
    DOM: typeof DOMElements,
    pendingAction: 'create-egi' | 'create-collection' | null = null
): void {
    if (!DOM.connectWalletModalEl) {
        UEM.handleClientError('CLIENT_DOM_MISSING_CONNECT_MODAL', { elementId: 'connect-wallet-modal' }, undefined, appTranslate('errorModalNotFoundConnectWallet', config.translations));
        return;
    }

    setPendingAuthAction(pendingAction);
    setLastFocusedElement(document.activeElement as HTMLElement | null);

    DOM.connectWalletModalEl.classList.remove('hidden');
    DOM.connectWalletModalEl.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';

    if (DOM.connectWalletAddressInputEl) DOM.connectWalletAddressInputEl.focus();
    else DOM.connectWalletModalEl.focus();
    console.log('Padmin WalletConnect: Modal Opened. Pending Action:', pendingAction);
}

/**
 * 📜 Oracode Function: closeConnectWalletModal
 * 🎯 Chiude la modale di connessione wallet.
 * Ripristina il focus sull'elemento precedente, resetta l'azione pendente
 * (se non già consumata) e pulisce i messaggi di errore e l'input.
 *
 * @export
 * @param {typeof DOMElements} DOM Collezione dei riferimenti agli elementi DOM.
 */
export function closeConnectWalletModal(DOM: typeof DOMElements): void {
    if (!DOM.connectWalletModalEl || DOM.connectWalletModalEl.classList.contains('hidden')) {
        return;
    }

    DOM.connectWalletModalEl.classList.add('hidden');
    DOM.connectWalletModalEl.setAttribute('aria-hidden', 'true');

    if (!DOM.uploadModalEl || DOM.uploadModalEl.classList.contains('hidden')) {
        document.body.style.overflow = '';
    }

    const lastFocused = consumeLastFocusedElement(); // Ottiene e resetta
    if (lastFocused) {
        lastFocused.focus();
    }
    consumePendingAuthAction(); // Assicura che anche l'azione pendente sia resettata se non usata

    if (DOM.walletErrorMessageEl) DOM.walletErrorMessageEl.classList.add('hidden');
    if (DOM.connectWalletAddressInputEl) DOM.connectWalletAddressInputEl.value = '';
    console.log('Padmin WalletConnect: Modal Closed.');
}

/**
 * 📜 Oracode Function: handleConnectWalletSubmit
 * 🎯 Gestisce l'evento di submit del form di connessione del wallet.
 * Esegue la validazione dell'input, invia la richiesta API per connettere il wallet,
 * gestisce la risposta (successo o errore UEM), e coordina le azioni post-connessione
 * come l'apertura della modale di upload EGI o il redirect alla registrazione.
 *
 * @export
 * @param {Event} event L'evento di submit del form.
 * @param {AppConfig} config L'oggetto di configurazione dell'applicazione.
 * @param {typeof DOMElements} DOM Collezione dei riferimenti agli elementi DOM.
 * @param {(UploadModalManager | null)} uploadModalMgr Istanza del gestore della modale di upload.
 * @param {typeof UEM} uem Istanza del gestore errori client UEM.
 * @param {() => void} uiUpdateCallback Callback per notificare l'UI principale di un cambiamento di stato auth.
 */
export async function handleConnectWalletSubmit(
    event: Event,
    config: AppConfig,
    DOM: typeof DOMElements,
    uploadModalMgr: UploadModalManager | null,
    uem: typeof UEM,
    uiUpdateCallback: () => void
): Promise<void> {
    event.preventDefault();
    if (!DOM.connectWalletFormEl || !DOM.walletErrorMessageEl || !DOM.connectSubmitButtonEl || !DOM.connectWalletAddressInputEl) {
        uem.handleClientError('CLIENT_DOM_MISSING_CONNECT_FORM_SUBMIT', {}, undefined, appTranslate('errorConnectWalletFormMissing', config.translations));
        return;
    }

    DOM.connectSubmitButtonEl.disabled = true;
    DOM.connectSubmitButtonEl.textContent = appTranslate('connecting', config.translations);
    DOM.walletErrorMessageEl.classList.add('hidden');

    try {
        const formData = new FormData(DOM.connectWalletFormEl);
        const walletAddressInput = formData.get('wallet_address') as string | null;

        if (!walletAddressInput || walletAddressInput.trim() === '') {
            const errMsg = appTranslate('walletAddressRequired', config.translations);
            uem.handleClientError('CLIENT_WALLET_FORM_INVALID_REQUIRED', { field: 'wallet_address' }, undefined, errMsg);
            if (DOM.walletErrorMessageEl) {
                DOM.walletErrorMessageEl.textContent = errMsg;
                DOM.walletErrorMessageEl.classList.remove('hidden');
            }
            throw new Error('Validation failed: Wallet address required.'); // Per bloccare il finally e il resto del try
        }
        // TODO: [PADMIN_VALIDATION] Implementare validazione client-side più robusta per il formato dell'indirizzo Algorand (58 caratteri, inizia con A-Z, ecc.)
        // Esempio base: if (!/^[A-Z2-7]{58}$/.test(walletAddressInput)) { ... }

        const response = await fetch(config.routes.walletConnect, { // Usa la rotta da AppConfig
            method: 'POST',
            body: new URLSearchParams(formData as any).toString(),
            headers: { 'X-CSRF-TOKEN': getCsrfTokenTS(), 'Accept': 'application/json', 'Content-Type': 'application/x-www-form-urlencoded' }
        });
        // La risposta JSON può essere di successo o un errore UEM formattato
        const data: ServerErrorResponse | { message: string; wallet_address: string; } = await response.json();

        if (!response.ok) {
            // Errore UEM dal backend o altro errore HTTP
            uem.handleServerErrorResponse(data as ServerErrorResponse, appTranslate('errorConnectionFailed', config.translations, { code: response.status }));
            if (DOM.walletErrorMessageEl) {
                DOM.walletErrorMessageEl.textContent = (data as ServerErrorResponse).message || appTranslate('errorConnectionGeneric', config.translations);
                DOM.walletErrorMessageEl.classList.remove('hidden');
            }
            throw new Error((data as ServerErrorResponse).message || `HTTP error ${response.status}`); // Per il blocco catch
        }

        // SUCCESSO dalla chiamata API
        const successData = data as { message: string; wallet_address: string; };
        const actionToPerform = consumePendingAuthAction(); // Ottiene E resetta l'azione pendente da authService

        setWeakAuthWallet(successData.wallet_address, uiUpdateCallback); // Salva in localStorage e CHIAMA L'AGGIORNAMENTO UI
        closeConnectWalletModal(DOM);     // Chiude questa modale

        if (actionToPerform === 'create-egi') {
            if (uploadModalMgr) {
                console.log('Padmin WalletConnect: Wallet connected, opening EGI upload modal...');
                setTimeout(() => uploadModalMgr.openModal('egi'), 100); // Leggero ritardo per transizioni UI
            } else {
                uem.handleClientError('CLIENT_MODAL_MANAGER_MISSING_EGI', { manager: 'uploadModalManager' }, undefined, appTranslate('errorEgiFormOpen', config.translations));
            }
        } else if (actionToPerform === 'create-collection') {
            const currentAuthStatus = getAuthStatus(config);
            if (currentAuthStatus === 'logged-in') {
                window.location.href = config.routes.collectionsCreate; // Usa la rotta da AppConfig
            } else { // Altrimenti è solo 'connected' (weak auth), quindi serve registrazione completa
                if (window.Swal) {
                    window.Swal.fire({
                        icon: 'info',
                        title: appTranslate('registrationRequiredTitle', config.translations),
                        text: appTranslate('registrationRequiredTextCollections', config.translations),
                        confirmButtonText: appTranslate('registerNowButton', config.translations),
                        showCancelButton: true,
                        cancelButtonText: appTranslate('laterButton', config.translations)
                    }).then((result: { isConfirmed: boolean }) => {
                        if (result.isConfirmed) window.location.href = config.routes.register; // Usa la rotta da AppConfig
                    });
                } else {
                    alert(appTranslate('registrationRequiredTextCollections', config.translations));
                    window.location.href = config.routes.register; // Usa la rotta da AppConfig
                }
            }
        } else {
            console.log('Padmin WalletConnect: Wallet connected successfully (no pending action). UI has been updated.');
            if (window.Swal) {
                window.Swal.fire({
                    icon: 'success', title: appTranslate('walletConnectedTitle', config.translations),
                    toast: true, position: 'top-end', showConfirmButton: false,
                    timer: 2000, timerProgressBar: true
                });
            }
        }
    } catch (error: any) {
        // Gestisce 'Validation failed' o altri errori JS nel blocco try
        console.error("Padmin WalletConnect: Error in connect wallet submit flow:", error.message);
        if (DOM.walletErrorMessageEl && DOM.walletErrorMessageEl.classList.contains('hidden') && error.message !== 'Validation failed: Wallet address required.') {
             // Mostra messaggio solo se non è già stato mostrato per la validazione o da UEM
            DOM.walletErrorMessageEl.textContent = error.message || appTranslate('errorUnexpected', config.translations);
            DOM.walletErrorMessageEl.classList.remove('hidden');
        }
    } finally {
        if (DOM.connectSubmitButtonEl) {
            DOM.connectSubmitButtonEl.disabled = false;
            DOM.connectSubmitButtonEl.textContent = appTranslate('connect', config.translations);
        }
    }
}