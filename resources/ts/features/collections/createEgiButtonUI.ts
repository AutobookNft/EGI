// File: resources/ts/features/collections/createEgiButtonUI.ts

import { AppConfig, appTranslate } from '../../config/appConfig';
import * as DOMElements from '../../dom/domElements';

function _updateButtonUI(
    details: { id: number | null; name: string | null } | null,
    config: AppConfig,
    DOM: typeof DOMElements
): void {
    const { createEgiButtonTextEl, createEgiButtonIconEl } = DOM;

    // DEBUG 3: Controlla se gli elementi DOM sono validi all'interno della funzione di update.
    console.log('--- DEBUG 3: Esecuzione di _updateButtonUI ---');
    console.log('Elemento Testo (createEgiButtonTextEl):', createEgiButtonTextEl);
    console.log('Dettagli ricevuti:', details);

    if (!createEgiButtonTextEl) return;

    if (details && details.id && details.name) {
        const staticText = appTranslate('add_egi_to', config.translations);
        createEgiButtonTextEl.innerHTML = `${staticText} <span class="font-semibold">${details.name}</span>`;
        if (createEgiButtonIconEl) createEgiButtonIconEl.classList.add('hidden');
    } else {
        createEgiButtonTextEl.textContent = appTranslate('create_egi', config.translations);
        if (createEgiButtonIconEl) createEgiButtonIconEl.classList.remove('hidden');
    }
}

export function initializeCreateEgiButton(config: AppConfig, DOM: typeof DOMElements): void {
    // DEBUG 1: Verifica che la funzione di inizializzazione venga chiamata.
    console.log('--- DEBUG 1: Esecuzione di initializeCreateEgiButton ---');

    const { createEgiContextualButtonEl, createEgiButtonTextEl } = DOM;

    // DEBUG 2: Ispeziona gli elementi del DOM. Questo è il punto più critico.
    console.log('--- DEBUG 2: Ispezione Elementi DOM ---');
    console.log('Pulsante (createEgiContextualButtonEl):', createEgiContextualButtonEl);
    console.log('Span per il testo (createEgiButtonTextEl):', createEgiButtonTextEl);

    if (!createEgiContextualButtonEl) {
        console.error('!!! FALLIMENTO DEBUG: Il pulsante contestuale #create-egi-contextual-button non è stato trovato. Impossibile procedere.');
        return;
    }

    const initialDetails = {
        id: config.initialUserData.current_collection_id,
        name: config.initialUserData.current_collection_name
    };
    _updateButtonUI(initialDetails, config, DOM);

    document.addEventListener('collection-changed', (event: Event) => {
        // DEBUG 4: Verifica che il listener per il cambio di collezione sia attivo.
        console.log('--- DEBUG 4: Evento "collection-changed" RICEVUTO! ---');
        const customEvent = event as CustomEvent<{ id: number; name: string; can_edit: boolean }>;

        // DEBUG 5: Ispeziona i dati ricevuti dall'evento.
        console.log('--- DEBUG 5: Dati dall\'evento ---', customEvent.detail);

        _updateButtonUI(customEvent.detail, config, DOM);
    });

    document.addEventListener('user-logged-out', () => {
        _updateButtonUI(null, config, DOM);
    });

    console.log('Padmin CreateEGI: Pulsante contestuale inizializzato e in ascolto.');
}
