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
import RequestCreateNotificationWallet from '../components/request-create-notification-wallet.js';
import RequestUpdateNotificationWallet from '../components/request-update-notification-wallet.js';

document.addEventListener('DOMContentLoaded', () => {
    new RequestCreateNotificationWallet({
        apiBaseUrl: '/notifications'
    });
    new RequestUpdateNotificationWallet({
        apiBaseUrl: '/notifications'
    });
});
