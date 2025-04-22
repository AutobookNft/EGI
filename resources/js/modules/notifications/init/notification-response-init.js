/**
 * @fileoverview File di inizializzazione per il componente notifiche wallet
 * @version 1.0.0
 * @package EGI Florence
 *
 * @description
 * Questo file gestisce l'inizializzazione del componente delle notifiche
 * quando il DOM è completamente caricato.
 *
 * @requires Notification
 */

import Notification from '../responses/notification';

let notificationInstance = null;

document.addEventListener('DOMContentLoaded', () => {
    if (!notificationInstance) {
        notificationInstance = new Notification({ apiBaseUrl: '/notifications' });
        console.log(`🔍 Inizializzazione unica Notification`);
    } else {
        console.warn(`⛔ Tentativo di inizializzazione multipla ignorato`);
    }
});
