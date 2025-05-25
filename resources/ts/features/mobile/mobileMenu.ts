// File: resources/ts/features/mobile/mobileMenu.ts

/**
 * 📜 Oracode TypeScript Module: MobileMenuHandler
 * Gestisce l'apertura e la chiusura del menu di navigazione mobile principale.
 *
 * @version 1.1.0 (Padmin - Revised toggle logic based on aria-expanded)
 * @date 2025-05-24
 * @author Padmin D. Curtis (for Fabio Cherici)
 */

import * as DOMElements from '../../dom/domElements'; // Assumendo che DOMElements sia importato correttamente
import { AppConfig, appTranslate } from '../../config/appConfig';
import { UEM_Client_TS_Placeholder as UEM } from '../../services/uemClientService'; // Assumendo che UEM sia importato

/**
 * 📜 Oracode Function: toggleMobileMenu
 * 🎯 Apre o chiude il menu di navigazione mobile.
 * La logica si basa sullo stato corrente di 'aria-expanded' del bottone
 * per determinare se aprire o chiudere il menu, rendendo il toggle più robusto.
 * Aggiorna la visibilità degli elementi del menu, delle icone hamburger/chiusura,
 * e l'attributo `aria-expanded` del bottone.
 *
 * @export
 * @param {typeof DOMElements} DOM Collezione dei riferimenti agli elementi DOM.
 * @param {AppConfig} config L'oggetto di configurazione dell'app.
 */
// File: resources/ts/features/mobile/mobileMenu.ts

export function toggleMobileMenu(DOM: typeof DOMElements, config: AppConfig): void {
    // Riconferma il riferimento all'elemento bottone usando l'ID ogni volta,
    // per essere assolutamente sicuri che stiamo operando sull'elemento corretto nel DOM attuale.
    // Questo è un po' ridondante se DOMElements.initializeDOMReferences() ha funzionato bene,
    // ma serve a eliminare ogni dubbio sul riferimento.
    const currentMobileMenuButtonInDOM = document.getElementById('mobile-menu-button') as HTMLButtonElement | null;

    // Usa l'elemento passato da DOM se quello fresco non è trovato, ma logga una discrepanza.
    const buttonToUse = currentMobileMenuButtonInDOM || DOM.mobileMenuButtonEl;

    if (!buttonToUse) {
        console.error('MOBILEMENU.TS: mobileMenuButtonEl (#mobile-menu-button) is NULL or UNDEFINED. Cannot proceed.');
        // UEM.handleClientError(...)
        return;
    }

    // Estrai gli altri elementi dall'oggetto DOM passato, assumendo che siano stabili
    // una volta che initializeDOMReferences è stato chiamato.
    const { mobileMenuEl, hamburgerIconEl, closeIconEl } = DOM;

    if (!mobileMenuEl || !hamburgerIconEl || !closeIconEl) {
        console.error('MOBILEMENU.TS: One of mobileMenuEl, hamburgerIconEl, or closeIconEl is missing.');
        // UEM.handleClientError(...)
        return;
    }

    // Logga lo stato dell'attributo PRIMA di qualsiasi logica
    const initialAriaExpanded = buttonToUse.getAttribute('aria-expanded');
    console.log(`[MOBILEMENU.TS - CLICK START] mobile-menu-button current aria-expanded: "${initialAriaExpanded}" (type: ${typeof initialAriaExpanded})`);

    const isCurrentlyExpanded = initialAriaExpanded === 'true';
    console.log(`[MOBILEMENU.TS] Based on attribute, isCurrentlyExpanded: ${isCurrentlyExpanded}`);

    if (isCurrentlyExpanded) {
        // AZIONE: CHIUDERE IL MENU
        console.log('[MOBILEMENU.TS] Action: Closing menu.');
        mobileMenuEl.classList.add('hidden');
        hamburgerIconEl.classList.remove('hidden');
        closeIconEl.classList.add('hidden');
        buttonToUse.setAttribute('aria-expanded', 'false');
    } else {
        // AZIONE: APRIRE IL MENU
        console.log('[MOBILEMENU.TS] Action: Opening menu.');
        mobileMenuEl.classList.remove('hidden');
        hamburgerIconEl.classList.add('hidden');
        closeIconEl.classList.remove('hidden');
        buttonToUse.setAttribute('aria-expanded', 'true');
    }

    const finalAriaExpanded = buttonToUse.getAttribute('aria-expanded');
    console.log(`[MOBILEMENU.TS - CLICK END] mobile-menu-button aria-expanded AFTER set: "${finalAriaExpanded}"`);
    console.log(`[MOBILEMENU.TS] mobileMenuEl classes: "${mobileMenuEl.classList.toString()}"`);
    console.log(`[MOBILEMENU.TS] hamburgerIconEl hidden: ${hamburgerIconEl.classList.contains('hidden')}`);
    console.log(`[MOBILEMENU.TS] closeIconEl hidden: ${closeIconEl.classList.contains('hidden')}`);
}
