import './bootstrap';
import './notification';
import Swal from 'sweetalert2';
window.Swal = Swal;

import { fetchTranslations, ensureTranslationsLoaded, getTranslation } from './utils/translations';

// Importiamo la funzione per caricare gli enum
import { loadEnums, getEnum } from './utils/enums';

// Carica gli enum all'avvio
loadEnums();

// Rende la funzione disponibile globalmente
window.getEnum = getEnum;

// Carica le traduzioni all'avvio
fetchTranslations();

// Rende le funzioni disponibili globalmente
window.getTranslation = getTranslation;
window.ensureTranslationsLoaded = ensureTranslationsLoaded;

// Import dei moduli notifiche
import './modules/notifications/init/response-notification-wallet-init';
import './modules/notifications/init/request-notification-wallet-init';
import './modules/notifications/delete-proposal-wallet';
import './modules/notifications/delete-proposal-invitation';
