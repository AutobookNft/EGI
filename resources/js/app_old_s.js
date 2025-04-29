console.log('Inizializzazione di app.js');

import './bootstrap';

// Importa il polyfill whatwg-fetch per garantire la compatibilità della funzione fetch con tutti i browser
// -------------------------------------------------------------
// Polyfill per la funzione fetch
// -------------------------------------------------------------
import 'whatwg-fetch';

// (Un polyfill è un pezzo di codice (solitamente JavaScript) che fornisce una funzionalità moderna in browser più vecchi che non la supportano nativamente.)
// La funzione fetch() è un meccanismo basato su Promise per effettuare richieste web a livello programmatico nel browser.
// Questo progetto è un polyfill che implementa un sottoinsieme della specifica Fetch standard, sufficiente a rendere
// fetch una valida alternativa alla maggior parte degli utilizzi di XMLHttpRequest nelle applicazioni web tradizionali
// -------------------------------------------------------------
// La funzione fetch è un'API moderna per effettuare richieste HTTP
// che non è supportata da tutti i browser, specialmente le versioni più vecchie.
// Il polyfill whatwg-fetch fornisce un'implementazione della funzione fetch
// che funziona anche su browser più datati, come Internet Explorer 11.
//
// Includendo questo import, assicuriamo che il nostro codice che utilizza fetch
// possa funzionare correttamente in tutti i browser, migliorando la compatibilità
// e l'esperienza utente. È importante includerlo qui nel file principale
// per essere sicuri che il polyfill sia caricato una volta sola e sia disponibile
// in tutto il nostro progetto JavaScript.
//

import './notification';
import Swal from 'sweetalert2';
window.Swal = Swal;


import { fetchTranslations, ensureTranslationsLoaded, getTranslation } from './utils/translations';
import { loadEnums, getEnum, isPendingStatus } from './utils/enums';

import $ from 'jquery';
window.$ = window.jQuery = $;

import { initThreeAnimation } from './sfera-geodetica';

document.addEventListener('DOMContentLoaded', () => {
    // Controlla se ci sono elementi necessari per l'animazione sulla pagina
    if (document.getElementById('dynamic-3d-container') && document.getElementById('webgl-canvas')) {
      // Inizializza l'animazione
      console.log('Inizializzazione animazione Three.js...');
      initThreeAnimation();
    }
});

console.log('THREE è stato caricato e reso disponibile globalmente', window.THREE);

// Carica gli enum all'avvio
loadEnums();
window.getEnum = getEnum;
window.isPendingStatus = isPendingStatus;

// Carica le traduzioni all'avvio
fetchTranslations();
window.getTranslation = getTranslation;
window.ensureTranslationsLoaded = ensureTranslationsLoaded;

import { initializeApp } from '/vendor/ultra/ultra-upload-manager/resources/ts/core/file_upload_manager.ts';
document.addEventListener('DOMContentLoaded', initializeApp, { once: true });

// Inizializza i moduli wallet come singletons
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
        console.log(`🔍 Inizializzazione unica RequestCreateNotificationWallet`);
    } else {
        console.warn(`⛔ Tentativo di inizializzazione multipla di RequestCreateNotificationWallet ignorato`);
    }

    if (!walletUpdateInstance) {
        walletUpdateInstance = new RequestUpdateNotificationWallet({ apiBaseUrl: '/notifications' });
        console.log(`🔍 Inizializzazione unica RequestUpdateNotificationWallet`);
    } else {
        console.warn(`⛔ Tentativo di inizializzazione multipla di RequestUpdateNotificationWallet ignorato`);
    }

    if (!walletDonationInstance) {
        walletDonationInstance = new RequestWalletDonation({ apiBaseUrl: '/notifications' });
        console.log(`🔍 Inizializzazione unica RequestWalletDonation`);
    } else {
        console.warn(`⛔ Tentativo di inizializzazione multipla di RequestWalletDonation ignorato`);
    }
});

// Inizializza DeleteProposalInvitation come singleton
let deleteProposalInvitationInstance = null;
import { DeleteProposalInvitation } from './modules/notifications/delete-proposal-invitation';

document.addEventListener('DOMContentLoaded', () => {
    if (!deleteProposalInvitationInstance) {
        deleteProposalInvitationInstance = new DeleteProposalInvitation({ apiBaseUrl: '/notifications' });
        console.log(`🔍 Inizializzazione unica DeleteProposalInvitation`);
    } else {
        console.warn(`⛔ Tentativo di inizializzazione multipla di DeleteProposalInvitation ignorato`);
    }
});

// Inizializza DeleteProposalWallet come singleton
let deleteProposalWalletInstance = null;
import { DeleteProposalWallet } from './modules/notifications/delete-proposal-wallet';

document.addEventListener('DOMContentLoaded', () => {
    if (!deleteProposalWalletInstance) {
        deleteProposalWalletInstance = new DeleteProposalWallet({ apiBaseUrl: '/notifications' });
        console.log(`🔍 Inizializzazione unica DeleteProposalWallet`);
    } else {
        console.warn(`⛔ Tentativo di inizializzazione multipla di DeleteProposalWallet ignorato`);
    }
});


// Import dei moduli notifiche (senza inizializzazioni dirette)
import './modules/notifications/init/notification-response-init';

// // Documentazione: di window.fetch polyfill
// Documentazione: https://github.com/github/fetch;
