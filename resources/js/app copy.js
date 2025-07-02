// resources/js/app.js (Versione Modulo)

// Importa tutto ciò che serve
import './bootstrap';
import 'whatwg-fetch';
import Swal from 'sweetalert2';
window.Swal = Swal;
import $ from 'jquery';
window.$ = window.jQuery = $;
import { initThreeAnimation } from './sfera-geodetica';
import { initializeApp as initializeUltraUploadManager } from '/vendor/ultra/ultra-upload-manager/resources/ts/core/file_upload_manager';
import { RequestCreateNotificationWallet, RequestUpdateNotificationWallet, RequestWalletDonation } from './modules/notifications/init/request-notification-wallet-init';
import { DeleteProposalInvitation } from './modules/notifications/delete-proposal-invitation';
import { DeleteProposalWallet } from './modules/notifications/delete-proposal-wallet';

// Esportiamo una singola funzione di inizializzazione
export function initializeBaseModules() {
    console.log('⚙️ Esecuzione di initializeBaseModules (logica da app.js)...');

    // Animazione 3D
    if (document.getElementById('dynamic-3d-container') && document.getElementById('webgl-canvas')) {
      initThreeAnimation();
    }

    // Upload Manager
    initializeUltraUploadManager();

    // Moduli Notifiche e Wallet
    new RequestCreateNotificationWallet({ apiBaseUrl: '/notifications' });
    new RequestUpdateNotificationWallet({ apiBaseUrl: '/notifications' });
    new RequestWalletDonation({ apiBaseUrl: '/notifications' });
    new DeleteProposalInvitation({ apiBaseUrl: '/notifications' });
    new DeleteProposalWallet({ apiBaseUrl: '/notifications' });

    console.log('✅ Moduli di base (da app.js) inizializzati.');
}
