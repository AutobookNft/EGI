/**
 * Factory per la creazione di un handler per i payload delle notifiche
 * @module PayloadHandlerFactory
 * @version 1.0.0
 * @package EGI Florence
 * @requires WalletStrategy
 * @requires InvitationStrategy
 * @exports PayloadHandlerFactory
 *
 */

import { WalletStrategy } from '../strategies/wallet-strategy.js';
import { InvitationStrategy } from '../strategies/invitation-strategy.js';

export class PayloadHandlerFactory {
    static create(payload, notificationInstance) {
        switch (payload) {
            case 'wallet':
                return new WalletStrategy(notificationInstance);
            case 'invitation':
                return new InvitationStrategy(notificationInstance);
            default:
                throw new Error(`Tipo di payload non gestito: ${payload}`);
        }
    }
}
