    // File: resources/ts/features/auth/authService.ts

    /**
     * 📜 Oracode TypeScript Module: AuthService
     * Fornisce funzioni di base per la gestione dello stato di autenticazione,
     * del wallet connesso (sia strong auth via backend che weak auth via localStorage),
     * e per la gestione dello stato transitorio relativo alle azioni pendenti
     * post-autenticazione e al focus degli elementi prima dell'apertura di modali.
     *
     * @version 1.2.0 (Padmin Verified - Documentation Complete, No Placeholders)
     * @date 2025-05-11
     * @author Padmin D. Curtis (for Fabio Cherici)
     */

    import { AppConfig } from '../../config/appConfig'; // Dipende solo da AppConfig per i dati iniziali

    // --- 🧠 STATO INTERNO DEL MODULO (Accessibile tramite funzioni esportate) ---

    /**
     * @private
     * Memorizza l'azione che l'utente intendeva compiere prima di dover
     * passare attraverso un flusso di autenticazione/connessione wallet.
     * @type {('create-egi' | 'create-collection' | null)}
     */
    let currentPendingAuthAction: 'create-egi' | 'create-collection' | null = null;

    /**
     * @private
     * Memorizza l'elemento HTML che aveva il focus prima che una modale
     * (solitamente la modale di connessione wallet) venisse aperta.
     * Utilizzato per ripristinare il focus per accessibilità.
     * @type {(HTMLElement | null)}
     */
    let lastFocusedElementBeforeModal: HTMLElement | null = null;

    // --- FUNZIONI ESPORTATE ---

    /**
     * 📜 Oracode Function: setPendingAuthAction
     * 🎯 Imposta un'azione specifica che l'utente desidera eseguire dopo aver
     * completato con successo un flusso di autenticazione o connessione wallet.
     *
     * @export
     * @param {('create-egi' | 'create-collection' | null)} action L'azione pendente.
     *        Può essere 'create-egi', 'create-collection', o null per resettare.
     */
    export function setPendingAuthAction(action: 'create-egi' | 'create-collection' | null): void {
        currentPendingAuthAction = action;
        // console.log('Padmin AuthState: Pending action set to ->', action);
    }

    /**
     * 📜 Oracode Function: consumePendingAuthAction
     * 🎯 Recupera l'azione pendente corrente e la resetta immediatamente a `null`.
     * Questo assicura che l'azione venga "consumata" e non eseguita più volte.
     * Da chiamare dopo che il flusso di autenticazione è stato completato e l'azione
     * sta per essere eseguita.
     *
     * @export
     * @returns {('create-egi' | 'create-collection' | null)} L'azione che era pendente, o `null` se nessuna.
     */
    export function consumePendingAuthAction(): 'create-egi' | 'create-collection' | null {
        const action = currentPendingAuthAction;
        currentPendingAuthAction = null; // Resetta dopo averla letta
        // console.log('Padmin AuthState: Pending action consumed ->', action);
        return action;
    }

    /**
     * 📜 Oracode Function: setLastFocusedElement
     * 🎯 Salva un riferimento all'elemento HTML che aveva il focus prima
     * dell'apertura di una modale (tipicamente la modale di connessione wallet).
     * Questo è cruciale per l'accessibilità, per ripristinare il focus correttamente
     * quando la modale viene chiusa.
     *
     * @export
     * @param {(HTMLElement | null)} element L'elemento HTML che aveva il focus, o `null`.
     */
    export function setLastFocusedElement(element: HTMLElement | null): void {
        lastFocusedElementBeforeModal = element;
        // if (element) console.log('Padmin AuthState: Last focused element saved ->', element);
    }

    /**
     * 📜 Oracode Function: consumeLastFocusedElement
     * 🎯 Recupera l'elemento che aveva il focus prima dell'apertura di una modale
     * e resetta immediatamente il riferimento interno a `null`.
     * Da chiamare quando la modale viene chiusa per ripristinare il focus.
     *
     * @export
     * @returns {(HTMLElement | null)} L'elemento che aveva il focus, o `null`.
     */
    export function consumeLastFocusedElement(): HTMLElement | null {
        const element = lastFocusedElementBeforeModal;
        lastFocusedElementBeforeModal = null; // Resetta dopo averlo letto
        // if (element) console.log('Padmin AuthState: Last focused element consumed ->', element);
        return element;
    }

    /**
     * 📜 Oracode Function: getAuthStatus
     * 🎯 Determina lo stato di autenticazione corrente dell'utente.
     * Controlla prima l'autenticazione forte via backend (se l'utente è loggato),
     * poi l'autenticazione debole via `localStorage` (se un wallet è connesso localmente),
     * altrimenti considera l'utente disconnesso.
     *
     * @export
     * @param {AppConfig} config L'oggetto di configurazione dell'applicazione, contenente `isAuthenticatedByBackend`.
     * @returns {('logged-in' | 'connected' | 'disconnected')} Lo stato di autenticazione.
     */
    export function getAuthStatus(config: AppConfig): 'logged-in' | 'connected' | 'disconnected' {
        if (config.isAuthenticated) {
            return 'logged-in';
        }
        if (localStorage.getItem('connected_wallet')) {
            return 'connected';
        }
        return 'disconnected';
    }

    /**
     * 📜 Oracode Function: getConnectedWalletAddress
     * 🎯 Recupera l'indirizzo del wallet Algorand attualmente connesso.
     * Se l'utente è 'logged-in' (autenticazione forte), restituisce l'indirizzo wallet
     * associato all'account utente dal backend (fornito tramite `AppConfig`).
     * Altrimenti (per stati 'connected' o 'disconnected'), tenta di recuperarlo
     * dal `localStorage` (autenticazione debole).
     *
     * @export
     * @param {AppConfig} config L'oggetto di configurazione, contenente `loggedInUserWallet`.
     * @returns {(string | null)} L'indirizzo del wallet connesso (58 caratteri) o `null` se nessuno.
     */
    export function getConnectedWalletAddress(config: AppConfig): string | null {
        const authStatus = getAuthStatus(config); // Riusa la logica di getAuthStatus
        if (authStatus === 'logged-in') {
            return config.loggedInUserWallet || null;
        }
        // Per 'connected' o 'disconnected', il fallback è sempre localStorage
        return localStorage.getItem('connected_wallet');
    }

    /**
     * 📜 Oracode Function: setWeakAuthWallet
     * 🎯 Imposta o rimuove l'indirizzo del wallet per l'autenticazione debole nel `localStorage`.
     * Questa funzione è usata quando un utente connette il proprio wallet senza effettuare un login completo.
     * Dopo aver modificato il `localStorage`, invoca una `uiUpdateCallback` per permettere
     * all'interfaccia utente principale (es. la navbar) di riflettere il cambiamento di stato.
     *
     * @export
     * @param {(string | null)} address L'indirizzo wallet da salvare. Se `null` o stringa vuota,
     *                                  l'indirizzo esistente viene rimosso dal `localStorage`.
     * @param {() => void} uiUpdateCallback Una funzione callback che viene invocata per
     *                                     notificare all'UI che lo stato è cambiato e necessita un aggiornamento.
     */
    export function setWeakAuthWallet(address: string | null, uiUpdateCallback: () => void): void {
        if (address && typeof address === 'string' && address.trim() !== '') {
            // TODO: [PADMIN_VALIDATION] Considerare una validazione base del formato dell'address Algorand qui prima di salvarlo.
            localStorage.setItem('connected_wallet', address);
            console.log('Padmin AuthState: Weak auth wallet set in localStorage:', address);
        } else {
            localStorage.removeItem('connected_wallet');
            console.log('Padmin AuthState: Weak auth wallet removed from localStorage.');
        }
        uiUpdateCallback(); // Notifica l'UI del cambiamento
    }
