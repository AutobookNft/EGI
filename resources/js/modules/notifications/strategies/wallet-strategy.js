/**
 * @fileoverview Strategia per la gestione delle notifiche di tipo "wallet"
 * @version 1.0.0
 * @package EGI Florence
 * @module WalletStrategy
 *
 * @description
 * Questa classe implementa la logica per gestire le azioni sulle notifiche di tipo "wallet".
 * Fornisce metodi per accettare, aggiornare, rifiutare, archiviare e completare notifiche,
 * utilizzando una mappatura dinamica delle azioni basata su stati predefiniti.
 * Richiede un'inizializzazione asincrona per caricare gli stati delle notifiche tramite `getEnum`.
 *
 * @requires NotificationActionRequest
 * @requires notification-api/sendAction
 * @requires utils/displayError
 * @requires handleUI/progressBarMessage
 *
 * @example
 * // Creazione e utilizzo della strategia
 * const strategy = new WalletStrategy();
 * await strategy.initialize();
 * const request = new NotificationActionRequest({
 *   action: "update",
 *   notificationId: "123",
 *   payload: "wallet",
 *   payloadId: "456"
 * });
 * await strategy.handleAction(request, "/notifications");
 */
import { NotificationActionRequest } from '../dto/notification-action-request.js';
import { sendAction } from '../services/notification-api.js';
import { displayError } from '../services/utils.js';
import { getEnum } from '../../../utils/enums.js';
// import { progressBarMessage } from '../services/handleUI.js';

export class WalletStrategy {
    /**
     * @constructor
     * @description Inizializza la strategia con proprietà di base.
     * Gli stati delle notifiche vengono caricati successivamente con `initialize`.
     */
    constructor(notificationInstance) {
        /** @type {Notification} Istanza di Notification */
        this.notificationInstance = notificationInstance; // Salviamo l'istanza di Notification

        /** @type {boolean} Indica se lo stile di progresso è stato aggiunto */
        this.progressStyleAdded = false;

        /** @type {string} Percorso base per le notifiche wallet */
        this.payload = '/wallet';

        /**
         * @type {Object} Mappa degli stati delle notifiche, inizializzati come null
         * @property {string|null} ACCEPTED - Stato di accettazione
         * @property {string|null} UPDATE - Stato di aggiornamento
         * @property {string|null} REJECTED - Stato di rifiuto
         * @property {string|null} ARCHIVED - Stato di archiviazione
         * @property {string|null} DONE - Stato di completamento
         */
        this.statuses = {
            ACCEPTED: null,
            UPDATE: null,
            REJECTED: null,
            ARCHIVED: null,
            DONE: null
        };
    }

    /**
     * @method initialize
     * @description Inizializza la strategia caricando gli stati delle notifiche in modo asincrono.
     * Deve essere chiamato prima di usare `handleAction`.
     * @returns {Promise<WalletStrategy>} Restituisce l'istanza corrente per consentire chaining
     * @async
     */
    async initialize() {
        await this.initStatuses();
        return this;
    }

    /**
     * @method initStatuses
     * @description Carica gli stati delle notifiche da `getEnum` in modo asincrono.
     * @private
     * @async
     */
    async initStatuses() {
        this.statuses.ACCEPTED = await getEnum("NotificationStatus", "ACCEPTED");
        this.statuses.UPDATE = await getEnum("NotificationStatus", "UPDATE");
        this.statuses.REJECTED = await getEnum("NotificationStatus", "REJECTED");
        this.statuses.ARCHIVED = await getEnum("NotificationStatus", "ARCHIVED");
        this.statuses.DONE = await getEnum("NotificationStatus", "DONE");
    }

    /**
     * @method handleAction
     * @description Gestisce un'azione specificata su una notifica di tipo "wallet".
     * Mappa l'azione richiesta a un metodo specifico e la esegue.
     * @param {NotificationActionRequest} actionRequest - Oggetto richiesta con dettagli dell'azione
     * @param {string} baseUrl - URL base per l'API delle notifiche
     * @returns {Promise<void>}
     * @throws {Error} Se l'azione non è gestita o si verifica un errore durante l'esecuzione
     * @async
     */
    async handleAction(actionRequest, baseUrl) {
        const fullBaseUrl = baseUrl + this.payload;

        console.log(`🚀 handleAction parametri per la notifica ${JSON.stringify(actionRequest)} e per il percorso: ${fullBaseUrl}`);

        const actions = {
            [this.statuses.ACCEPTED]: () => this.accept(actionRequest, fullBaseUrl),
            [this.statuses.UPDATE]: () => this.update(actionRequest, fullBaseUrl),
            [this.statuses.REJECTED]: () => this.openRejectModal(actionRequest, fullBaseUrl),
            [this.statuses.ARCHIVED]: () => this.archive(actionRequest, fullBaseUrl),
            [this.statuses.DONE]: () => this.done(actionRequest, fullBaseUrl)
        };

        const actionFn = actions[actionRequest.action];
        if (actionFn) await actionFn();
        else console.warn(`Azione non gestita: ${actionRequest.action}`);
    }

    /**
     * @method showProgressMessage
     * @description Mostra un messaggio di progresso nell'UI per un'azione completata.
     * @param {string} message - Messaggio da visualizzare
     * @param {string} notificationId - ID della notifica interessata
     * @param {string} colorbg - Colore di sfondo del messaggio (es. '#3B82F6')
     * @private
     */
    showProgressMessage(message, notificationId, colorbg) {
        this.progressStyleAdded = true;
        this.notificationInstance.showProgressMessage(message, notificationId, colorbg);
    }


    /**
     * @method accept
     * @description Accetta una notifica wallet e aggiorna l'UI.
     * @param {NotificationActionRequest} actionRequest - Richiesta dell'azione
     * @param {string} baseUrl - URL completo per l'API
     * @returns {Promise<void>}
     * @async
     */
    async accept(actionRequest, baseUrl) {
        await sendAction.call(this, actionRequest, baseUrl);
        this.showProgressMessage('💳 Wallet in accettazione!', actionRequest.notificationId, '#3B82F6');
    }

    /**
     * @method update
     * @description Aggiorna una notifica wallet e aggiorna l'UI.
     * @param {NotificationActionRequest} actionRequest - Richiesta dell'azione
     * @param {string} baseUrl - URL completo per l'API
     * @returns {Promise<void>}
     * @async
     */
    async update(actionRequest, baseUrl) {
        await sendAction.call(this, actionRequest, baseUrl);
        this.showProgressMessage('💳 Esecuzione della modifica al wallet!', actionRequest.notificationId, '#3B82F6');
    }

    /**
     * @method openRejectModal
     * @description Apre una modale per inserire il motivo del rifiuto e chiama `reject` se confermato.
     * @param {NotificationActionRequest} actionRequest - Richiesta dell'azione
     * @param {string} baseUrl - URL completo per l'API
     * @returns {Promise<void>}
     * @async
     */
    async openRejectModal(actionRequest, baseUrl) {
        const { value: reason } = await Swal.fire({
            title: 'Motivo del rifiuto',
            input: 'text',
            inputPlaceholder: 'Inserisci il motivo del rifiuto',
            showCancelButton: true,
            cancelButtonText: 'Annulla',
            confirmButtonText: 'Invia Rifiuto',
            preConfirm: (reason) => {
                if (!reason) {
                    Swal.showValidationMessage(`Per favore, inserisci un motivo per il rifiuto`);
                }
                return reason;
            }
        });
        if (reason) {
            try {
                actionRequest.reason = reason;
                await this.reject(actionRequest, baseUrl);
            } catch (error) {
                displayError.call(this, error.message);
            }
        }
    }

    /**
     * @method reject
     * @description Rifiuta una notifica wallet e aggiorna l'UI.
     * @param {NotificationActionRequest} actionRequest - Richiesta dell'azione, con `reason` opzionale
     * @param {string} baseUrl - URL completo per l'API
     * @returns {Promise<void>}
     * @async
     */
    async reject(actionRequest, baseUrl) {
        await sendAction.call(this, actionRequest, baseUrl);
        this.showProgressMessage('❌ Eliminazione della proposta wallet!', actionRequest.notificationId, '#E53E3E');
    }

    /**
     * @method archive
     * @description Archivia una notifica wallet e aggiorna l'UI.
     * @param {NotificationActionRequest} actionRequest - Richiesta dell'azione
     * @param {string} baseUrl - URL completo per l'API
     * @returns {Promise<void>}
     * @async
     */
    async archive(actionRequest, baseUrl) {
        await sendAction.call(this, actionRequest, baseUrl);
        this.showProgressMessage('📦 Archiviazione della proposta wallet!', actionRequest.notificationId, '#6B7280');
    }

    /**
     * @method done
     * @description Completa una notifica wallet e aggiorna l'UI.
     * @param {NotificationActionRequest} actionRequest - Richiesta dell'azione
     * @param {string} baseUrl - URL completo per l'API
     * @returns {Promise<void>}
     * @async
     */
    async done(actionRequest, baseUrl) {
        await sendAction.call(this, actionRequest, baseUrl);
        this.showProgressMessage('🎉 Proposta wallet completata!', actionRequest.notificationId, '#10B981');
    }
}
