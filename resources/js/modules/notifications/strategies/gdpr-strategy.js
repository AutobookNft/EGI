import { sendAction } from '../services/notification-api.js';
import { displayError, displaySuccess } from '../services/utils.js';
import { appTranslate } from '../../../../ts/config/appConfig.ts';
// '../../../ts/config/appConfig';

/**
 * @fileoverview Strategia per la gestione delle notifiche GDPR interattive con protocollo a tripla scelta.
 * @version 2.0.0 - Tripla Scelta (Confirm/Revoke/Disavow)
 * @description Gestisce il flusso sicuro di conferma, revoca o disconoscimento delle notifiche GDPR.
 *
 * --- OS1.5 DOCUMENTATION ---
 * @oracode-intent: Fornire logica client-side sicura per gestire interazioni utente con notifiche GDPR che richiedono conferma esplicita, implementando il "Protocollo Fortino Digitale"
 * @oracode-security: Implementa protezioni contro azioni accidentali tramite modali di chiarificazione e conferme progressive. Il disavow richiede doppia conferma per prevenire falsi allarmi di sicurezza.
 * @os1-compliance: Full
 */
export class GdprStrategy {
    /**
     * @constructor
     * @param {Notification} notificationInstance - L'istanza dell'orchestratore di notifiche principale.
     */
    constructor(notificationInstance) {
        this.notificationInstance = notificationInstance;
        this.actions = {
            CONFIRM: 'confirm',
            INIT_DISAVOW: 'init_disavow',
            REVOKE: 'revoke',
            DISAVOW: 'disavow'
        };

        // Configurazione SweetAlert2 per modali
        this.modalConfig = {
            customClass: {
                popup: 'swal2-gdpr-modal',
                title: 'swal2-gdpr-title',
                confirmButton: 'swal2-gdpr-confirm',
                cancelButton: 'swal2-gdpr-cancel'
            },
            backdrop: true,
            allowOutsideClick: false,
            allowEscapeKey: true
        };
    }

    /**
     * Gestisce un'azione specificata su una notifica GDPR interattiva.
     * @param {NotificationActionRequest} actionRequest - L'oggetto DTO con i dettagli dell'azione.
     * @returns {Promise<void>}
     */
    async handleAction(actionRequest) {
        try {
            switch (actionRequest.action) {
                case this.actions.CONFIRM:
                    await this.confirmAction(actionRequest);
                    break;

                case this.actions.INIT_DISAVOW:
                    await this.showDisavowClarificationModal(actionRequest);
                    break;

                default:
                    console.warn(`Azione GDPR non gestita: ${actionRequest.action}`);
                    break;
            }
        } catch (error) {
            console.error(`Errore nella gestione azione GDPR:`, error);
            displayError(window.getTranslation('gdprErrorGeneral') || 'Si è verificato un errore imprevisto.');
        }
    }

    /**
     * Esegue l'azione di conferma chiamando direttamente l'API.
     * @param {NotificationActionRequest} actionRequest
     * @returns {Promise<void>}
     */
    async confirmAction(actionRequest) {
        try {
            const response = await this.callGdprEndpoint(actionRequest.notificationId, 'confirm');

            if (response.success) {
                const message = window.getTranslation('gdprConsentUpdateSuccess') || 'Le tue preferenze di consenso sono state aggiornate.';
                this.showSuccessFeedback(message, actionRequest.notificationId);

                // Rimuovi la notifica dal DOM o aggiorna lo stato visivo
                this.updateNotificationUI(actionRequest.notificationId, 'confirmed');
            }

        } catch (error) {
            throw new Error(`Errore durante la conferma: ${error.message}`);
        }
    }

    /**
     * Mostra il modale di chiarificazione per distinguere tra revoca e disconoscimento.
     * @param {NotificationActionRequest} actionRequest
     * @returns {Promise<void>}
     */
    async showDisavowClarificationModal(actionRequest) {
        const { value: userChoice } = await Swal.fire({
            title: appTranslate('gdprModalClarificationTitle') || 'Chiarificazione Necessaria',
            html: this.buildClarificationModalContent(),
            icon: 'question',
            showCancelButton: true,
            showDenyButton: true,
            confirmButtonText: window.getTranslation('gdprModalRevokeButtonText') || 'Ho cambiato idea',
            denyButtonText: window.getTranslation('gdprModalDisavowButtonText') || 'Non riconosco questa azione',
            cancelButtonText: window.getTranslation('gdprCancel') || 'Annulla',
            confirmButtonColor: '#f59e0b', // Amber per "ripensamento"
            denyButtonColor: '#dc2626',    // Red per "sicurezza"
            ...this.modalConfig
        });

        // Gestisci la scelta dell'utente
        if (userChoice === true) {
            // Utente ha scelto "Ho cambiato idea" → REVOKE
            await this.revokeAction(actionRequest);
        } else if (userChoice === false) {
            // Utente ha scelto "Non riconosco" → DISAVOW (con conferma aggiuntiva)
            await this.showDisavowConfirmationModal(actionRequest);
        }
        // Se userChoice è undefined, l'utente ha annullato
    }

    /**
     * Mostra il modale di conferma finale per il disconoscimento (Codice Rosso).
     * @param {NotificationActionRequest} actionRequest
     * @returns {Promise<void>}
     */
    async showDisavowConfirmationModal(actionRequest) {
        const { isConfirmed } = await Swal.fire({
            title: window.getTranslation('gdprModalConfirmationTitle') || 'Conferma Protocollo di Sicurezza',
            html: this.buildDisavowConfirmationContent(),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: window.getTranslation('gdprModalConfirmDisavow') || 'Sì, attiva protocollo di sicurezza',
            cancelButtonText: window.getTranslation('gdprCancel') || 'Annulla',
            confirmButtonColor: '#dc2626',
            focusCancel: true, // Protegge da conferme accidentali
            ...this.modalConfig
        });

        if (isConfirmed) {
            await this.disavowAction(actionRequest);
        }
    }

    /**
     * Esegue l'azione di revoca (ripensamento dell'utente).
     * @param {NotificationActionRequest} actionRequest
     * @returns {Promise<void>}
     */
    async revokeAction(actionRequest) {
        try {
            const response = await this.callGdprEndpoint(actionRequest.notificationId, 'revoke');

            if (response.success) {
                const message = response.message || window.getTranslation('gdprConsentUpdateSuccess') || 'Consenso revocato con successo.';
                this.showSuccessFeedback(message, actionRequest.notificationId);
                this.updateNotificationUI(actionRequest.notificationId, 'revoked');
            }

        } catch (error) {
            throw new Error(`Errore durante la revoca: ${error.message}`);
        }
    }

    /**
     * Esegue l'azione di disconoscimento (attivazione protocollo di sicurezza).
     * @param {NotificationActionRequest} actionRequest
     * @returns {Promise<void>}
     */
    async disavowAction(actionRequest) {
        try {
            const response = await this.callGdprEndpoint(actionRequest.notificationId, 'disavow');

            if (response.success) {
                // Messaggio speciale per disavow - include riferimento a controlli aggiuntivi
                const message = response.message ||
                    'Protocollo di sicurezza attivato. Controlla la tua email per ulteriori istruzioni.';

                this.showSecurityFeedback(message, actionRequest.notificationId);
                this.updateNotificationUI(actionRequest.notificationId, 'disavowed');
            }

        } catch (error) {
            throw new Error(`Errore durante il disconoscimento: ${error.message}`);
        }
    }

    /**
     * Effettua la chiamata all'endpoint GDPR appropriato.
     * @param {string} notificationId
     * @param {string} action - 'confirm', 'revoke', o 'disavow'
     * @returns {Promise<Object>}
     */
    async callGdprEndpoint(notificationId, action) {
        const endpoint = `/notifications/${notificationId}/gdpr/${action}`;

        console.log(`Chiamata API GDPR: ${endpoint} con azione ${action}`);

        const response = await fetch(endpoint, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        console.log(`Risposta API GDPR: ${response.status} ${response.statusText}`);

        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || `Errore HTTP ${response.status}`);
        }

        return await response.json();
    }

    /**
     * Costruisce il contenuto HTML del modale di chiarificazione.
     * @returns {string}
     */
    buildClarificationModalContent() {
        const explanation = appTranslate('gdprModalClarificationExplanation') ||
            'Per garantire la tua sicurezza, dobbiamo capire il motivo della tua azione:';

        const revokeDescription = `<strong>${appTranslate('gdprModalRevokeButtonText') || 'Ho cambiato idea'}:</strong> ${window.getTranslation('gdprModalRevokeDescription') || 'Vuoi semplicemente revocare il consenso precedentemente dato.'}`;

        const disavowDescription = `<strong>${window.getTranslation('gdprModalDisavowButtonText') || 'Non riconosco questa azione'}:</strong> ${window.getTranslation('gdprModalDisavowDescription') || 'Non hai mai dato questo consenso (potenziale problema di sicurezza).'}`;

        return `
            <div class="text-left space-y-4">
                <p class="text-gray-700">${explanation}</p>
                <div class="space-y-3">
                    <div class="p-3 bg-amber-50 border border-amber-200 rounded-md">
                        <p class="text-sm text-amber-800">${revokeDescription}</p>
                    </div>
                    <div class="p-3 bg-red-50 border border-red-200 rounded-md">
                        <p class="text-sm text-red-800">${disavowDescription}</p>
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Costruisce il contenuto HTML del modale di conferma disconoscimento.
     * @returns {string}
     */
    buildDisavowConfirmationContent() {
        const warning = window.getTranslation('gdprModalConfirmationWarning') ||
            'Questa azione attiverà un protocollo di sicurezza che include:';

        const consequences = [
            window.getTranslation('gdprModalConsequence1') || 'Revoca immediata del consenso',
            window.getTranslation('gdprModalConsequence2') || 'Notifica al team di sicurezza',
            window.getTranslation('gdprModalConsequence3') || 'Possibili controlli aggiuntivi sull\'account',
            window.getTranslation('gdprModalConsequence4') || 'Email di conferma con istruzioni'
        ];

        const consequencesList = consequences.map(item => `<li>${item}</li>`).join('');

        const finalWarning = window.getTranslation('gdprModalFinalWarning') ||
            'Procedi solo se sei certo che non hai mai autorizzato questa azione.';

        return `
            <div class="text-left space-y-4">
                <p class="text-gray-700">${warning}</p>
                <ul class="list-disc list-inside space-y-1 text-sm text-gray-600">
                    ${consequencesList}
                </ul>
                <div class="p-3 bg-red-50 border border-red-200 rounded-md">
                    <p class="text-sm text-red-800 font-medium">
                        ${finalWarning}
                    </p>
                </div>
            </div>
        `;
    }

    /**
     * Mostra feedback di successo per azioni standard.
     * @param {string} message
     * @param {string} notificationId
     */
    showSuccessFeedback(message, notificationId) {
        this.notificationInstance.showProgressMessage(
            message,
            notificationId,
            '#10b981' // Green-500
        );

        // Toast di conferma aggiuntivo
        displaySuccess(message);
    }

    /**
     * Mostra feedback specializzato per azioni di sicurezza.
     * @param {string} message
     * @param {string} notificationId
     */
    showSecurityFeedback(message, notificationId) {
        this.notificationInstance.showProgressMessage(
            message,
            notificationId,
            '#dc2626' // Red-600
        );

        // Toast di sicurezza con styling specifico
        Swal.fire({
            title: window.getTranslation('gdprModalSecurityTitle') || 'Protocollo di Sicurezza Attivato',
            text: message,
            icon: 'info',
            confirmButtonText: window.getTranslation('gdprModalSecurityUnderstood') || 'Ho capito',
            confirmButtonColor: '#dc2626'
        });
    }

    /**
     * Aggiorna l'interfaccia utente della notifica in base al nuovo stato.
     * @param {string} notificationId
     * @param {string} newStatus
     */
    updateNotificationUI(notificationId, newStatus) {
        const notificationElement = document.querySelector(`[data-notification-id="${notificationId}"]`);

        if (notificationElement) {
            // Aggiungi classe CSS per stato visivo
            notificationElement.classList.add(`notification-${newStatus}`);

            // Disabilita i pulsanti di azione
            const actionButtons = notificationElement.querySelectorAll('.notification-action-btn');
            actionButtons.forEach(button => {
                button.disabled = true;
                button.classList.add('opacity-50', 'cursor-not-allowed');
            });

            // Aggiungi badge di stato
            const statusBadge = this.createStatusBadge(newStatus);
            notificationElement.appendChild(statusBadge);
        }
    }

    /**
     * Crea un badge visivo per lo stato della notifica.
     * @param {string} status
     * @returns {HTMLElement}
     */
    createStatusBadge(status) {
        const badge = document.createElement('div');
        badge.className = 'absolute px-2 py-1 text-xs font-medium rounded-full top-2 right-2';

        const statusConfig = {
            confirmed: {
                text: window.getTranslation('gdprStatusGranted') || 'Confermato',
                classes: 'bg-green-100 text-green-800'
            },
            revoked: {
                text: window.getTranslation('gdprStatusWithdrawn') || 'Revocato',
                classes: 'bg-amber-100 text-amber-800'
            },
            disavowed: {
                text: window.getTranslation('gdprStatusRejected') || 'Segnalato',
                classes: 'bg-red-100 text-red-800'
            }
        };

        const config = statusConfig[status] || statusConfig.confirmed;
        badge.className += ` ${config.classes}`;
        badge.textContent = config.text;

        return badge;
    }
}
