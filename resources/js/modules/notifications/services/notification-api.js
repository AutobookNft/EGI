/**
 * @fileoverview Gestione delle chiamate API per le notifiche wallet
 * @version 1.0.0
 * @package EGI Florence
 * @module NotificationApi
 *
 * @description
 * Questo modulo gestisce tutte le interazioni con il backend per le notifiche,
 * fornendo metodi statici per effettuare le chiamate API necessarie.
 */


// api.js
export async function sendAction(notificationId, action, reason = null) {
    try {

        const baseUrl = this.options.apiBaseUrl || '/defaultBaseUrl';
        const url = `${baseUrl}/${notificationId}/response`;

        console.log(`🚀 Invio azione ${action} per notifica ${notificationId} a ${url}`);

        const payload = { action: action, reason: reason };
        console.log("📦 Payload inviato:", JSON.stringify(payload, null, 2));

        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(payload)
        });

        const text = await response.text();
        console.log('📢 Risposta del server:', text);

        let data;
        try {
            data = JSON.parse(text);
        } catch (error) {
            console.error('❌ Errore nel parsing JSON:', error);
            throw new Error('La risposta del server non è JSON valido.');
        }

        if (!response.ok) {
            console.error(`Errore HTTP ${response.status}:`, data);
            throw new Error(`Errore HTTP ${response.status}: ${data.message || 'Errore sconosciuto'}`);
        }

        console.log('✅ JSON ricevuto:', data);

        return data;
    } catch (error) {
        console.error(`Errore nell'azione ${action} per la notifica ${notificationId}:`, error);
        throw error;
    }
}
