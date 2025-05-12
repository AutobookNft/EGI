// File: resources/ts/main.ts

/**
 * 📜 Oracode TypeScript Module: Main Application Entry Point (FlorenceEGI Guest Layout)
 * Il cuore pulsante dell'applicazione client-side di FlorenceEGI.
 * Questo modulo orchestra l'inizializzazione della configurazione globale,
 * dei riferimenti agli elementi DOM, dei servizi cruciali (come UEM e UploadModalManager),
 * e imposta gli event listener principali per gestire le interazioni dell'utente
 * e aggiornare dinamicamente l'interfaccia utente della navbar e delle modali.
 *
 * @version 1.1.0 (Padmin Refactored for Modularity)
 * @date 2025-05-11
 * @author Padmin D. Curtis (for Fabio Cherici)
 */

// --- ⚙️ IMPORTAZIONI MODULI CORE ---
import { getAppConfig, AppConfig, appTranslate } from './config/appConfig';
import * as DOMElements from './dom/domElements'; // Importa tutti gli elementi DOM esportati
import { getCsrfTokenTS } from './utils/csrf';
import { UploadModalManager, UploadModalDomElements } from './ui/uploadModalManager';

// --- 🛠️ IMPORTAZIONI FUNZIONALITÀ DAI MODULI DEDICATI ---
import {
    // setWeakAuthWallet, // setWeakAuthWallet è chiamato da handleConnectWalletSubmit e handleDisconnect
    openConnectWalletModal,
    closeConnectWalletModal,
    handleConnectWalletSubmit
} from './features/auth/walletConnect'; // authService è usato internamente da walletConnect e walletDropdown

import { getAuthStatus } from './features/auth/authService'; // Funzione di utilità per ottenere l'indirizzo del wallet

import {
    copyWalletAddress,
    handleDisconnect,
    toggleWalletDropdownMenu
    // closeWalletDropdownMenu e handleWalletDropdownKeydown sono gestite internamente da toggleWalletDropdownMenu
} from './features/auth/walletDropdown';

import {
    // initializeUserCollectionState, // Chiamata da navbarManager
    toggleCollectionListDropdown
    // Altre funzioni di collectionUI sono gestite internamente o da initializeUserCollectionState
} from './features/collections/collectionUI';

import { toggleMobileMenu } from './features/mobile/mobileMenu';
import { updateNavbarUI } from './ui/navbarManager'; // Funzione UI principale

// Placeholder per UEM Client (o importazione reale)
import { UEM_Client_TS_Placeholder as UEM } from './services/uemClientService';

// --- ✨ ISTANZE GLOBALI DEL MODULO MAIN (Accessibili solo all'interno di main.ts) ---
let mainAppConfig: AppConfig;
let mainUploadModalManager: UploadModalManager | null = null;

/**
 * 📜 Oracode Function: initializeApplication
 * 🎯 Funzione principale di inizializzazione dell'applicazione client-side.
 * Chiamata una volta che il DOM è completamente caricato.
 * Orchestra il caricamento della configurazione, l'inizializzazione dei riferimenti DOM,
 * la creazione delle istanze dei manager (es. UploadModalManager),
 * l'impostazione degli event listener e l'aggiornamento iniziale dell'interfaccia utente.
 */
function initializeApplication(): void {
    // Tenta di caricare la configurazione; se fallisce, getAppConfig gestirà l'errore fatale.
    try {
        mainAppConfig = getAppConfig();
    } catch (error) {
        console.error("Padmin Main: Critical error during appConfig loading. Application cannot proceed.", error);
        return; // Interrompi l'esecuzione se la configurazione fallisce
    }
    console.log(`${appTranslate('padminGreeting', mainAppConfig.translations)} Initializing FlorenceEGI Frontend Orchestration...`);
    console.log('Padmin Main: App configuration loaded.');

    // Conferma (o inizializza se domElements.ts fosse strutturato diversamente) i riferimenti DOM.
    // Con l'approccio attuale di domElements.ts, gli elementi sono già disponibili dopo l'import.
    // La chiamata a confirmDOMReferencesLoaded è opzionale, più per un log di conferma.
    DOMElements.confirmDOMReferencesLoaded();
    console.log('Padmin Main: DOM references confirmed/acquired.');

    // Inizializza UEM Client (se ha una funzione di init)
    // if (UEM && typeof UEM.initialize === 'function') {
    //     UEM.initialize(/* eventuali parametri di config per UEM */);
    //     console.log('Padmin Main: UEM Client Service initialized.');
    // }

    // Inizializza UploadModalManager
    if (DOMElements.uploadModalEl && DOMElements.uploadModalCloseButtonEl && DOMElements.uploadModalContentEl) {
        const uploadModalDOMElements: UploadModalDomElements = {
            modal: DOMElements.uploadModalEl,
            closeButton: DOMElements.uploadModalCloseButtonEl,
            modalContent: DOMElements.uploadModalContentEl
        };
        mainUploadModalManager = new UploadModalManager(uploadModalDOMElements, getCsrfTokenTS());
        console.log('Padmin Main: Main UploadModalManager initialized.');
    } else {
        const missing = [
            !DOMElements.uploadModalEl ? '#upload-modal' : null,
            !DOMElements.uploadModalCloseButtonEl ? '#close-upload-modal' : null,
            !DOMElements.uploadModalContentEl ? (DOMElements.uploadModalContentEl === undefined ? 'uploadModalContentEl not in DOMElements.ts' : '#upload-container or content ID') : null
        ].filter(Boolean).join(', ');
        console.error(`Padmin Main Critical: Cannot init UploadModalManager. Missing DOM elements: ${missing}`);
        UEM.handleClientError('CLIENT_INIT_FAIL_UPLOAD_MODAL_MAIN', { reason: `DOM elements missing: ${missing}` });
    }

    // Imposta tutti gli event listener principali dell'applicazione.
    setupEventListeners();
    console.log('Padmin Main: Main event listeners engaged.');

    // Esegui il primo aggiornamento dell'interfaccia utente della navbar.
    // Questo leggerà lo stato di autenticazione e configurerà la UI di conseguenza.
    updateNavbarUI(mainAppConfig, DOMElements);
    console.log('Padmin Main: Initial Navbar UI update performed.');

    console.log(`${appTranslate('padminReady', mainAppConfig.translations)} FlorenceEGI client operational. Codice, Anima, Fuoco!`);
}

/**
 * 📜 Oracode Function: setupEventListeners
 * 🎯 Associa tutti gli event listener agli elementi DOM interattivi.
 * Questa funzione è chiamata una volta durante l'inizializzazione dell'applicazione.
 * Utilizza le funzioni importate dai moduli dedicati per gestire la logica specifica di ogni evento.
 */
function setupEventListeners(): void {
    // Modale Connessione Wallet: Apertura e Chiusura
    DOMElements.connectWalletButtonStdEl?.addEventListener('click', () =>
        openConnectWalletModal(mainAppConfig, DOMElements, null)
    );
    DOMElements.connectWalletButtonMobileEl?.addEventListener('click', () =>
        openConnectWalletModal(mainAppConfig, DOMElements, null)
    );
    DOMElements.closeConnectWalletButtonEl?.addEventListener('click', () => // Bottone "X" della modale connect
        closeConnectWalletModal(DOMElements)
    );
    DOMElements.connectWalletModalEl?.addEventListener('click', (e: MouseEvent) => { // Click sul backdrop per chiudere
        if (e.target === DOMElements.connectWalletModalEl) closeConnectWalletModal(DOMElements);
    });
    DOMElements.connectWalletFormEl?.addEventListener('submit', (e: Event) => // Submit del form di connessione
        handleConnectWalletSubmit(e, mainAppConfig, DOMElements, mainUploadModalManager, UEM, () => updateNavbarUI(mainAppConfig, DOMElements))
    );

    // Azioni "Create EGI" e "Create Collection" per utenti Guest
    DOMElements.createEgiGuestButtonsEl?.forEach(btn => btn.addEventListener('click', () => {
        const authStatus = getAuthStatus(mainAppConfig);
        if (authStatus === 'logged-in' || authStatus === 'connected') {
            mainUploadModalManager?.openModal('egi'); // Apri modale upload EGI
        } else {
            openConnectWalletModal(mainAppConfig, DOMElements, 'create-egi'); // Apri modale connect
        }
    }));

    DOMElements.createCollectionGuestButtonsEl?.forEach(btn => btn.addEventListener('click', () => {
        const authStatus = getAuthStatus(mainAppConfig);
        if (authStatus === 'logged-in') {
            window.location.href = mainAppConfig.routes.collectionsCreate; // Redirect diretto
        } else if (authStatus === 'connected') { // Solo connesso, non loggato, serve registrazione
            if (window.Swal) {
                window.Swal.fire({
                    icon: 'info',
                    title: appTranslate('registrationRequiredTitle', mainAppConfig.translations),
                    text: appTranslate('registrationRequiredTextCollections', mainAppConfig.translations),
                    confirmButtonText: appTranslate('registerNowButton', mainAppConfig.translations),
                    showCancelButton: true,
                    cancelButtonText: appTranslate('laterButton', mainAppConfig.translations),
                    confirmButtonColor: '#3085d6', // Esempio di colore Oracode-like
                    cancelButtonColor: '#aaa'
                }).then((result: { isConfirmed: boolean }) => {
                    if (result.isConfirmed) window.location.href = mainAppConfig.routes.register;
                });
            } else { // Fallback se Swal non è disponibile
                alert(appTranslate('registrationRequiredTextCollections', mainAppConfig.translations));
                window.location.href = mainAppConfig.routes.register;
            }
        } else { // Né connesso né loggato
            openConnectWalletModal(mainAppConfig, DOMElements, 'create-collection');
        }
    }));

    // Dropdown Wallet (Desktop)
    DOMElements.walletDropdownButtonEl?.addEventListener('click', () =>
        toggleWalletDropdownMenu(mainAppConfig, DOMElements, UEM)
    );
    DOMElements.walletCopyAddressButtonEl?.addEventListener('click', () =>
        copyWalletAddress(mainAppConfig, DOMElements, UEM)
    );
    DOMElements.walletDisconnectButtonEl?.addEventListener('click', () =>
        handleDisconnect(mainAppConfig, DOMElements, UEM, () => updateNavbarUI(mainAppConfig, DOMElements))
    );
    // I listener per chiudere il dropdown del wallet (click esterno, Escape) sono gestiti internamente da walletDropdown.ts

    // Dropdown "My Galleries" / Collection List
    DOMElements.collectionListDropdownButtonEl?.addEventListener('click', () =>
        toggleCollectionListDropdown(mainAppConfig, DOMElements, UEM)
    );
    // I listener per gli item, click esterno, Escape sono gestiti internamente da collectionUI.ts

    // Menu Mobile
    DOMElements.mobileMenuButtonEl?.addEventListener('click', () =>
        toggleMobileMenu(DOMElements, mainAppConfig) // Passa config per UEM error messages
    );

    // NOTA: Il listener per DOMElements.closeUploadModalButtonEl non è più qui.
    // È gestito internamente da UploadModalManager.ts, che riceve quell'elemento nel costruttore.
}


// --- PUNTO DI INGRESSO DELL'APPLICAZIONE ---
if (document.readyState === 'loading') {
    // Il DOM non è ancora pronto, attendi l'evento DOMContentLoaded.
    document.addEventListener('DOMContentLoaded', initializeApplication);
} else {
    // Il DOM è già pronto, esegui immediatamente l'inizializzazione.
    initializeApplication();
}