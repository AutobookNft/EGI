/**
 * @fileoverview Strategia per la gestione delle notifiche di tipo "reservation"
 * @version 1.0.0
 * @package FlorenceEGI
 * @module ReservationStrategy
 *
 * @description
 * Gestisce le azioni sulle notifiche di tipo "reservation" (archivio, visualizzazione),
 * mappando gli stati delle notifiche e integrandosi con il sistema di viste.
 *
 * @requires NotificationActionRequest
 * @requires notification-api/sendAction
 * @requires utils/displaySuccess
 */
import { NotificationActionRequest } from '../dto/notification-action-request.js';
import { sendAction } from '../services/notification-api.js';
import { displaySuccess, displayError } from '../services/utils.js';
import { getEnum } from '../../../utils/enums.js';

export class ReservationStrategy {
    constructor(notificationInstance) {
        this.notificationInstance = notificationInstance;
        this.payload = '/reservation';
        this.statuses = {
            ARCHIVED: null
        };
    }

    async initialize() {
        await this.initStatuses();
        return this;
    }

    async initStatuses() {
        this.statuses.ARCHIVED = await getEnum("NotificationStatus", "ARCHIVED");
    }

    async handleAction(actionRequest, baseUrl) {
        const fullBaseUrl = baseUrl + this.payload;
        console.log(`🚀 handleAction parametri per la notifica ${JSON.stringify(actionRequest)} e per il percorso: ${fullBaseUrl}`);

        const actions = {
            [this.statuses.ARCHIVED]: () => this.archive(actionRequest, fullBaseUrl),
            'archive': () => this.archive(actionRequest, fullBaseUrl) // Fallback for direct archive action
        };

        const actionFn = actions[actionRequest.action];
        if (actionFn) {
            await actionFn();
            console.log(`✅ Azione ${actionRequest.action} completata per prenotazione ${actionRequest.notificationId}`);
        } else {
            console.warn(`Azione non gestita: ${actionRequest.action}`);
        }
    }

    async archive(actionRequest, baseUrl) {
        try {
            await sendAction.call(this, actionRequest, baseUrl);

            // Determina il colore in base al tipo di notifica
            let color = '#6B7280'; // Default gray
            const notificationElement = document.querySelector(`[data-notification-id="${actionRequest.notificationId}"]`);

            if (notificationElement) {
                if (notificationElement.classList.contains('bg-emerald-600')) {
                    color = '#10B981'; // Green for highest
                } else if (notificationElement.classList.contains('bg-yellow-600')) {
                    color = '#F59E0B'; // Yellow for superseded
                } else if (notificationElement.classList.contains('bg-blue-600')) {
                    color = '#3B82F6'; // Blue for rank change
                }
            }

            this.notificationInstance.showProgressMessage('✅ Notifica archiviata!', actionRequest.notificationId, color);

            // Rimuovi la notifica dalla vista dopo un breve delay
            setTimeout(() => {
                if (notificationElement) {
                    notificationElement.style.transition = 'opacity 0.3s ease-out';
                    notificationElement.style.opacity = '0';
                    setTimeout(() => {
                        notificationElement.remove();
                        // Controlla se ci sono altre notifiche
                        const remainingNotifications = document.querySelectorAll('[data-payload="reservation"]');
                        if (remainingNotifications.length === 0) {
                            // Mostra messaggio "nessuna notifica"
                            const detailsContainer = document.getElementById('notification-details');
                            if (detailsContainer) {
                                detailsContainer.innerHTML = '<p class="text-gray-300 text-lg italic">Nessuna notifica di prenotazione</p>';
                            }
                        }
                    }, 300);
                }
            }, 1000);

        } catch (error) {
            console.error(`Errore nell'archiviazione della notifica ${actionRequest.notificationId}:`, error);
            displayError.call(this, error.message || 'Errore durante l\'archiviazione');
        }
    }

    /**
     * Gestisce il click sui pulsanti di azione specifici delle prenotazioni
     * come "Rilancia Ora" o "Vedi Classifica"
     */
    handleSpecialActions() {
        // Gestione click su "Rilancia Ora"
        document.querySelectorAll('a[href*="egi.show"]').forEach(link => {
            link.addEventListener('click', (e) => {
                console.log('🎯 Navigazione verso EGI:', link.href);
                // Potrebbe essere utile tracciare questa azione
                this.trackAction('view_egi', link.closest('[data-notification-id]')?.dataset.notificationId);
            });
        });
    }

    /**
     * Traccia le azioni dell'utente per analytics
     */
    trackAction(action, notificationId) {
        console.log(`📊 Tracking: ${action} for notification ${notificationId}`);
        // Qui potresti inviare dati ad analytics o al backend
    }

    /**
     * Inizializza i listener per i pulsanti delle notifiche reservation
     */
    initializeListeners() {
        // Listener per i pulsanti di archivio
        document.querySelectorAll('.reservation-archive-btn').forEach(button => {
            button.addEventListener('click', async (e) => {
                e.preventDefault();
                const notificationId = button.dataset.notificationId;
                const action = button.dataset.action || 'archive';

                const actionRequest = new NotificationActionRequest({
                    notificationId: notificationId,
                    action: action,
                    payload: 'reservation'
                });

                await this.handleAction(actionRequest, this.notificationInstance.apiBaseUrl);
            });
        });

        // Inizializza azioni speciali
        this.handleSpecialActions();
    }
}
