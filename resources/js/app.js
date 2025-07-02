// resources/js/app.js (versione semplificata - solo dipendenze globali)

/**
 * 📜 Oracode JavaScript Module: Global Dependencies Initializer
 * @version 2.0.0 (Simplified for Orchestrated Main)
 * @date 2025-07-02
 * @author Padmin D. Curtis (AI Partner OS2.0-Compliant) for Fabio Cherici
 * 🎯 Purpose: Setup only global dependencies - all initialization moved to main.ts
 * 🧱 Core Logic: Bootstrap → Editor → Polyfill → Global Libraries → Module Imports
 */

console.log('Inizializzazione di app.js (dependencies only)'); // Debugging

// --- 🔧 IMPORT BOOTSTRAP E CONFIGURAZIONI BASE ---
import './bootstrap';
console.log('bootstrap importato.'); // Debugging

// --- 📝 IMPORT EDITOR LEGALE ---
import './legal/editor'; // Importa il file editor.js
console.log('editor.js importato.'); // Debugging

// --- 🌐 IMPORT POLYFILL FETCH ---
import 'whatwg-fetch';
console.log('Polyfill whatwg-fetch importato.'); // Debugging

// --- 🍯 IMPORT E SETUP GLOBALE SWEETALERT2 ---
import Swal from 'sweetalert2';
window.Swal = Swal; // Rende disponibile globalmente per main.ts
console.log('SweetAlert2 importato e reso globale.'); // Debugging

// --- 📦 IMPORT E SETUP GLOBALE JQUERY ---
import $ from 'jquery';
window.$ = window.jQuery = $; // Rende disponibile globalmente per main.ts
console.log('jQuery importato e reso globale.'); // Debugging

// --- 🔄 IMPORT UTILITIES (solo import, inizializzazione in main.ts) ---
import { fetchTranslations, ensureTranslationsLoaded, getTranslation } from './utils/translations';
import { loadEnums, getEnum, isPendingStatus } from './utils/enums';
console.log('Utils per translations e enums importati (init in main.ts).'); // Debugging

// --- 🎮 IMPORT ANIMAZIONE (disponibile per main.ts) ---
import { initThreeAnimation } from './sfera-geodetica';
console.log('Modulo sfera-geodetica importato (init in main.ts).'); // Debugging

// --- 🏦 IMPORT MODULI WALLET (disponibili per main.ts) ---
import {
    RequestCreateNotificationWallet,
    RequestUpdateNotificationWallet,
    RequestWalletDonation,
} from './modules/notifications/init/request-notification-wallet-init';
import { DeleteProposalInvitation } from './modules/notifications/delete-proposal-invitation';
import { DeleteProposalWallet } from './modules/notifications/delete-proposal-wallet';
console.log('Moduli wallet importati (init in main.ts).'); // Debugging

// --- 📤 IMPORT ULTRA UPLOAD MANAGER (disponibile per main.ts) ---
import { initializeApp as initializeUltraUploadManager } from '/vendor/ultra/ultra-upload-manager/resources/ts/core/file_upload_manager.ts';
console.log('Modulo file_upload_manager importato (init in main.ts).'); // Debugging

// --- 🔔 IMPORT MODULI NOTIFICHE (auto-eseguiti o passive) ---
import './notification'; // Se auto-eseguito, rimane qui
import './modules/notifications/init/notification-response-init'; // Se auto-eseguito, rimane qui
console.log('Moduli notifiche importati.'); // Debugging

console.log('app.js setup complete (dependencies only - orchestration in main.ts).'); // Debugging

// --- 📚 DOCUMENTAZIONE POLYFILL ---
// Documentazione: https://github.com/github/fetch
