// File: resources/ts/ui/uploadModalManager.ts (Coordinamento Ultra Upload Manager Fixato)

/**
 * 📜 Oracode TypeScript Module: UploadModalManager
 * Gestisce specificamente l'apertura, la chiusura e la logica associata
 * per la modale di UPLOAD (#upload-modal) di FlorenceEGI.
 *
 * @version 2.1.0-ts (Ultra Coordination Fixed)
 * @date 2025-07-02
 * @author Padmin D. Curtis (AI Partner OS2.0-Compliant) for Fabio Cherici
 */

// --- 💎 IMPORTAZIONI TIPI E UTILITIES ---
import { ServerErrorResponse } from "../config/appConfig";

// --- 🔗 INTERFACCIA PER GLI ELEMENTI DOM NECESSARI ---
export interface UploadModalDomElements {
    modal: HTMLDivElement;
    closeButton: HTMLButtonElement; // Il bottone "X" o "Return" dentro la modale
    modalContent: HTMLDivElement; // L'area di contenuto principale della modale
}

// --- 📤 IMPORTAZIONI ULTRA UPLOAD MANAGER (per coordinamento diretto) ---
import { initializeApp as initializeUltraUploadManager } from '/vendor/ultra/ultra-upload-manager/resources/ts/core/file_upload_manager';

// Riferimenti globali semplificati (se ancora necessari)
declare global {
    interface Window {
        uploadType?: string;
        allowedExtensions?: string[];
        allowedMimeTypes?: string[];
        maxSize?: number;
        envMode?: string;
        uploadLimits?: any;
        cancelConfirmation?: string;
        uploadStatusWaiting?: string;
        invalidFilesTitle?: string;
        invalidFilesMessage?: string;
        checkFilesGuide?: string;
        okButton?: string;
        uploadFiniscedText?: string;
        allowedExtensionsMessage?: string;
    }
}

export class UploadModalManager {
    private elements: UploadModalDomElements;
    private isOpen: boolean;
    private csrfToken: string;
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
        console.log('Padmin D. Curtis: UploadModalManager instance created with Ultra coordination.');
    }

    private initializeEventListeners(): void {
        if (!this.elements.modal || !this.elements.closeButton || !this.elements.modalContent) {
            console.warn('UploadModalManager: Critical DOM elements missing. Modal may not function.');
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

        // Listener per ESC key
        document.addEventListener('keydown', (event: KeyboardEvent) => {
            if (event.key === 'Escape' && this.isOpen) {
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
     * 🎯 Carica le configurazioni necessarie per Ultra Upload Manager.
     */
    private async loadUltraUploadConfig(): Promise<void> {
        try {
            console.log('UploadModalManager: Loading Ultra Upload Manager configuration...');

            // Ultra Upload Manager chiama già /api/system/upload-limits da solo
            // Qui dobbiamo solo fornire le configurazioni che non vengono da lì

            // Configurazioni che Ultra Upload Manager si aspetta
            window.allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'txt'];
            window.allowedMimeTypes = [
                'image/jpeg', 'image/jpg', 'image/png', 'image/gif',
                'application/pdf', 'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'text/plain'
            ];
            window.maxSize = 10 * 1024 * 1024; // 10MB - verrà sovrascritto da upload-limits se necessario
            window.envMode = 'production'; // O 'local' se in sviluppo

            // Messaggi di testo che Ultra Upload Manager si aspetta
            (window as any).cancelConfirmation = 'Sei sicuro di voler annullare il caricamento?';
            (window as any).uploadStatusWaiting = 'Stato Upload: In attesa...';
            (window as any).invalidFilesTitle = 'File Non Validi Rilevati';
            (window as any).invalidFilesMessage = 'I seguenti file non possono essere caricati';
            (window as any).checkFilesGuide = 'Controlla tipi di file, dimensioni e nomi.';
            (window as any).okButton = 'OK';
            (window as any).uploadFiniscedText = 'Upload completato con successo!';
            (window as any).allowedExtensionsMessage = 'Estensioni consentite: ' + window.allowedExtensions.join(', ');

            console.log('UploadModalManager: Ultra Upload Manager configuration loaded successfully.');
            console.log('Config loaded:', {
                allowedExtensions: window.allowedExtensions,
                allowedMimeTypes: window.allowedMimeTypes,
                maxSize: window.maxSize,
                envMode: window.envMode
            });

        } catch (error) {
            console.error('UploadModalManager: Failed to load Ultra Upload Manager configuration:', error);

            // Configurazioni di emergenza per non bloccare tutto
            window.allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            window.allowedMimeTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            window.maxSize = 10 * 1024 * 1024;
            window.envMode = 'production';

            console.warn('UploadModalManager: Using fallback configuration.');
        }
    }

    /**
     * 🎯 Gestisce l'apertura della modale DOPO un check di autorizzazione.
     * @param {string} [uploadType='egi'] Il tipo di upload.
     * @deprecated Se il check di autorizzazione viene fatto prima di chiamare openModal().
     */
    public async openModalWithAuthCheck(uploadType: string = 'egi'): Promise<void> {
        console.log(`UploadModalManager: openModalWithAuthCheck called. Type: ${uploadType}. Checking auth...`);
        try {
            const response = await fetch('/api/check-upload-authorization', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                },
            });

            if (!response.ok) {
                const errorData: ServerErrorResponse = await response.json().catch(() => ({
                    error: 'HTTP_ERROR',
                    message: `Auth check HTTP error ${response.status}`
                }));
                console.error('UploadModalManager: Auth check failed.', errorData);
                alert(errorData.message || 'Authorization check failed.');
                return;
            }

            const result = await response.json();
            if (result.authorized) {
                this.openModal(uploadType);
            } else {
                console.warn('UploadModalManager: User not authorized for upload.', result);
                alert('You are not authorized to perform this action.');
            }
        } catch (error: any) {
            console.error('UploadModalManager: Error during authorization check:', error);
            alert('Could not verify authorization. Please try again.');
        }
    }

    /**
     * 🎯 Apre la modale di upload e inizializza Ultra Upload Manager.
     * Assume che l'autorizzazione sia già stata verificata dal chiamante.
     * @param {string} [uploadType='egi'] Il tipo di upload.
     */
    public async openModal(uploadType: string = 'egi'): Promise<void> {
        if (this.isOpen || !this.elements.modal || !this.elements.modalContent) {
            console.warn('UploadModalManager: Attempted to open modal when already open or elements missing.');
            return;
        }

        console.log(`Padmin D. Curtis: Opening upload modal programmatically. Type: ${uploadType}`);

        this.lastFocusedElement = document.activeElement as HTMLElement | null;

        // Assicurati che la modale di connessione wallet sia chiusa
        const connectWalletModal = document.getElementById('connect-wallet-modal') as HTMLDivElement | null;
        if (connectWalletModal && !connectWalletModal.classList.contains('hidden')) {
            const closeButton = connectWalletModal.querySelector<HTMLButtonElement>('#close-connect-wallet-modal');
            if (closeButton) closeButton.click();
            else connectWalletModal.classList.add('hidden');
        }

        // Mostra la modale prima di inizializzare Ultra Upload Manager
        this.elements.modal.classList.remove('hidden');
        this.elements.modal.classList.add('flex');
        this.elements.modalContent.dataset.uploadType = uploadType;
        this.isOpen = true;

        // Setup accessibilità
        this.elements.modal.setAttribute('aria-hidden', 'false');
        this.elements.modalContent.setAttribute('tabindex', '-1');
        this.elements.modalContent.focus();
        document.body.style.overflow = 'hidden';

        // --- 🔄 INIZIALIZZAZIONE SINCRONIZZATA ULTRA UPLOAD MANAGER ---
        try {
            console.log('UploadModalManager: Initializing Ultra Upload Manager now that modal is open...');

            // PRIMO: Carica configurazioni necessarie
            await this.loadUltraUploadConfig();

            // SECONDO: Imposta uploadType globalmente
            window.uploadType = uploadType;

            // TERZO: Inizializza Ultra Upload Manager ORA che tutto è pronto
            await initializeUltraUploadManager();

            // QUARTO: Trigger configurazione loaded per sicurezza
            const configLoadedEvent = new CustomEvent('configLoaded');
            document.dispatchEvent(configLoadedEvent);

            console.log('UploadModalManager: Ultra Upload Manager initialization completed successfully.');
        } catch (error) {
            console.error('UploadModalManager: Ultra Upload Manager initialization failed:', error);
            // Non chiudere la modale - l'utente può provare a caricare comunque
        }

        console.log('Padmin D. Curtis: Upload modal opened with synchronized Ultra coordination.');
    }

    /**
     * 🎯 Chiude la modale di upload con cleanup semplificato.
     */
    public closeModal(): void {
        if (!this.isOpen || !this.elements.modal) {
            console.log('UploadModalManager: Attempted to close modal when not open or missing.');
            return;
        }

        console.log('Padmin D. Curtis: Closing upload modal...');

        // --- 🎨 UI CLEANUP ---
        this.elements.modal.classList.add('hidden');
        this.elements.modal.classList.remove('flex');
        this.isOpen = false;

        this.elements.modal.setAttribute('aria-hidden', 'true');

        // Sblocca scroll del body SOLO se nessun'altra modale è attiva
        const connectWalletModal = document.getElementById('connect-wallet-modal') as HTMLDivElement | null;
        if (!connectWalletModal || connectWalletModal.classList.contains('hidden')) {
            document.body.style.overflow = '';
        }

        // Focus management
        if (this.lastFocusedElement) {
            this.lastFocusedElement.focus();
        }
        this.lastFocusedElement = null;

        // --- 🔄 SIMPLE UPLOAD CLEANUP ---
        // Reset semplice dell'UI di upload invece di chiamare funzioni problematiche
        try {
            // Trova e resetta gli elementi UI dell'upload se esistono
            const uploadContainer = this.elements.modalContent.querySelector('#upload-container');
            const progressBar = uploadContainer?.querySelector('.progress-bar') as HTMLElement;
            const progressText = uploadContainer?.querySelector('.progress-text') as HTMLElement;
            const fileCollection = uploadContainer?.querySelector('#collection') as HTMLElement;

            if (progressBar) progressBar.style.width = '0%';
            if (progressText) progressText.textContent = '';
            if (fileCollection) fileCollection.innerHTML = '';

            // Reset dei file input se presenti
            const fileInputs = uploadContainer?.querySelectorAll('input[type="file"]') as NodeListOf<HTMLInputElement>;
            fileInputs?.forEach(input => input.value = '');

            console.log('UploadModalManager: Simple upload UI cleanup completed.');
        } catch (error) {
            console.warn('UploadModalManager: Upload cleanup had minor issues:', error);
        }

        // Cleanup globali se necessario
        delete window.uploadType;

        console.log('Padmin D. Curtis: Upload modal closed with simple cleanup.');
    }

    /**
     * 🎯 Controlla se la modale è attualmente aperta.
     * @returns {boolean} True se la modale è aperta, false altrimenti.
     */
    public isModalOpen(): boolean {
        return this.isOpen;
    }
}
