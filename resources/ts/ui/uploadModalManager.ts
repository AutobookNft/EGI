// File: resources/ts/ui/uploadModalManager.ts (Nuovo nome e posizione suggerita)
// Precedentemente open-close-modal.ts

/**
 * 📜 Oracode TypeScript Module: UploadModalManager
 * Gestisce specificamente l'apertura, la chiusura e la logica associata
 * per la modale di UPLOAD (#upload-modal) di FlorenceEGI.
 *
 * @version 2.0.0-ts (Refactored)
 * @date 2025-05-10
 * @author Padmin D. Curtis (for Fabio Cherici)
 */

// --- 💎 IMPORTAZIONI TIPI E UTILITIES ---
// import { appConfig } from '../config/appConfig'; // Se necessario per rotte API o traduzioni
// import { UEM_Client_TS } from '../services/uemClientService'; // Se UEM client è in un modulo
// Per ora, assumiamo che CSRF e UEM siano gestiti esternamente o tramite funzioni globali/helper

// --- 🔗 INTERFACCIA PER GLI ELEMENTI DOM NECESSARI ---
export interface UploadModalDomElements {
    modal: HTMLDivElement;
    // openButtons: NodeListOf<HTMLButtonElement>; // Questi erano per l'apertura DIRETTA da layout app, non da guest
    closeButton: HTMLButtonElement; // Il bottone "X" o "Return" dentro la modale
    modalContent: HTMLDivElement; // L'area di contenuto principale della modale
}

// Riferimento globale al gestore di upload file (da UUM/EGI-Module)
// Assumiamo che sia ancora su window per ora, finché non refattorizziamo anche quello.
declare global {
    interface Window {
        fileUploadManager?: {
            resetUploadForm: () => void;
        };
        // redirectToURL?: () => void; // Se ancora usato
    }
}


export class UploadModalManager {
    private elements: UploadModalDomElements;
    private isOpen: boolean;
    private csrfToken: string; // Se l'auth check è ancora qui
    private lastFocusedElement: HTMLElement | null = null;

    /**
     * @constructor
     * @param {UploadModalDomElements} domElements Oggetto contenente i riferimenti agli elementi DOM della modale.
     * @param {string} csrfToken Il token CSRF per eventuali chiamate API (es. auth check).
     */
    constructor(domElements: UploadModalDomElements, csrfToken: string) {
        this.elements = domElements;
        this.isOpen = false;
        this.csrfToken = csrfToken;

        this.initializeEventListeners();
        console.log('Padmin D. Curtis: UploadModalManager instance created.');
    }

    private initializeEventListeners(): void {
        if (!this.elements.modal || !this.elements.closeButton || !this.elements.modalContent) {
            console.warn('UploadModalManager: Critical DOM elements missing. Modal may not function.');
            // UEM_Client_TS.handleClientError('CLIENT_DOM_MISSING_MODAL_UPLOAD', { missing: !this.elements.modal ? 'modal' : !this.elements.closeButton ? 'closeButton' : 'modalContent' });
            return;
        }

        // Listener per il bottone di chiusura INTERNO alla modale
        this.elements.closeButton.addEventListener('click', (event: MouseEvent) => {
            event.preventDefault();
            this.closeModal();
        });

        // Listener per cliccare fuori dalla modale (sul backdrop) per chiuderla
        this.elements.modal.addEventListener('click', (event: MouseEvent) => {
            if (event.target === this.elements.modal) {
                this.closeModal();
            }
        });


        // Listener per l'evento custom 'upload-completed' (se ancora usato)
        document.addEventListener('upload-completed', () => {
            console.log('UploadModalManager: Event "upload-completed" received, closing modal.');
            this.closeModal();
        });
    }

    /**
     * 🎯 Gestisce l'apertura della modale DOPO un check di autorizzazione.
     * Questa funzione era pensata per bottoni nel layout `app.blade.php` (utenti loggati).
     * Potrebbe essere richiamata dal `main.ts` se quei bottoni esistono ancora.
     * @param {string} [uploadType='egi'] Il tipo di upload.
     * @deprecated Se il check di autorizzazione viene fatto prima di chiamare openModal().
     *             Preferire `openModal()` direttamente dopo aver verificato l'autorizzazione esternamente.
     */
    public async openModalWithAuthCheck(uploadType: string = 'egi'): Promise<void> {
        // Fabio, questo check di autorizzazione era specifico per bottoni che aprono DIRETTAMENTE
        // la modale di upload (tipicamente in un layout per utenti già loggati).
        // Nel flusso guest, l'autorizzazione è implicita dopo la connessione del wallet,
        // quindi `main.ts` chiamerà `openModal()` direttamente.
        // Se questi bottoni di apertura diretta (con auth check) esistono ancora, questa logica serve.
        // Altrimenti, possiamo rimuoverla o marcarla come deprecata.
        console.log(`UploadModalManager: openModalWithAuthCheck called. Type: ${uploadType}. Checking auth...`);
        try {
            // const apiConfig = getAppConfig().routes.api; // Assumendo che esista un endpoint per questo
            // const authCheckEndpoint = apiConfig.checkUploadAuth; // Esempio
            const response = await fetch('/api/check-upload-authorization', { // TODO: Usare rotta da appConfig
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                },
            });

            if (!response.ok) {
                const errorData: ServerErrorResponse = await response.json().catch(() => ({ error: 'HTTP_ERROR', message: `Auth check HTTP error ${response.status}` }));
                console.error('UploadModalManager: Auth check failed.', errorData);
                // UEM_Client_TS.handleServerErrorResponse(errorData, 'Authorization check failed.');
                alert(errorData.message || 'Authorization check failed.'); // Semplice alert per ora
                if (response.status === 401 || response.status === 403) {
                    // const loginRoute = getAppConfig().routes.login; // Esempio
                    // window.location.href = loginRoute || '/login';
                }
                return;
            }

            const result = await response.json();
            if (result.authorized) {
                this.openModal(uploadType);
            } else {
                console.warn('UploadModalManager: User not authorized for upload.', result);
                alert('You are not authorized to perform this action.'); // Semplice alert
                // window.location.href = result.redirect || getAppConfig().routes.login || '/login';
            }
        } catch (error: any) {
            console.error('UploadModalManager: Error during authorization check:', error);
            // UEM_Client_TS.handleClientError('CLIENT_AUTH_CHECK_ERROR_UPLOAD_MODAL', { error: error.message }, error);
            alert('Could not verify authorization. Please try again.');
        }
    }

    /**
     * 🎯 Apre la modale di upload.
     * Assume che l'autorizzazione sia già stata verificata dal chiamante.
     * @param {string} [uploadType='egi'] Il tipo di upload.
     */
    public openModal(uploadType: string = 'egi'): void {
        if (this.isOpen || !this.elements.modal || !this.elements.modalContent) {
            console.warn('UploadModalManager: Attempted to open modal when already open or elements missing.');
            return;
        }
        console.log(`Padmin D. Curtis: Opening upload modal programmatically. Type: ${uploadType}`);

        this.lastFocusedElement = document.activeElement as HTMLElement | null;

        // Assicurati che la modale di connessione wallet sia chiusa (se il main.ts non lo fa già)
        const connectWalletModal = document.getElementById('connect-wallet-modal') as HTMLDivElement | null;
        if (connectWalletModal && !connectWalletModal.classList.contains('hidden')) {
            const closeButton = connectWalletModal.querySelector<HTMLButtonElement>('#close-connect-wallet-modal');
            if (closeButton) closeButton.click();
            else connectWalletModal.classList.add('hidden'); // Fallback
        }

        this.elements.modal.classList.remove('hidden');
        this.elements.modal.classList.add('flex'); // Usa flex per centrare (come da HTML originale)
        this.elements.modalContent.dataset.uploadType = uploadType; // Passa il tipo di upload se serve al form interno
        this.isOpen = true;
        // window.uploadType = uploadType; // Se ancora necessario globalmente, ma meglio evitarlo

        this.elements.modal.setAttribute('aria-hidden', 'false');
        this.elements.modalContent.setAttribute('tabindex', '-1');
        this.elements.modalContent.focus();
        document.body.style.overflow = 'hidden';
    }

    /**
     * 🎯 Chiude la modale di upload.
     */
    public closeModal(): void {
        if (!this.isOpen || !this.elements.modal) {
            console.log('UploadModalManager: Attempted to close modal when not open or missing.');
            return;
        }

        this.elements.modal.classList.add('hidden');
        this.elements.modal.classList.remove('flex');
        this.isOpen = false;

        this.elements.modal.setAttribute('aria-hidden', 'true');

        // Sblocca scroll del body SOLO SE NESSUN'ALTRA MODALE è attiva
        // (Per ora, controlliamo solo la modale connect-wallet)
        const connectWalletModal = document.getElementById('connect-wallet-modal') as HTMLDivElement | null;
        if (!connectWalletModal || connectWalletModal.classList.contains('hidden')) {
            document.body.style.overflow = '';
        }


        // Resetta il form di upload tramite il gestore di UUM/EGI-Module
        if (window.fileUploadManager?.resetUploadForm) {
            window.fileUploadManager.resetUploadForm();
            console.log('UploadModalManager: Upload form reset via fileUploadManager.');
        } else {
            console.warn('UploadModalManager: window.fileUploadManager or resetUploadForm not found.');
        }

        if (this.lastFocusedElement) {
            this.lastFocusedElement.focus();
        }
        this.lastFocusedElement = null;
        console.log('Padmin D. Curtis: Upload modal closed.');

        // Gestione di window.redirectToURL se ancora presente e necessario
        // if (typeof window.redirectToURL === 'function') {
        //     console.warn('UploadModalManager: window.redirectToURL will be called.');
        //     window.redirectToURL();
        // }
    }

    /**
     * 🎯 Controlla se la modale è attualmente aperta.
     * @returns {boolean} True se la modale è aperta, false altrimenti.
     */
    public isModalOpen(): boolean {
        return this.isOpen;
    }
}

// NON ESPORTARE UNA FUNZIONE `initializeModal` CHE ASSEGNA A `window.globalModalManager`.
// L'istanza sarà creata e gestita da `main.ts`.