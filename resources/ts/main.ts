// File: resources/ts/main.ts (modifiche per integrare il sistema di prenotazione)

/**
 * 📜 Oracode TypeScript Module: Main Application Entry Point (FlorenceEGI Guest Layout)
 * @version 3.1.1 (Padmin - Corrected DOM Element Initialization Timing)
 * @date 2025-05-24
 * @author Padmin D. Curtis (for Fabio Cherici)
 */

// --- ⚙️ IMPORTAZIONI MODULI CORE ---
import { initializeAppConfig, AppConfig, appTranslate } from './config/appConfig';
import * as DOMElements from './dom/domElements'; // Ora importa anche initializeDOMReferences
import { getCsrfTokenTS } from './utils/csrf';
import { UploadModalManager, UploadModalDomElements } from './ui/uploadModalManager';
import likeUIManager from './ui/likeUIManager';

// --- 🛠️ IMPORTAZIONI FUNZIONALITÀ DAI MODULI DEDICATI ---
import {
    openSecureWalletModal,
    closeSecureWalletModal,
    handleSecureWalletSubmit
} from './features/auth/walletConnect';
import { getAuthStatus } from './features/auth/authService';
import {
    copyWalletAddress,
    handleDisconnect,
    toggleWalletDropdownMenu
} from './features/auth/walletDropdown';
import {
    toggleCollectionListDropdown
} from './features/collections/collectionUI';
import { toggleMobileMenu } from './features/mobile/mobileMenu';
import { updateNavbarUI } from './ui/navbarManager';
import { UEM_Client_TS_Placeholder as UEM } from './services/uemClientService';
import reservationFeature from './features/reservations/reservationFeature';
import reservationButtons from './features/reservations/reservationButtons';
import { NatanAssistant } from './components/natan-assistant';


// --- ✨ ISTANZE GLOBALI DEL MODULO MAIN ---
let mainAppConfig: AppConfig;
let mainUploadModalManager: UploadModalManager | null = null;

/**
 * 📜 Oracode Function: initializeApplication
 * 🎯 Funzione principale di inizializzazione dell'applicazione client-side.
 */
async function initializeApplication(): Promise<void> {
    try {
        // 1. Inizializza UEM per gestione errori
        if (UEM && typeof UEM.initialize === 'function') {
            // Assumendo che UEM.initialize possa essere async
            const uemInit = UEM.initialize();
            if (uemInit && typeof uemInit.then === 'function') {
                await uemInit;
            }
            console.log('Padmin Main: UEM Client Service initialized.');
        }

        // === LA MODIFICA CHIAVE È QUI ===
        // 2. Inizializza i riferimenti DOM (dopo DOMContentLoaded, prima del loro uso)
        DOMElements.initializeDOMReferences();
        // console.log('Padmin Main: DOM references acquired via initializeDOMReferences().');

        // 3. Carica configurazione dal server
        mainAppConfig = await initializeAppConfig();
        console.log(`${appTranslate('padminGreeting', mainAppConfig?.translations || {padminGreeting:'Padmin'})} Configuration loaded successfully.`);
        // console.debug('Padmin Main: AppConfig:', mainAppConfig);


        // 4. Conferma riferimenti DOM (era il tuo punto 3 - ora è più un check opzionale)
        DOMElements.confirmDOMReferencesLoaded(); // Puoi commentare/decommentare per debug
        console.log('Padmin Main: DOM references confirmation check complete.');


        // 5. Inizializza UploadModalManager (era il tuo punto 4)
        if (DOMElements.uploadModalEl && DOMElements.uploadModalCloseButtonEl && DOMElements.uploadModalContentEl) {
            const uploadModalDOMElements: UploadModalDomElements = {
                modal: DOMElements.uploadModalEl,
                closeButton: DOMElements.uploadModalCloseButtonEl,
                modalContent: DOMElements.uploadModalContentEl
            };
            mainUploadModalManager = new UploadModalManager(uploadModalDOMElements, mainAppConfig.csrf_token);
            console.log('Padmin Main: UploadModalManager initialized.');
        } else {
            const missingElements = [
                !DOMElements.uploadModalEl ? '#upload-modal' : null,
                !DOMElements.uploadModalCloseButtonEl ? '#close-upload-modal' : null,
                !DOMElements.uploadModalContentEl ? '#upload-container' : null,
            ].filter(Boolean).join(', ');
            console.error(`Padmin Main: Cannot initialize UploadModalManager - DOM elements missing: ${missingElements}`);
            UEM.handleClientError('CLIENT_INIT_FAIL_UPLOAD_MODAL_MAIN_TS', { reason: `DOM elements missing for UploadModal: ${missingElements}` });
        }

        // 6. Setup event listeners (era il tuo punto 5)
        setupEventListeners();
        // console.log('Padmin Main: Event listeners engaged (after setupEventListeners call).'); // Log spostato dentro setupEventListeners

        // 7. Aggiorna UI navbar (era il tuo punto 6)
        updateNavbarUI(mainAppConfig, DOMElements);
        console.log('Padmin Main: Initial navbar UI update performed.');

        // 8. Inizializza il sistema di like (era il tuo punto 7)
        if (likeUIManager && typeof likeUIManager.initialize === 'function') {
            likeUIManager.initialize(mainAppConfig);
            console.log('Padmin Main: Like system initialized.');
        } else {
            console.warn('Padmin Main: likeUIManager or its initialize method not found.');
        }


        // 9. Inizializza il sistema di prenotazione (era il tuo punto 8)
        if (reservationFeature && typeof reservationFeature.initialize === 'function') {
            await reservationFeature.initialize();
            console.log('Padmin Main: Reservation feature initialized.');
        } else {
            console.warn('Padmin Main: reservationFeature or its initialize method not found.');
        }

        // 10. Inizializza i bottoni di prenotazione (era il tuo punto 9)
        if (reservationButtons && typeof reservationButtons.initialize === 'function') {
            await reservationButtons.initialize();
            console.log('Padmin Main: Reservation buttons initialized.');
        } else {
            console.warn('Padmin Main: reservationButtons or its initialize method not found.');
        }

        // 11. Inizializza Natan Assistant (era il tuo punto 10)
        try {
            if (typeof NatanAssistant === 'function') { // Controlla se NatanAssistant è una classe/funzione
                const natanAssistant = new NatanAssistant();
                console.log('Padmin Main: Natan Assistant initialized.');
            } else {
                console.warn('Padmin Main: NatanAssistant is not a constructor or function.');
            }
        } catch (error) {
            console.error('Padmin Main: Error initializing Natan Assistant:', error);
            UEM.handleClientError('CLIENT_INIT_FAIL_NATAN_TS', { originalError: error instanceof Error ? error.message : String(error) });
        }

        console.log('Padmin Main: FlorenceEGI Client Initialization Sequence Complete.');

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
 */
function setupEventListeners(): void {
    console.log('Padmin Main: Attempting to setup event listeners...');

    // --- MODALE CONNESSIONE WALLET (Secret Link System) ---
    DOMElements.connectWalletButtonStdEl?.addEventListener('click', () => openSecureWalletModal(mainAppConfig, DOMElements, null));
    DOMElements.connectWalletButtonMobileEl?.addEventListener('click', () => openSecureWalletModal(mainAppConfig, DOMElements, null));
    DOMElements.closeConnectWalletButtonEl?.addEventListener('click', () => closeSecureWalletModal(DOMElements));
    DOMElements.connectWalletModalEl?.addEventListener('click', (e: MouseEvent) => {
        if (e.target === DOMElements.connectWalletModalEl) closeSecureWalletModal(DOMElements);
    });
    DOMElements.connectWalletFormEl?.addEventListener('submit', (e: Event) =>
        handleSecureWalletSubmit(e, mainAppConfig, DOMElements, mainUploadModalManager, UEM, () => {
            updateNavbarUI(mainAppConfig, DOMElements);
            if (reservationFeature && typeof reservationFeature.updateReservationButtonStates === 'function') {
                reservationFeature.updateReservationButtonStates();
            }
        })
    );

    // --- AZIONI CREATE EGI/COLLECTION ---
    DOMElements.createEgiGuestButtonsEl?.forEach(btn => btn.addEventListener('click', () => { const authStatus = getAuthStatus(mainAppConfig);
            if (authStatus === 'logged-in' || authStatus === 'connected') {
                mainUploadModalManager?.openModal('egi');
            } else {
                openSecureWalletModal(mainAppConfig, DOMElements, 'create-egi');
            }
        }));

    DOMElements.createCollectionGuestButtonsEl?.forEach(btn => btn.addEventListener('click', () => { const authStatus = getAuthStatus(mainAppConfig);
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
            updateNavbarUI(mainAppConfig, DOMElements);
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
            console.log('Padmin Main: Mobile menu button (from setupEventListeners) clicked. Element:', DOMElements.mobileMenuButtonEl); // LOG A
            toggleMobileMenu(DOMElements, mainAppConfig);
        });
    } else {
        console.warn('Padmin Main: mobileMenuButtonEl not found in setupEventListeners. Mobile menu click listener NOT attached.');
        // UEM.handleClientError('CLIENT_EVENT_SETUP_FAIL_MOBILE_BTN_TS', { detail: 'mobileMenuButtonEl is null in setupEventListeners' });
    }

    console.log('Padmin Main: Event listeners setup process complete.');
}

// --- PUNTO DI INGRESSO DELL'APPLICAZIONE ---
if (document.readyState === 'loading') {
    // console.log('Padmin Main: DOM not ready, deferring initializeApplication.');
    document.addEventListener('DOMContentLoaded', initializeApplication);
} else {
    // console.log('Padmin Main: DOM already ready, calling initializeApplication directly.');
    initializeApplication();
}
