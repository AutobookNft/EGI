/**
 * @fileoverview File di inizializzazione per il componente notifiche wallet
 * @version 1.0.0
 * @package EGI Florence
 *
 * @description
 * Questo file gestisce l'inizializzazione del componente delle notifiche
 * quando il DOM è completamente caricato.
 *
 * @requires RequestCreateNotificationWallet
 */

// request-notification-wallet-init.js
import RequestCreateNotificationWallet from '../requests/wallets/create.js';
import RequestUpdateNotificationWallet from '../requests/wallets/update.js';
import RequestWalletDonation from '../requests/wallets/donation.js';

// Esporta le classi per l’import in main.js
export {
    RequestCreateNotificationWallet,
    RequestUpdateNotificationWallet,
    RequestWalletDonation,
};
