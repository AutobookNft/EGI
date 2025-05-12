// File: resources/ts/features/mobile/mobileMenu.ts

/**
 * 📜 Oracode TypeScript Module: MobileMenuHandler
 * Gestisce l'apertura e la chiusura del menu di navigazione mobile principale.
 *
 * @version 1.0.1 (Padmin Corrected Translation Calls for UEM, No Placeholders)
 * @date 2025-05-11
 * @author Padmin D. Curtis (for Fabio Cherici)
 */

import * as DOMElements from '../../dom/domElements';
import { UEM_Client_TS_Placeholder as UEM } from '../../services/uemClientService';
import { AppConfig, appTranslate } from '../../config/appConfig';

/**
 * 📜 Oracode Function: toggleMobileMenu
 * 🎯 Apre o chiude il menu di navigazione mobile, alternando la visibilità
 * degli elementi del menu e delle icone hamburger/chiusura.
 * Aggiorna l'attributo `aria-expanded` del bottone del menu per accessibilità.
 *
 * @export
 * @param {typeof DOMElements} DOM Collezione dei riferimenti agli elementi DOM necessari
 *        (mobileMenuEl, hamburgerIconEl, closeIconEl, mobileMenuButtonEl).
 * @param {AppConfig} config L'oggetto di configurazione dell'app, usato qui per passare
 *        `config.translations` a `appTranslate` per i messaggi di errore UEM.
 */
export function toggleMobileMenu(DOM: typeof DOMElements, config: AppConfig): void {
    const { mobileMenuEl, hamburgerIconEl, closeIconEl, mobileMenuButtonEl } = DOM;

    if (!mobileMenuEl || !hamburgerIconEl || !closeIconEl || !mobileMenuButtonEl) {
        UEM.handleClientError(
            'CLIENT_DOM_MISSING_MOBILE_MENU_CORE', // Codice errore UEM specifico
            {
                // Contesto per il debug
                missingElementsDetails: [
                    !mobileMenuEl ? 'mobileMenuEl (#mobile-menu)' : null,
                    !hamburgerIconEl ? 'hamburgerIconEl (#hamburger-icon)' : null,
                    !closeIconEl ? 'closeIconEl (#close-icon)' : null,
                    !mobileMenuButtonEl ? 'mobileMenuButtonEl (#mobile-menu-button)' : null,
                ].filter(Boolean).join(', ')
            },
            undefined, // Nessun errore originale
            appTranslate('errorMobileMenuElementsMissing', config.translations) // Messaggio utente
        );
        return;
    }

    mobileMenuEl.classList.toggle('hidden');
    hamburgerIconEl.classList.toggle('hidden'); // Questi sono gli elementi SVG
    closeIconEl.classList.toggle('hidden');
    mobileMenuButtonEl.setAttribute('aria-expanded', mobileMenuEl.classList.contains('hidden') ? 'false' : 'true');
}