// resources/js/app.js

console.log('Inizializzazione di app.js (inizio)'); // Debugging

import './bootstrap';
console.log('bootstrap importato.'); // Debugging

// Importa il polyfill whatwg-fetch
import 'whatwg-fetch';
console.log('Polyfill whatwg-fetch importato.'); // Debugging

// Importa SweetAlert2
import Swal from 'sweetalert2';
window.Swal = Swal; // Sintassi JS standard
console.log('SweetAlert2 importato e globale.'); // Debugging

// Importa la gestione del modale
// import { initializeModal } from '../ts/open-close-modal';
// initializeModal();

// Importa utils (translations, enums)
import { fetchTranslations, ensureTranslationsLoaded, getTranslation } from './utils/translations';
import { loadEnums, getEnum, isPendingStatus } from './utils/enums';
console.log('Utils per translations e enums importati.'); // Debugging


// Importa jQuery
import $ from 'jquery';
window.$ = window.jQuery = $; // Sintassi JS standard
console.log('jQuery importato e globale.'); // Debugging

// Importa e inizializza l'animazione Three.js (sfera geodetica)
import { initThreeAnimation } from './sfera-geodetica';
console.log('Modulo sfera-geodetica importato.'); // Debugging


// Importa e inizializza il File Upload Manager di Ultra
// Assumendo che initializeApp renda disponibile window.fileUploadManager
import { initializeApp as initializeUltraUploadManager } from '/vendor/ultra/ultra-upload-manager/resources/ts/core/file_upload_manager.ts'; // Mantieni questo import
console.log('Modulo file_upload_manager importato.'); // Debugging


// --- Listener DOMContentLoaded principali (esistenti nel tuo codice) ---

// Listener per l'animazione Three.js (dal tuo codice originale)
document.addEventListener('DOMContentLoaded', () => {
    // Controlla se ci sono elementi necessari per l'animazione sulla pagina
    if (document.getElementById('dynamic-3d-container') && document.getElementById('webgl-canvas')) {
      // Inizializza l'animazione
      console.log('Inizializzazione animazione Three.js (DOMContentLoaded).');
      initThreeAnimation();
    }

});

// --- Listener per l'inizializzazione Ultra Upload Manager E la logica Modale ---
// Abbiamo UNITO i due listener per garantire l'ordine
document.addEventListener('DOMContentLoaded', () => {
    // 1. Inizializza Ultra Upload Manager (come facevi prima)
    // La chiamata a initializeUltraUploadManager avviene qui
    initializeUltraUploadManager(); // La tua chiamata originale
    console.log('Ultra Upload Manager inizializzato (DOMContentLoaded).'); // Debugging

}, { once: true }); // Manteniamo { once: true } se initializeUltraUploadManager lo richiede


// Listener per i moduli Wallet (dal tuo codice originale)
let walletCreateInstance = null;
let walletUpdateInstance = null;
let walletDonationInstance = null;
import {
    RequestCreateNotificationWallet,
    RequestUpdateNotificationWallet,
    RequestWalletDonation,
} from './modules/notifications/init/request-notification-wallet-init';
document.addEventListener('DOMContentLoaded', () => {
    if (!walletCreateInstance) {
        walletCreateInstance = new RequestCreateNotificationWallet({ apiBaseUrl: '/notifications' });
        console.log(`🔍 Inizializzazione unica RequestCreateNotificationWallet (DOMContentLoaded)`);
    } else {
        console.warn(`⛔ Tentativo di inizializzazione multipla di RequestCreateNotificationWallet ignorato`);
    }

    if (!walletUpdateInstance) {
        walletUpdateInstance = new RequestUpdateNotificationWallet({ apiBaseUrl: '/notifications' });
        console.log(`🔍 Inizializzazione unica RequestUpdateNotificationWallet (DOMContentLoaded)`);
    } else {
        console.warn(`⛔ Tentativo di inizializzazione multipla di RequestUpdateNotificationWallet ignorato`);
    }

    if (!walletDonationInstance) {
        walletDonationInstance = new RequestWalletDonation({ apiBaseUrl: '/notifications' }); // Corretto nome classe? Era RequestWalletDonation
        console.log(`🔍 Inizializzazione unica RequestWalletDonation (DOMContentLoaded)`);
    } else {
        console.warn(`⛔ Tentativo di inizializzazione multipla di RequestWalletDonation ignorato`);
    }
});

// Listener per DeleteProposalInvitation (dal tuo codice originale)
let deleteProposalInvitationInstance = null;
import { DeleteProposalInvitation } from './modules/notifications/delete-proposal-invitation';
document.addEventListener('DOMContentLoaded', () => {
    if (!deleteProposalInvitationInstance) {
        deleteProposalInvitationInstance = new DeleteProposalInvitation({ apiBaseUrl: '/notifications' });
        console.log(`🔍 Inizializzazione unica DeleteProposalInvitation (DOMContentLoaded)`);
    } else {
        console.warn(`⛔ Tentativo di inizializzazione multipla di DeleteProposalInvitation ignorato`);
    }
});

// Listener per DeleteProposalWallet (dal tuo codice originale)
let deleteProposalWalletInstance = null;
import { DeleteProposalWallet } from './modules/notifications/delete-proposal-wallet';
document.addEventListener('DOMContentLoaded', () => {
    if (!deleteProposalWalletInstance) {
        deleteProposalWalletInstance = new DeleteProposalWallet({ apiBaseUrl: '/notifications' });
        console.log(`🔍 Inizializzazione unica DeleteProposalWallet (DOMContentLoaded)`);
    } else {
        console.warn(`⛔ Tentativo di inizializzazione multipla di DeleteProposalWallet ignorato`);
    }
});

// --- Rimuovi il listener separato per la logica Modale ---
// document.addEventListener('DOMContentLoaded', () => {
//     console.log('Inizializzazione logica modale di upload (DOMContentLoaded).');
//     initUploadModalLogic();
// });
// --- FINE RIMOZIONE ---


// Carica gli enum all'avvio (mantieni come nel tuo codice originale se è fuori listener)
loadEnums(); // <-- Questo è chiamato direttamente nel tuo codice originale, non in un listener
window.getEnum = getEnum; // Sintassi JS standard
window.isPendingStatus = isPendingStatus; // Sintassi JS standard
console.log('Enums caricati (fuori listener).'); // Debugging


// Carica le traduzioni all'avvio (mantieni come nel tuo codice originale se è fuori listener)
fetchTranslations(); // <-- Questo è chiamato direttamente nel tuo codice originale, non in un listener
window.getTranslation = getTranslation; // Sintassi JS standard
window.ensureTranslationsLoaded = ensureTranslationsLoaded; // Sintassi JS standard
console.log('Traduzioni avviate (fuori listener).'); // Debugging


// Import dei moduli notifiche (senza inizializzazioni dirette mostrate nel listener DOMContentLoaded fornito)
import './notification'; // Questo modulo esegue codice subito?
import './modules/notifications/init/notification-response-init'; // Questo modulo esegue codice subito?
// Se questi moduli devono essere inizializzati DOPO DOMContentLoaded, dovrai spostare le loro importazioni
// o chiamare le loro funzioni di inizializzazione dentro un listener DOMContentLoaded come gli altri.


console.log('app.js execution finished (initial phase - after imports).'); // Debugging


// // Documentazione: di window.fetch polyfill
// Documentazione: https://github.com/github/fetch;
