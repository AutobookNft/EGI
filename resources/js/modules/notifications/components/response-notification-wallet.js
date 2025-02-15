/**
 * @fileoverview Gestione delle notifiche di risposta per il wallet
 * @version 1.0.0
 * @package EGI Florence
 * @module ResponseNotificationWallet
 *
 * @description
 * Questo modulo rappresenta il componente principale per la gestione delle notifiche
 * relative alle risposte del wallet. Coordina le interazioni tra i vari sottomoduli:
 * - NotificationLoader: Caricamento dati
 * - NotificationBindings: Gestione eventi UI
 * - NotificationActions: Logica delle azioni
 *
 * @requires NotificationLoader
 * @requires NotificationBindings
 * @requires NotificationActions
 *
 * @example
 * const wallet = new ResponseNotificationWallet({
 *   apiBaseUrl: '/notifications'
 * });
 */

import { bindNotificationEvents } from './response-notification-wallet-bindings.js';
import { reloadNotificationListLoader } from './response-notification-wallet-loader.js';
import { handleAccept, handleUpdate, handleReject, handleArchive, handleDone } from './notification-actions.js';
import { sendAction as apiSendAction } from '../services/notification-api.js';

class ResponseNotificationWallet {
    constructor(options = {}) {
        this.progressStyleAdded = false;
        this.options = options; // Salviamo le opzioni
        // eventuale utilizzo di options, ad esempio options.apiBaseUrl
        bindNotificationEvents.call(this); // lega gli eventi in modo che "this" punti all'istanza
        console.log('🚀 NotificationWallet initialized');
    }

    // Metodo per aprire la modale di rifiuto (rimane nella classe)
    async openRejectModal(notificationId) {
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
            // Chiamata alla funzione importata per il rifiuto
            this.handleReject(notificationId, reason);
        }
    }

    // Questi metodi delegano alle funzioni importate, usando il binding della istanza (this)
    async handleAccept(notificationId) {
        return handleAccept.call(this, notificationId);
    }

    async handleUpdate(notificationId) {
        return handleUpdate.call(this, notificationId);
    }

    async handleReject(notificationId, reason) {
        return handleReject.call(this, notificationId, reason);
    }

    async handleArchive(notificationId) {
        return handleArchive.call(this, notificationId);
    }

    async handleDone(notificationId) {
        return handleDone.call(this, notificationId);
    }

    // Metodo API: delega alla funzione importata da api.js
    async sendAction(notificationId, action, reason = null) {
        return apiSendAction.call(this, notificationId, action, reason);
    }

    // Resto dei metodi che gestiscono l'UI e il comportamento
    progressBarMessage(message, notificationId, colorbg) {
        // Rimuovi la notifica corrente
        const notificationItem = document.querySelector(`.notification-item[data-notification-id="${notificationId}"]`);
        if (notificationItem) {
            notificationItem.remove();
        }

        const detailsContainer = document.getElementById('notification-details');

        // Aggiorna l'HTML con la progressbar e applica il colore inline
        detailsContainer.innerHTML = `
        <div id="archive-message" style="color: #FFFFFF; background-color: #000000; padding: 1rem; font-weight: bold; border-radius: 0.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.3); position: relative;">
            ${message}
           <div class="progress-bar" style="width: 100%; background-color: ${colorbg}; transition: width 3s linear; height: 8px; position: absolute; bottom: 0; left: 0;"></div>
        </div>
        `;

        // <div id="archive-message" class="archive-message p-4 font-semibold rounded-lg shadow-md relative" style="color: ${colorbg};">

        // Avvia la deplezione della progress bar
        setTimeout(() => {
            const progressBar = detailsContainer.querySelector('.progress-bar');
            if (progressBar) {
                // Forza il reflow (se necessario)
                progressBar.offsetWidth;
                // Imposta la larghezza a 0% inline per attivare la transizione
                progressBar.style.width = '0%';
            }
        }, 10);

        // Ricarica le notifiche al termine della progress bar
        setTimeout(() => {
            this.reloadNotificationList();
        }, 3000);
    }

    updateUI(notificationId, notificationData) {
        const notificationItem = document.querySelector(`.notification-item[data-notification-id="${notificationId}"]`);
        if (!notificationItem) return;

        setTimeout(() => {
            const archiveBtn = document.getElementById(`archive-btn-${notificationId}`);
            console.log("🔄 Pulsante Archive trovato:", archiveBtn);

            if (notificationData.outcome === getEnum("NotificationStatus", "ACCEPTED")) {
                if (archiveBtn) archiveBtn.style.display = 'block';
            } else {
                if (archiveBtn) archiveBtn.style.display = 'none';
            }

            const statusElement = document.getElementById(`status-field-${notificationId}`);
            console.log("🔄 Gestione stato notifica:", statusElement);
            console.log("🔄 notificationData.outcome:", notificationData.outcome);

            if (statusElement) {
                statusElement.textContent = notificationData.status;
                statusElement.classList.remove('text-yellow-500', 'text-red-500', 'text-green-500');

                if (notificationData.outcome === getEnum("NotificationStatus", "ACCEPTED")) {
                    statusElement.classList.add('text-green-500');
                    statusElement.textContent = getEnum("NotificationStatus", "ACCEPTED");
                } else if (notificationData.outcome === getEnum("NotificationStatus", "REJECTED")) {
                    statusElement.classList.add('text-red-500');
                    statusElement.textContent = getEnum("NotificationStatus", "REJECTED");
                }
            }
        }, 500);

        const actionsContainer = notificationItem.querySelector('.notification-actions');
        if (!actionsContainer) return;

        if (notificationData.outcome === getEnum("NotificationStatus", "ACCEPTED")) {
            const archiveBtn = document.getElementById(`archive-btn-${notificationId}`);
            if (archiveBtn) {
                archiveBtn.style.display = 'block';
            } else {
                console.warn(`⚠️ archive-btn-${notificationId} non trovato.`);
            }
        } else {
            actionsContainer.innerHTML = `
                <button
                    class="response-btn flex-1 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center"
                    data-notification-id="${notificationId}"
                    data-action="${getEnum("NotificationStatus", "ACCEPTED")}"
                    aria-label="Accetta la notifica di creazione del wallet">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    ${window.getTranslation('collection.wallet.accept')}
                </button>
                <button id="reject-btn-${notificationId}"
                    class="reject-btn flex-1 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center"
                    data-notification-id="${notificationId}"
                    data-action="${getEnum("NotificationStatus", "REJECTED")}"
                    aria-label="Rifiuta la notifica di creazione del wallet">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    ${window.getTranslation('collection.wallet.decline')}
                </button>
            `;
        }

        // Rileghiamo gli eventi (se necessario)
        bindNotificationEvents.call(this);
    }

    displayError(message) {
        Swal.fire({
            icon: 'error',
            title: 'Errore!',
            text: message
        });
    }

    // Metodo per ricaricare la lista delle notifiche, delega al modulo loader
    reloadNotificationList() {
        reloadNotificationListLoader.call(this);
    }
}

export default ResponseNotificationWallet;
