import './bootstrap';
import './notification';
import Swal from 'sweetalert2';
window.Swal = Swal;

import { fetchTranslations, ensureTranslationsLoaded, getTranslation } from './utils/translations';
import { loadEnums, getEnum, isPendingStatus } from './utils/enums';

// Carica gli enum all'avvio
loadEnums();
window.getEnum = getEnum;
window.isPendingStatus = isPendingStatus;

// Carica le traduzioni all'avvio
fetchTranslations();
window.getTranslation = getTranslation;
window.ensureTranslationsLoaded = ensureTranslationsLoaded;

// Inizializza Notification come singleton
let notificationInstance = null;
document.addEventListener('DOMContentLoaded', () => {
    if (!notificationInstance) {
        notificationInstance = new Notification({ apiBaseUrl: '/notifications' });
        console.log(`🔍 Inizializzazione unica Notification`);
    } else {
        console.warn(`⛔ Tentativo di inizializzazione multipla di Notification ignorato`);
    }
});

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
