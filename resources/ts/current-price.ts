// Real-time price updates using existing Echo instance from bootstrap.js

import { EgiDisplayUpdater } from './services/reservation/ui/EgiDisplayUpdater';

function inViewport(el: Element) {
    const r = el.getBoundingClientRect();
    return r.top < window.innerHeight && r.bottom > 0;
}

function mountCurrentPrice(el: HTMLElement) {
    const id = el.dataset.egiId;
    if (!id) {
        console.warn('❌ No EGI ID found for element:', el);
        return;
    }

    // Use existing Echo instance from bootstrap.js
    if (!window.Echo) {
        console.warn('Current Price: Echo not available, skipping real-time updates');
        return;
    }

    // console.log('✅ Echo available, setting up channel for ID:', id);
    const channel = window.Echo.channel(`price.${id}`);

    // console.log('📡 Channel created:', channel);

    let pending: any = null; // coalescing base

    channel.listen('.price.updated', (msg: any) => {
        console.log('🔥 MESSAGGIO RICEVUTO!', msg);
        pending = msg;
        // throttle/coalesce: applica dopo un micro ritardo
        setTimeout(() => {
            if (!pending) return;
            if (!inViewport(el)) { pending = null; return; } // opzionale: aggiorna solo se visibile

            console.log('🎯 Aggiornamento prezzo:', pending);

            // 🎯 Detecta se siamo nella pagina di dettaglio EGI
            const currentPath = window.location.pathname;
            const isDetailPage = currentPath.includes('/egis/') &&
                !currentPath.includes('/egis?') &&
                !currentPath.endsWith('/egis') &&
                !currentPath.endsWith('/egis/');

            console.log('🔍 Path check:', {
                currentPath,
                isDetailPage,
                hasStructureChanges: !!pending.structure_changes
            });

            if (isDetailPage && pending.structure_changes) {
                // 🔄 RELOAD automatico per pagina di dettaglio con cambiamenti strutturali
                console.log('🔄 Reloading page per aggiornamenti strutturali EGI detail');

                // Effetto visivo prima del reload
                el.classList.add('ring-2', 'ring-blue-400');

                // Mostra notifica rapida
                const notification = document.createElement('div');
                notification.className = 'fixed z-50 px-4 py-2 text-white transition-all duration-300 bg-blue-500 rounded-lg shadow-lg top-4 right-4';
                notification.textContent = 'Aggiornamento EGI rilevato, ricaricando...';
                document.body.appendChild(notification);

                // Reload dopo breve delay
                setTimeout(() => {
                    window.location.reload();
                }, 1500);

                pending = null;
                return;
            }

            // 🆕 Usa EgiDisplayUpdater per aggiornamenti completi nelle card
            if (pending.structure_changes) {
                console.log('🏗️ Aggiornamento con structure_changes:', pending.structure_changes);
                // Aggiornamento completo con cambiamenti strutturali
                EgiDisplayUpdater.updateFromBroadcast(parseInt(id), {
                    amount: pending.amount,
                    currency: pending.currency,
                    structure_changes: pending.structure_changes
                });
            } else {
                console.log('💰 Solo aggiornamento prezzo (legacy)');
                // Solo aggiornamento prezzo (legacy)
                const amountEl = el.querySelector('.amount');
                const currEl = el.querySelector('.currency');

                if (amountEl) amountEl.textContent = pending.amount;
                if (currEl && pending.currency) currEl.textContent = pending.currency;
            }

            // Effetto visivo
            el.classList.add('ring-2', 'ring-emerald-400');
            setTimeout(() => el.classList.remove('ring-2', 'ring-emerald-400'), 300);

            pending = null;
        }, 120);
    });
}

export function mountAllCurrentPrices() {
    console.log('🚀 mountAllCurrentPrices() chiamato');
    const elements = document.querySelectorAll<HTMLElement>('.current-price');
    console.log(`📊 Trovati ${elements.length} elementi .current-price`);

    elements.forEach((el, index) => {
        // console.log(`🎯 Montaggio elemento ${index + 1}:`, el);
        mountCurrentPrice(el);
    });
}
