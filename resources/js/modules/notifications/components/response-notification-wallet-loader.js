/**
 * @fileoverview Gestione del caricamento dati per le notifiche wallet
 * @version 1.0.0
 * @package EGI Florence
 * @module NotificationLoader
 *
 * @description
 * Questo modulo gestisce tutte le operazioni di caricamento dati per le notifiche,
 * incluso il caricamento iniziale e il ricaricamento dopo le azioni.
 */

import { bindNotificationEvents } from './response-notification-wallet-bindings.js';

// response-notification-wallet-loader.js
export async function reloadNotificationListLoader() {
    console.log('🔄 Ricarico lista notifiche');
    try {
        const response = await fetch('/notifications/request');
        console.log('🎉 response', response);
        const html = await response.text();
        console.log('🔄 Ricarico lista notifiche', html);

        // Aggiorna il container delle notifiche
        const container = document.getElementById('head-notifications-container');
        if (container) {
            container.innerHTML = html;

            // Ricollega gli eventi
            bindNotificationEvents.call(this);
        }

        // Ora controlliamo quanti elementi di notifica ci sono
        // Supponendo che ogni notifica abbia la classe 'notification-item'
        const notifications = container ? container.querySelectorAll('.notification-thumbnail') : [];
        const detailsContainer = document.getElementById('notification-details');

        // Se non ci sono notifiche, mostriamo il messaggio "no notifications"
        if (notifications.length === 0) {
            detailsContainer.innerHTML = `<p class="text-gray-300 text-lg italic">${window.getTranslation('notification.no_notifications')}</p>`;
        } else {
            detailsContainer.innerHTML = `<p class="text-gray-300 text-lg italic">${window.getTranslation('notification.select_notification')}</p>`;
        }
    } catch (error) {
        console.error('Errore nel caricamento delle notifiche:', error);
    }
}


