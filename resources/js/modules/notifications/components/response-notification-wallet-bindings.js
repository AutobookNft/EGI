/**
 * @fileoverview Gestione degli eventi UI per le notifiche wallet
 * @version 1.0.0
 * @package EGI Florence
 * @module NotificationBindings
 *
 * @description
 * Questo modulo gestisce tutti gli eventi dell'interfaccia utente relativi alle notifiche,
 * inclusi i click sui pulsanti di azione e sulle miniature delle notifiche.
 *
 * @requires NotificationActions
 */

// response-notification-wallet-bindings.js
export function bindNotificationEvents() {
    // Gestione click sui pulsanti (risposta, archiviazione, rifiuto, done)
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('.response-btn, .archive-btn, .reject-btn');
        if (!btn) return;

        const notificationId = btn.closest('.notification-item').dataset.notificationId;
        const action = btn.dataset.action;

        console.log(`🔘 Click su pulsante ${action} per notifica ${notificationId}`);

        if (action === getEnum("NotificationStatus", "ACCEPTED")) {
            await this.handleAccept(notificationId);
        } else if (action === getEnum("NotificationStatus", "UPDATE")) {
            await this.handleUpdate(notificationId);
        } else if (action === getEnum("NotificationStatus", "REJECTED")) {
            await this.openRejectModal(notificationId);
        } else if (action === getEnum("NotificationStatus", "ARCHIVED")) {
            this.handleArchive(notificationId);
        } else if (action === getEnum("NotificationStatus", "DONE")) {
            this.handleDone(notificationId);
        }
    });

    // Inizializza il binding dei thumbnail
    bindThumbnailClicks.call(this);
}

function bindThumbnailClicks() {
    const thumbnails = document.querySelectorAll('.notification-thumbnail');

    const setActiveState = (selectedId) => {
        thumbnails.forEach(t => {
            t.style.backgroundColor = t.dataset.notificationId === selectedId ? '#4a5568' : '#2d3748';
        });
    };

    thumbnails.forEach(thumbnail => {
        thumbnail.addEventListener('click', () => {
            const notificationId = thumbnail.dataset.notificationId;
            setActiveState(notificationId);

            console.log("🔍 Carico dettagli per notifica:", notificationId);

            thumbnails.forEach(t => t.classList.remove('bg-gray-700'));
            thumbnail.classList.add('bg-gray-700');

            const url = `/notifications/${notificationId}/details`;
            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Errore HTTP: ${response.status}`);
                    }
                    return response.text();
                })
                .then(html => {
                    const detailsContainer = document.getElementById('notification-details');
                    detailsContainer.innerHTML = html;
                })
                .catch(error => {
                    console.error("❌ Errore nel caricamento della notifica:", error);
                    document.getElementById('notification-details').innerHTML =
                        '<p class="text-red-500">Errore nel caricamento della notifica.</p>';
                });
        });
    });
}

