/**
 * @fileoverview Gestione delle azioni sulle notifiche wallet
 * @version 1.0.0
 * @package EGI Florence
 * @module NotificationActions
 *
 * @description
 * Questo modulo gestisce tutte le azioni possibili sulle notifiche:
 * - Accettazione
 * - Rifiuto
 * - Archiviazione
 * - Completamento
 *
 * @requires NotificationApi
 */

export async function handleAccept(notificationId) {
    await this.sendAction(notificationId, getEnum("NotificationStatus", "ACCEPTED"));
    const message = '💳 Wallet in accettazione!';
    const colorbg = '#3B82F6';
    this.progressStyleAdded = true;
    this.progressBarMessage(message, notificationId, colorbg);
}

export async function handleUpdate(notificationId) {
    await this.sendAction(notificationId, getEnum("NotificationStatus", "UPDATE"));
    const message = '💳 Esecuzione della modifica al wallet!';
    const colorbg = '#3B82F6';
    this.progressStyleAdded = true;
    this.progressBarMessage(message, notificationId, colorbg);
}

export async function handleReject(notificationId, reason) {
    try {
        await this.sendAction(notificationId, getEnum("NotificationStatus", "REJECTED"), reason);
        const message = '❌ Eliminazione della proposta wallet!';
        const colorbg = '#E53E3E';
        this.progressStyleAdded = true;
        this.progressBarMessage(message, notificationId, colorbg);
    } catch (error) {
        this.displayError(error.message);
    }
}

export async function handleArchive(notificationId) {
    try {
        await this.sendAction(notificationId, getEnum("NotificationStatus", "ARCHIVED"));
        const message = '🗄️ Archiviazione della notifica!';
        const colorbg = '#F6AD55';
        this.progressStyleAdded = true;
        this.progressBarMessage(message, notificationId, colorbg);
    } catch (error) {
        this.displayError(error.message);
    }
}

export async function handleDone(notificationId) {
    this.progressStyleAdded = true;
    console.log(`🚀 handleDone chiamato per la notifica ${notificationId}`);
}
