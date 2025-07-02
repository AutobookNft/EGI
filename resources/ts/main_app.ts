// File: resources/ts/main.ts (versione corretta con fix TypeScript)

/**
 * 📜 Oracode TypeScript Module: Main Application Entry Point (FlorenceEGI Guest Layout)
 * @version 4.2.0 (Surgical Fixes Applied)
 * @date 2025-07-01
 * @author Padmin D. Curtis (for Fabio Cherici)
 */

// --- ⚙️ IMPORTAZIONI MODULI CORE ---
// Usa il percorso corretto per puntare dalla cartella /ts alla cartella /js
import { fetchTranslations, ensureTranslationsLoaded, getTranslation } from '../js/utils/translations.js';
import { loadEnums, getEnum, isPendingStatus } from '../js/utils/enums.js';
import { initializeAppConfig, AppConfig, appTranslate } from './config/appConfig.js';
import * as DOMElements from './dom/domElements.js';
import { getCsrfTokenTS } from './utils/csrf.js';
import { UploadModalManager, UploadModalDomElements } from './ui/uploadModalManager.js';
import likeUIManager from './ui/likeUIManager.js';

// --- 🔧 IMPORTAZIONE TYPES PER CUSTOM EVENTS ---
import './types/customEvents.js'; // Questo estende DocumentEventMap

// --- 🛠️ IMPORTAZIONI FUNZIONALITÀ DAI MODULI DEDICATI ---
import {
    openSecureWalletModal,
    closeSecureWalletModal
} from './features/auth/walletConnect.js';
import { getAuthStatus } from './features/auth/authService.js';
import {
    copyWalletAddress,
    handleDisconnect,
    toggleWalletDropdownMenu
} from './features/auth/walletDropdown.js';
import {
    toggleCollectionListDropdown
} from './features/collections/collectionUI.js';
import { toggleMobileMenu } from './features/mobile/mobileMenu.js';
import { updateNavbarUI } from './ui/navbarManager.js';
import { UEM } from './services/uemClientService.js';
import reservationFeature from './features/reservations/reservationFeature.js';
import reservationButtons from './features/reservations/reservationButtons.js';
import { NatanAssistant } from './components/natan-assistant.js';


// --- ✨ ISTANZE GLOBALI DEL MODULO MAIN ---
let mainAppConfig: AppConfig;
let mainUploadModalManager: UploadModalManager | null = null;

// --- IMPOSTAZIONE HELPERS GLOBALI (Ripristina il comportamento di app.js) ---
fetchTranslations(); // Avvia il caricamento delle traduzioni
window.ensureTranslationsLoaded = ensureTranslationsLoaded; // <-- 2. AGGANCIA A WINDOW
window.getTranslation = getTranslation; // <-- 2. AGGANCIA A WINDOW

// Logica per gli enum (REINTEGRATA)
loadEnums(); // <-- Questo è chiamato direttamente nel tuo codice originale, non in un listener
window.getEnum = getEnum; // Sintassi JS standard
window.isPendingStatus = isPendingStatus; // Sintassi JS standard
console.log('Enums caricati (fuori listener).'); // Debugging
// --------------------------------------------------------------------------


/**
 * 📜 Oracode Function: initializeApplication
 * 🎯 Funzione principale di inizializzazione dell'applicazione client-side.
 * Updated per supportare il sistema FEGI.
 */
async function initializeApplication(): Promise<void> {
    try {
        // 1. Inizializza UEM per gestione errori
        if (UEM && typeof UEM.initialize === 'function') {
            await UEM.initialize();
            console.log('Padmin Main: UEM Client Service initialized.');
        }

        // 2. Inizializza i riferimenti DOM
        DOMElements.initializeDOMReferences();

        // document.addEventListener('open-wallet-modal', () => {
        //     openSecureWalletModal(mainAppConfig, DOMElements, null);
        // });

        // 3. Carica configurazione dal server - MODIFICA #1
        const fullConfigResponse = await initializeAppConfig();
        mainAppConfig = await initializeAppConfig(); // Estraiamo l'oggetto corretto
        console.log(`${appTranslate('padminGreeting', mainAppConfig.translations)} FEGI Configuration loaded successfully.`);

        // 4. Conferma riferimenti DOM
        DOMElements.confirmDOMReferencesLoaded();
        console.log('Padmin Main: DOM references confirmation check complete.');

        // 5. Inizializza UploadModalManager
        // if (DOMElements.uploadModalEl && DOMElements.uploadModalCloseButtonEl && DOMElements.uploadModalContentEl) {
        //     const uploadModalDOMElements: UploadModalDomElements = {
        //         modal: DOMElements.uploadModalEl,
        //         closeButton: DOMElements.uploadModalCloseButtonEl,
        //         modalContent: DOMElements.uploadModalContentEl
        //     };
        //     mainUploadModalManager = new UploadModalManager(uploadModalDOMElements, mainAppConfig.csrf_token);
        //     console.log('Padmin Main: UploadModalManager initialized.');
        // } else {
        //     const missingElements = [
        //         !DOMElements.uploadModalEl ? '#upload-modal' : null,
        //         !DOMElements.uploadModalCloseButtonEl ? '#close-upload-modal' : null,
        //         !DOMElements.uploadModalContentEl ? '#upload-container' : null,
        //     ].filter(Boolean).join(', ');
        //     console.error(`Padmin Main: Cannot initialize UploadModalManager - DOM elements missing: ${missingElements}`);
        //     UEM.handleClientError('CLIENT_INIT_FAIL_UPLOAD_MODAL_MAIN_TS', { reason: `DOM elements missing for UploadModal: ${missingElements}` });
        // }

        // 6. Setup event listeners (inclusi quelli FEGI)
        setupEventListeners();

        // 7. Aggiorna UI navbar - MODIFICA #2
        updateNavbarUI(mainAppConfig, DOMElements, UEM); // Passiamo UEM
        console.log('Padmin Main: Initial navbar UI update performed.');

        // 8. Inizializza il sistema di like
        if (likeUIManager && typeof likeUIManager.initialize === 'function') {
            likeUIManager.initialize(mainAppConfig);
            console.log('Padmin Main: Like system initialized.');
        } else {
            console.warn('Padmin Main: likeUIManager or its initialize method not found.');
        }

        // 9. Inizializza il sistema di prenotazione
        if (reservationFeature && typeof reservationFeature.initialize === 'function') {
            // await reservationFeature.initialize();
            console.log('Padmin Main: Reservation feature initialized.');
        } else {
            console.warn('Padmin Main: reservationFeature or its initialize method not found.');
        }

        // 10. Inizializza i bottoni di prenotazione
        if (reservationButtons && typeof reservationButtons.initialize === 'function') {
            await reservationButtons.initialize();
            console.log('Padmin Main: Reservation buttons initialized.');
        } else {
            console.warn('Padmin Main: reservationButtons or its initialize method not found.');
        }

        // 11. Inizializza Natan Assistant
        try {
            if (typeof NatanAssistant === 'function') {
                const natanAssistant = new NatanAssistant();
                console.log('Padmin Main: Natan Assistant initialized.');
            } else {
                console.warn('Padmin Main: NatanAssistant is not a constructor or function.');
            }
        } catch (error) {
            console.error('Padmin Main: Error initializing Natan Assistant:', error);
            UEM.handleClientError('CLIENT_INIT_FAIL_NATAN_TS', { originalError: error instanceof Error ? error.message : String(error) });
        }

        // 12. Setup FEGI-specific custom event listeners
        setupFegiCustomEvents();

        console.log('Padmin Main: FlorenceEGI FEGI Client Initialization Sequence Complete.');

    } catch (error) {
        console.error('Padmin Main: CRITICAL INITIALIZATION ERROR in initializeApplication:', error);
        const errorTitle = mainAppConfig?.translations?.initializationErrorTitle || 'Application Error';
        const errorText = mainAppConfig?.translations?.initializationErrorText || 'A critical error occurred while starting the application. Please try refreshing the page.';
        if (window.Swal) {
            window.Swal.fire({ icon: 'error', title: errorTitle, text: errorText, confirmButtonColor: '#ef4444' });
        } else {
            alert(`${errorTitle}\n${errorText}`);
        }
    }
}

/**
 * 📜 Oracode Function: setupEventListeners
 * 🎯 Associa tutti gli event listener agli elementi DOM interattivi.
 * Updated per supportare il sistema FEGI.
 */
function setupEventListeners(): void {
    console.log('Padmin Main: Attempting to setup FEGI event listeners...');

    // --- MODALE CONNESSIONE FEGI WALLET ---
    DOMElements.connectWalletButtonStdEl?.addEventListener('click', () => openSecureWalletModal(mainAppConfig, DOMElements, null));
    DOMElements.connectWalletButtonMobileEl?.addEventListener('click', () => openSecureWalletModal(mainAppConfig, DOMElements, null));
    DOMElements.closeConnectWalletButtonEl?.addEventListener('click', () => closeSecureWalletModal(DOMElements));
    DOMElements.connectWalletModalEl?.addEventListener('click', (e: MouseEvent) => {
        if (e.target === DOMElements.connectWalletModalEl) closeSecureWalletModal(DOMElements);
    });

    // --- AZIONI CREATE EGI/COLLECTION con controllo FEGI ---
    DOMElements.createEgiGuestButtonsEl?.forEach(btn => btn.addEventListener('click', () => {
        const authStatus = getAuthStatus(mainAppConfig);
        if (authStatus === 'logged-in' || authStatus === 'connected') {
            mainUploadModalManager?.openModal('egi');
        } else {
            openSecureWalletModal(mainAppConfig, DOMElements, 'create-egi');
        }
    }));

    DOMElements.createCollectionGuestButtonsEl?.forEach(btn => btn.addEventListener('click', () => {
        const authStatus = getAuthStatus(mainAppConfig);
        if (authStatus === 'logged-in') {
            window.location.href = mainAppConfig.routes.collectionsCreate;
        } else if (authStatus === 'connected') {
            // Mostra messaggio per registrazione completa
            if (window.Swal) {
                window.Swal.fire({
                    icon: 'info',
                    title: appTranslate('registrationRequiredTitle', mainAppConfig.translations),
                    text: appTranslate('registrationRequiredTextCollections', mainAppConfig.translations),
                    confirmButtonText: appTranslate('registerNowButton', mainAppConfig.translations),
                    showCancelButton: true,
                    cancelButtonText: appTranslate('laterButton', mainAppConfig.translations),
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#aaa'
                }).then((result: { isConfirmed: boolean }) => {
                    if (result.isConfirmed) {
                        window.location.href = mainAppConfig.routes.register;
                    }
                });
            } else {
                alert(appTranslate('registrationRequiredTextCollections', mainAppConfig.translations));
                window.location.href = mainAppConfig.routes.register;
            }
        } else {
            openSecureWalletModal(mainAppConfig, DOMElements, 'create-collection');
        }
    }));

    // --- DROPDOWN WALLET ---
    DOMElements.walletDropdownButtonEl?.addEventListener('click', () => toggleWalletDropdownMenu(mainAppConfig, DOMElements, UEM));
    DOMElements.walletCopyAddressButtonEl?.addEventListener('click', () => copyWalletAddress(mainAppConfig, DOMElements, UEM));
    DOMElements.walletDisconnectButtonEl?.addEventListener('click', () => {
        handleDisconnect(mainAppConfig, DOMElements, UEM, () => {
            updateNavbarUI(mainAppConfig, DOMElements, UEM); // Passa UEM anche qui
            if (reservationFeature && typeof reservationFeature.updateReservationButtonStates === 'function') {
                reservationFeature.updateReservationButtonStates();
            }
        });
    });

    // --- DROPDOWN COLLECTION LIST ---
    DOMElements.collectionListDropdownButtonEl?.addEventListener('click', () => toggleCollectionListDropdown(mainAppConfig, DOMElements, UEM));

    // --- MENU MOBILE ---
    if (DOMElements.mobileMenuButtonEl) {
        DOMElements.mobileMenuButtonEl.addEventListener('click', () => {
            console.log('Padmin Main: Mobile menu button (from setupEventListeners) clicked. Element:', DOMElements.mobileMenuButtonEl);
            toggleMobileMenu(DOMElements, mainAppConfig);
        });
    } else {
        console.warn('Padmin Main: mobileMenuButtonEl not found in setupEventListeners. Mobile menu click listener NOT attached.');
    }

    console.log('Padmin Main: FEGI Event listeners setup process complete.');
}

/**
 * 📜 Oracode Function: setupFegiCustomEvents
 * 🎯 Setup custom events specifici per il sistema FEGI
 * 🔧 TypeScript: Now properly typed with extended DocumentEventMap
 */
function setupFegiCustomEvents(): void {
    // Event listener per apertura upload modal da walletConnect.ts
    // Ora TypeScript riconosce il tipo corretto grazie all'estensione DocumentEventMap
    document.addEventListener('openUploadModal', (event) => {
        const customEvent = event as CustomEvent;
        const { type } = customEvent.detail;
        if (mainUploadModalManager && type) {
            mainUploadModalManager.openModal(type);
            console.log(`Padmin Main: Upload modal opened via custom event for type: ${type}`);
        }
    });
    // Event listener per aggiornamenti UI dopo connessione FEGI
    document.addEventListener('fegiConnectionComplete', (event) => {

        const customEvent = event as CustomEvent;
        updateNavbarUI(mainAppConfig, DOMElements, UEM); // Passa UEM anche qui
        if (reservationFeature && typeof reservationFeature.updateReservationButtonStates === 'function') {
            reservationFeature.updateReservationButtonStates();
        }
        console.log('Padmin Main: UI updated after FEGI connection');

        // Opzionalmente usa i dati dell'evento
        if (customEvent.detail?.walletAddress) {
            console.log(`Padmin Main: Connected wallet: ${customEvent.detail.walletAddress}`);
        }
    });

    console.log('Padmin Main: FEGI custom events setup complete.');
}

// --- PUNTO DI INGRESSO DELL'APPLICAZIONE ---
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeApplication);
} else {
    initializeApplication();
}
