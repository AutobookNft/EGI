// File: resources/ts/main.ts

/**
 * 📜 Oracode TypeScript Module: Main Application Entry Point (FlorenceEGI Guest Layout)
 * Il cuore pulsante dell'applicazione client-side di FlorenceEGI.
 * Questo modulo orchestra l'inizializzazione della configurazione globale,
 * dei riferimenti agli elementi DOM, dei servizi cruciali (come UEM e UploadModalManager),
 * e imposta gli event listener principali per gestire le interazioni dell'utente
 * e aggiornare dinamicamente l'interfaccia utente della navbar e delle modali.
 *
 * @version 3.0.0 (Async Config Implementation)
 * @date 2025-05-13
 * @author Padmin D. Curtis (for Fabio Cherici)
 */

// --- ⚙️ IMPORTAZIONI MODULI CORE ---
import { initializeAppConfig, AppConfig, appTranslate } from './config/appConfig';
import * as DOMElements from './dom/domElements';
import { getCsrfTokenTS } from './utils/csrf';
import { UploadModalManager, UploadModalDomElements } from './ui/uploadModalManager';
import likeUIManager from './ui/likeUIManager';

// --- 🛠️ IMPORTAZIONI FUNZIONALITÀ DAI MODULI DEDICATI ---
// NUOVO: Importa il modulo wallet secure invece del vecchio
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

// UEM Client
import { UEM_Client_TS_Placeholder as UEM } from './services/uemClientService';

// --- ✨ ISTANZE GLOBALI DEL MODULO MAIN ---
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
async function initializeApplication(): Promise<void> {
    try {


        // 1. Inizializza UEM per gestione errori
        if (UEM && typeof UEM.initialize === 'function') {
            await UEM.initialize();
            console.log('Padmin Main: UEM Client Service initialized.');
        }

        // 2. Carica configurazione dal server
        mainAppConfig = await initializeAppConfig();
        console.log(`${appTranslate('padminGreeting')} Configuration loaded successfully.`);

        // 3. Conferma riferimenti DOM
        DOMElements.confirmDOMReferencesLoaded();
        console.log('Padmin Main: DOM references confirmed.');

        // 4. Inizializza UploadModalManager
        if (DOMElements.uploadModalEl && DOMElements.uploadModalCloseButtonEl && DOMElements.uploadModalContentEl) {
            const uploadModalDOMElements: UploadModalDomElements = {
                modal: DOMElements.uploadModalEl,
                closeButton: DOMElements.uploadModalCloseButtonEl,
                modalContent: DOMElements.uploadModalContentEl
            };
            mainUploadModalManager = new UploadModalManager(uploadModalDOMElements, mainAppConfig.csrf_token);
            console.log('Padmin Main: UploadModalManager initialized.');
        } else {
            console.error('Padmin Main: Cannot initialize UploadModalManager - DOM elements missing.');
        }

        // 5. Setup event listeners
        setupEventListeners();
        console.log('Padmin Main: Event listeners engaged.');

        // 6. Aggiorna UI navbar
        updateNavbarUI(mainAppConfig, DOMElements);
        console.log('Padmin Main: Initial navbar UI update performed.');

        // 7. Inizializza il sistema di like
        likeUIManager.initialize(mainAppConfig);
        console.log('Padmin Main: Like system initialized.');

        console.log(`${appTranslate('padminReady')} FlorenceEGI client operational.`);

    } catch (error) {
        console.error('Padmin Main: Critical initialization error:', error);

        // Mostra errore all'utente se UEM non è disponibile
        if (window.Swal) {
            window.Swal.fire({
                icon: 'error',
                title: 'Initialization Error',
                text: 'Failed to initialize application. Please refresh the page.',
                confirmButtonColor: '#ef4444'
            });
        } else {
            alert('Failed to initialize application. Please refresh the page.');
        }
    }
}

/**
 * 📜 Oracode Function: setupEventListeners
 * 🎯 Associa tutti gli event listener agli elementi DOM interattivi.
 * Utilizza le funzioni importate dai moduli dedicati per gestire la logica specifica di ogni evento.
 * AGGIORNATO: Usa il nuovo sistema wallet secure con Secret Link
 */
function setupEventListeners(): void {
    // --- MODALE CONNESSIONE WALLET (Secret Link System) ---
    // Desktop wallet connect button
    DOMElements.connectWalletButtonStdEl?.addEventListener('click', () =>
        openSecureWalletModal(mainAppConfig, DOMElements, null)
    );

    // Mobile wallet connect button
    DOMElements.connectWalletButtonMobileEl?.addEventListener('click', () =>
        openSecureWalletModal(mainAppConfig, DOMElements, null)
    );

    // Close button della modale
    DOMElements.closeConnectWalletButtonEl?.addEventListener('click', () =>
        closeSecureWalletModal(DOMElements)
    );

    // Click sul backdrop per chiudere
    DOMElements.connectWalletModalEl?.addEventListener('click', (e: MouseEvent) => {
        if (e.target === DOMElements.connectWalletModalEl) {
            closeSecureWalletModal(DOMElements);
        }
    });

    // Submit del form di connessione
    DOMElements.connectWalletFormEl?.addEventListener('submit', (e: Event) =>
        handleSecureWalletSubmit(
            e,
            mainAppConfig,
            DOMElements,
            mainUploadModalManager,
            UEM,
            () => updateNavbarUI(mainAppConfig, DOMElements)
        )
    );

    // --- AZIONI CREATE EGI/COLLECTION ---
    // Create EGI buttons
    DOMElements.createEgiGuestButtonsEl?.forEach(btn =>
        btn.addEventListener('click', () => {
            const authStatus = getAuthStatus(mainAppConfig);
            if (authStatus === 'logged-in' || authStatus === 'connected') {
                mainUploadModalManager?.openModal('egi');
            } else {
                openSecureWalletModal(mainAppConfig, DOMElements, 'create-egi');
            }
        })
    );

    // Create Collection buttons
    DOMElements.createCollectionGuestButtonsEl?.forEach(btn =>
        btn.addEventListener('click', () => {
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
        })
    );

    // --- DROPDOWN WALLET ---
    DOMElements.walletDropdownButtonEl?.addEventListener('click', () =>
        toggleWalletDropdownMenu(mainAppConfig, DOMElements, UEM)
    );

    DOMElements.walletCopyAddressButtonEl?.addEventListener('click', () =>
        copyWalletAddress(mainAppConfig, DOMElements, UEM)
    );

    DOMElements.walletDisconnectButtonEl?.addEventListener('click', () =>
        handleDisconnect(mainAppConfig, DOMElements, UEM, () => updateNavbarUI(mainAppConfig, DOMElements))
    );

    // --- DROPDOWN COLLECTION LIST ---
    DOMElements.collectionListDropdownButtonEl?.addEventListener('click', () =>
        toggleCollectionListDropdown(mainAppConfig, DOMElements, UEM)
    );

    // --- MENU MOBILE ---
    DOMElements.mobileMenuButtonEl?.addEventListener('click', () =>
        toggleMobileMenu(DOMElements, mainAppConfig)
    );

    console.log('Padmin Main: Event listeners setup complete.');
}

// --- PUNTO DI INGRESSO DELL'APPLICAZIONE ---
if (document.readyState === 'loading') {
    console.log('Padmin Main: Starting application initialization...');
    document.addEventListener('DOMContentLoaded', initializeApplication);
} else {
    initializeApplication();
}
