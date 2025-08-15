/**
 * Factory per la creazione di un handler per i payload delle notifiche
 * @module PayloadHandlerFactory
 * @version 1.0.0
 * @package EGI Florence
 * @requires WalletStrategy
 * @requires InvitationStrategy
 * @requires GdprStrategy
 * @exports PayloadHandlerFactory
 *
 */

import { WalletStrategy } from '../strategies/wallet-strategy.js';
import { InvitationStrategy } from '../strategies/invitation-strategy.js';
import { GdprStrategy } from '../strategies/gdpr-strategy.js'; // <-- NUOVO IMPORT
import { ReservationStrategy } from '../strategies/reservation-strategy.js'; // <-- NUOVO IMPORT

export class PayloadHandlerFactory {


    static create(payload, notificationInstance) {
        switch (payload) {
            case 'wallet':
                return new WalletStrategy(notificationInstance);
            case 'invitation':
                return new InvitationStrategy(notificationInstance);
            case 'gdpr': // <-- NUOVA VOCE
                console.log(`🔍 Creazione strategia per payload: ${notificationInstance}`);
                return new GdprStrategy(notificationInstance);
            case 'reservation':
                return new ReservationStrategy(notificationInstance);
            default:
                // Restituiamo null invece di lanciare un errore per gestire con grazia tipi non ancora implementati.
                console.warn(`Tipo di payload non gestito: ${payload}`);
                return null;
        }
    }
}
