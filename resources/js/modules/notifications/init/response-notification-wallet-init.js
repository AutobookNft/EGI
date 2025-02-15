/**
 * @fileoverview File di inizializzazione per il componente notifiche wallet
 * @version 1.0.0
 * @package EGI Florence
 *
 * @description
 * Questo file gestisce l'inizializzazione del componente delle notifiche
 * quando il DOM è completamente caricato.
 *
 * @requires ResponseNotificationWallet
 */

// response-notification-wallet-init.js
import ResponseNotificationWallet from '../components/response-notification-wallet.js';

document.addEventListener('DOMContentLoaded', () => {
    new ResponseNotificationWallet({
        apiBaseUrl: '/notifications'
    });
});
