/**
 * @fileoverview Gestione dinamica delle notifiche di risposta per il wallet
 * @version 1.0.0
 * @package EGI Florence
 * @module ResponseNotificationWallet
 */
import { PayloadHandlerFactory } from '../factories/payload-handler-factory.js';
import { NotificationActionRequest } from '../dto/notification-action-request.js';

let notificationInstance = null;

export default class Notification {

    constructor(options = {}) {
        if (notificationInstance) {
            console.warn(`⛔ Tentativo di inizializzazione multipla di Notification ignorato`);
            return notificationInstance;
        }
        this.options = options || { apiBaseUrl: '/notifications' };
        this.handlersCache = new Map();
        this.processingNotifications = {};
        // // console.log(`🔍 Inizializzazione unica Notification: options ${JSON.stringify(this.options)}`);
        this.initialize();
        notificationInstance = this;
        return this;
    }

    initialize() {
        this.bindEvents();
        this.highlightAllNotifications();
    }

    async getOrCreateHandler(payload) {
        if (!this.handlersCache.has(payload)) {
            const handler = PayloadHandlerFactory.create(payload, this);

            if (!handler) {
                console.warn(`Factory ha restituito null per payload: ${payload}`);
                return null;
            }

            // ✅ INIZIALIZZA SOLO SE ESISTE IL METODO
            if (typeof handler.initialize === 'function') {
                try {
                    await handler.initialize();
                    // console.log(`Handler per ${payload} inizializzato`);
                } catch (error) {
                    console.error(`Errore inizializzazione handler per ${payload}:`, error);
                    return null;
                }
            }

            // ✅ CACHA SEMPRE, ANCHE SENZA initialize()
            this.handlersCache.set(payload, handler);
            // // console.log(`Handler per ${payload} cachato`);
        }

        return this.handlersCache.get(payload);
    }

    bindEvents() {
        let clickCounter = 0; // Contatore univoco per ogni evento click
        const recentClicks = new Map(); // Map per tracciare i click recenti con microsecondi
        const debounceTime = 2; // 2ms per evitare doppi click ravvicinati (1.3ms di differenza)

        document.addEventListener('click', async (e) => {
            const timestamp = performance.now(); // Microsecondi per maggiore precisione
            const clickId = clickCounter++; // Incrementa il contatore prima di usarlo

            // // console.log(`🔔 Evento click all'inizio di bindEvents catturato alle ${new Date().toISOString()}, ID: ${clickCounter++}, target: ${e.target.tagName}.${e.target.className}, timestamp: ${timestamp.toFixed(3)}ms`);

            const btn = e.target.closest('.response-btn, .archive-btn, .reject-btn, .invitation-response-btn, .invitation-reject-btn, .invitation-archive-btn');
            if (btn) {
                e.stopPropagation(); // Ferma la propagazione
                const notificationId = btn.closest('.notification-item')?.dataset.notificationId;
                if (!notificationId) {
                    console.error('Notifica ID mancante per pulsante');
                    return;
                }

                // Blocca se c'è stato un click recente sulla stessa notifica, azione e target
                const clickKey = `${notificationId}-${btn.dataset.action}-${e.target.tagName.toLowerCase()}-${timestamp.toFixed(0)}`;
                if (recentClicks.has(clickKey)) {
                    console.warn(`⛔ Azione ignorata: click duplicato su ${clickKey} entro ${debounceTime}ms`);
                    return;
                }
                recentClicks.set(clickKey, timestamp);
                setTimeout(() => recentClicks.delete(clickKey), debounceTime + 100); // Pulisci dopo 102ms

                // Verifica unicità del pulsante nel DOM
                const duplicateCount = document.querySelectorAll(`[data-notification-id="${notificationId}"][data-action="${btn.dataset.action}"]`).length;
                if (duplicateCount > 1) {
                    console.warn(`⚠ Elemento duplicato trovato: ${btn.dataset.action} per notifica ${notificationId}, count: ${duplicateCount}`);
                    return; // Ignora se ci sono duplicati
                }

                // Lock per la notifica specifica
                if (this.processingNotifications && this.processingNotifications[notificationId]) {
                    console.warn(`⛔ Azione ignorata: notifica ${notificationId} già in processamento`);
                    return;
                }
                if (!this.processingNotifications) {
                    this.processingNotifications = {};
                }
                this.processingNotifications[notificationId] = true;

                // console.log(`🔘 Pulsante trovato: ${btn.dataset.action} per notifica ${notificationId}, click ID: ${clickId}`);

                try {
                    await this.handleActionClick(btn);
                } catch (error) {
                    console.error(`Errore nell'elaborazione del click per ${notificationId}, ID ${clickId}:`, error);
                } finally {
                    delete this.processingNotifications[notificationId]; // Sblocca dopo l'azione
                }
                return;
            }

            const thumbnail = e.target.closest('.notification-thumbnail');

            if (thumbnail ) {

                e.stopPropagation(); // Ferma la propagazione
                const notificationId = thumbnail.dataset.notificationId;
                if (!notificationId) {
                    console.error('ID notifica mancante per thumbnail');
                    return;
                }

                // Blocca se c'è stato un click recente sulla stessa thumbnail e target
                const clickKey = `${notificationId}-thumbnail-${e.target.tagName.toLowerCase()}-${timestamp.toFixed(0)}`;
                if (recentClicks.has(clickKey)) {
                    console.warn(`⛔ Azione ignorata: click duplicato su ${clickKey} entro ${debounceTime}ms`);
                    return;
                }
                recentClicks.set(clickKey, timestamp);
                setTimeout(() => recentClicks.delete(clickKey), debounceTime + 100); // Pulisci dopo 102ms

                // Verifica unicità della thumbnail nel DOM
                const duplicateCount = document.querySelectorAll(`.notification-thumbnail[data-notification-id="${notificationId}"]`).length;
                if (duplicateCount > 1) {
                    console.warn(`⚠ Thumbnail duplicata trovata per notifica ${notificationId}, count: ${duplicateCount}`);
                    return; // Ignora se ci sono duplicati
                }

                // console.log(`🔍 Thumbnail trovata: notifica ${notificationId}, click ID: ${clickId}`);
                try {
                    await this.handleThumbnailClick(thumbnail);
                } catch (error) {
                    console.error(`Errore nell'elaborazione del click per ${notificationId}, ID ${clickId}:`, error);
                }
            }

            // console.log(`🔔 Evento alla fine di bindEventscatturato alle ${new Date().toISOString()}, ID: ${clickCounter++}, target: ${e.target.tagName}.${e.target.className}, timestamp: ${timestamp.toFixed(3)}ms`);
        });
    }

    async handleActionClick(btn) {
        const payload = btn.closest('.notification-item')?.dataset.payload;
        const action = btn.dataset.action;
        const notificationId = btn.closest('.notification-item')?.dataset.notificationId;
        const payloadId = btn.closest('.notification-item')?.dataset.payloadId;

        if (!payload || !action || !notificationId) {
            console.error('Dati mancanti per azione:', { payload, action, notificationId });
            return;
        }

        // Verifica unicità del pulsante nel DOM
        const duplicateCount = document.querySelectorAll(`[data-notification-id="${notificationId}"][data-action="${action}"]`).length;
        if (duplicateCount > 1) {
            console.warn(`⚠ Elemento duplicato trovato: ${action} per notifica ${notificationId}, count: ${duplicateCount}`);
            return; // Ignora se ci sono duplicati
        }

        // Verifica stato del pulsante (disabilitato o già processato)
        if (btn.disabled) {
            console.warn(`⛔ Pulsante ${action} per notifica ${notificationId} già disabilitato, azione ignorata`);
            return;
        }

        // console.log(`🔘 Inizio azione ${action} per notifica ${notificationId} con payload ${payload}`);

        const actionRequest = new NotificationActionRequest({
            action: action,
            notificationId: notificationId,
            payload: payload,
            payloadId: payloadId
        });

        try {
            const handler = await this.getOrCreateHandler(payload);
            if (!handler) {
                console.warn(`Nessun gestore trovato per il payload ${payload}`);
                return;
            }
            console.log('🚀 baseUrl:', this.options.apiBaseUrl);
            await handler.handleAction(actionRequest, this.options.apiBaseUrl);
        } catch (error) {
            console.error(`Errore nella gestione della notifica ${notificationId}:`, error.message);
        }
    }

    async handleThumbnailClick(thumbnail) {
        const notificationId = thumbnail.dataset.notificationId;
        if (!notificationId) {
            console.error('ID notifica mancante per thumbnail');
            return;
        }

        const thumbnails = document.querySelectorAll('.notification-thumbnail');
        thumbnails.forEach(t => t.classList.remove('bg-gray-700'));
        thumbnail.classList.add('bg-gray-700');
        this.setThumbnailActiveState(notificationId);

        console.log("🔍 Carico dettagli per notifica:", notificationId, thumbnails);
        const url = `/notifications/${notificationId}/details`;
        try {
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`Errore HTTP: ${response.status}`);
            }
            const html = await response.text();
            const detailsContainer = document.getElementById('notification-details');
            if (detailsContainer) {
                detailsContainer.innerHTML = html;
            } else {
                console.error('Container dettagli non trovato');
            }
        } catch (error) {
            console.error("❌ Errore nel caricamento della notifica:", error);
            const detailsContainer = document.getElementById('notification-details');
            if (detailsContainer) {
                detailsContainer.innerHTML = '<p class="text-red-500">Errore nel caricamento della notifica.</p>';
            }
        }
    }

    setThumbnailActiveState(selectedId) {
        const thumbnails = document.querySelectorAll('.notification-thumbnail');
        thumbnails.forEach(t => {
            t.style.backgroundColor = t.dataset.notificationId === selectedId ? '#4a5568' : '#2d3748';
        });
    }

    highlightAllNotifications() {
        console.log("🎨 Evidenziazione notifiche al caricamento...");
        const thumbnails = document.querySelectorAll('.notification-thumbnail');
        thumbnails.forEach(thumbnail => {
            const createdAt = new Date(thumbnail.dataset.createdAt);
            const now = new Date();
            const expirationHours = parseInt(window.NOTIFICATION_EXPIRATION_HOURS || 72, 10);
            const diffHours = (now - createdAt) / 36e5;

            let warningText = thumbnail.querySelector("#expiration-warning");
            let warningTooltip = thumbnail.querySelector("#text-tooltip");

            if (!warningText) {
                console.error(`❌ Non trovato <p id="expiration-warning"> per notifica ${thumbnail.dataset.notificationId}`);
                return;
            }

            if (typeof isPendingStatus === 'function' && isPendingStatus(thumbnail.dataset.status) && diffHours >= (expirationHours - 5)) {
                // console.log(`🟡 La notifica ${thumbnail.dataset.notificationId} è quasi scaduta!`);
                const remainingHours = Math.ceil(expirationHours - diffHours);
                setTimeout(() => {
                    if (warningTooltip) {
                        warningTooltip.setAttribute("title", `Questa notifica scadrà tra ${remainingHours} ore. Dopo non sarà più modificabile.`);
                    }
                }, 10);
                warningText.textContent = `${remainingHours}h alla scadenza...`;
                warningText.classList.add("text-yellow-300", "font-bold");
                thumbnail.style.backgroundColor = "#ffcc00";
            }
        });
    }

    showProgressMessage(message, notificationId, colorbg) {
        const notificationItem = document.querySelector(`.notification-item[data-payload-id="${notificationId}"]`);
        if (notificationItem) {
            notificationItem.remove();
        }

        const detailsContainer = document.getElementById('notification-details');
        if (detailsContainer) {
            detailsContainer.innerHTML = `
                <div id="archive-message" style="color: #FFFFFF; background-color: #000000; padding: 1rem; font-weight: bold; border-radius: 0.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.3); position: relative;">
                    ${message}
                    <div class="progress-bar" style="width: 100%; background-color: ${colorbg}; transition: width 3s linear; height: 8px; position: absolute; bottom: 0; left: 0;"></div>
                </div>
            `;

            setTimeout(() => {
                const progressBar = detailsContainer.querySelector('.progress-bar');
                if (progressBar) {
                    progressBar.offsetWidth;
                    progressBar.style.width = '0%';
                }
            }, 10);

            // Delay aumentato a 4000ms per maggiore stabilità, con verifica DOM
            // console.log(`🔄 Inizio verifica DOM e reload per notifica ${notificationId} dopo 4000ms`);
            setTimeout(() => {
                const duplicateBtns = document.querySelectorAll(`.invitation-response-btn[data-notification-id="${notificationId}"]`).length > 1;
                if (duplicateBtns) {
                    console.warn(`⚠ Pulsanti duplicati trovati per notifica ${notificationId}, ignorando reload`);
                    return;
                }
                this.reloadNotificationList();
                // console.log(`🔄 Reload completato per notifica ${notificationId}`);
            }, 4000);
        } else {
            console.error('Container dettagli non trovato');
        }
    }

    async reloadNotificationList() {
        console.log('🔄 Ricarico lista notifiche');
        try {
            const response = await fetch('/notifications/request');
            if (!response.ok) {
                throw new Error(`Errore HTTP: ${response.status}`);
            }
            const html = await response.text();
            // Validazione HTML basata su .notification-thumbnail
            if (!html || typeof html !== 'string') {
                console.error('HTML non valido ricevuto dal server: contenuto vuoto o non stringa');
                const detailsContainer = document.getElementById('notification-details');
                if (detailsContainer) {
                    detailsContainer.innerHTML = `<p class="text-red-500">Errore: ${window.getTranslation('notification.notification_list_error')}</p>`;
                }
                return;
            }
            const container = document.getElementById('head-notifications-container');
            if (container) {
                container.innerHTML = html;
                this.bindEvents(); // Ri-lega eventi a pulsanti e thumbnail
                this.highlightAllNotifications(); // Ricolora le thumbnail
                const thumbnailCount = document.querySelectorAll('.notification-thumbnail').length;
                // console.log(`🔄 Ricaricamento completato, thumbnail rilegate: ${thumbnailCount}`);
            } else {
                console.error('Container notifiche non trovato');
            }
            const notifications = container ? container.querySelectorAll('.notification-thumbnail') : [];
            const detailsContainer = document.getElementById('notification-details');
            if (detailsContainer) {
                if (notifications.length === 0) {
                    detailsContainer.innerHTML = `<p class="text-gray-300 text-lg italic">${window.getTranslation('notification.no_notifications')}</p>`;
                } else {
                    detailsContainer.innerHTML = `<p class="text-gray-300 text-lg italic">${window.getTranslation('notification.select_notification')}</p>`;
                }
            }
        } catch (error) {
            console.error('Errore nel caricamento delle notifiche:', error);
            const detailsContainer = document.getElementById('notification-details');
            if (detailsContainer) {
                detailsContainer.innerHTML = `<p class="text-red-500">Errore: ${window.getTranslation('notification.notification_list_error')}</p>`;
            }
        }
    }

    /**
     * Rimuove una notifica specifica dal DOM
     */
    removeNotificationFromDOM(notificationId) {
        console.log(`🗑 Rimozione notifica ${notificationId} dal DOM`);

        // Rimuovi thumbnail dalla lista
        const thumbnail = document.querySelector(`.notification-thumbnail[data-notification-id="${notificationId}"]`);
        if (thumbnail) {
            thumbnail.remove();
            console.log(`✅ Thumbnail rimossa per notifica ${notificationId}`);
        }

        // Rimuovi item dalla lista notifiche (se presente)
        const notificationItem = document.querySelector(`.notification-item[data-notification-id="${notificationId}"]`);
        if (notificationItem) {
            notificationItem.remove();
            console.log(`✅ Item rimosso per notifica ${notificationId}`);
        }

        // Pulisci dettagli se questa notifica era selezionata
        const detailsContainer = document.getElementById('notification-details');
        if (detailsContainer) {
            const remainingNotifications = document.querySelectorAll('.notification-thumbnail').length;
            if (remainingNotifications === 0) {
                detailsContainer.innerHTML = `<p class="text-gray-300 text-lg italic">${window.getTranslation('notification.no_notifications')}</p>`;
            } else {
                detailsContainer.innerHTML = `<p class="text-gray-300 text-lg italic">${window.getTranslation('notification.select_notification')}</p>`;
            }
        }
    }
}
