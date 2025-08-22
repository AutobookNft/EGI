// Real-time price updates using existing Echo instance from bootstrap.js

function inViewport(el: Element) {
    const r = el.getBoundingClientRect();
    return r.top < window.innerHeight && r.bottom > 0;
}

function mountCurrentPrice(el: HTMLElement) {
    const id = el.dataset.egiId;
    if (!id) return;

    // Use existing Echo instance from bootstrap.js
    if (!window.Echo) {
        console.warn('Current Price: Echo not available, skipping real-time updates');
        return;
    }

    const channel = window.Echo.channel(`price.${id}`);

    let pending: any = null; // coalescing base

    channel.listen('.price.updated', (msg: any) => {
        pending = msg;
        // throttle/coalesce: applica dopo un micro ritardo
        setTimeout(() => {
            if (!pending) return;
            if (!inViewport(el)) { pending = null; return; } // opzionale: aggiorna solo se visibile

            const amountEl = el.querySelector('.amount');
            const currEl = el.querySelector('.currency');

            if (amountEl) amountEl.textContent = pending.amount;
            if (currEl && pending.currency) currEl.textContent = pending.currency;

            el.classList.add('ring-2', 'ring-emerald-400'); // piccolo flash
            setTimeout(() => el.classList.remove('ring-2', 'ring-emerald-400'), 300);

            pending = null;
        }, 120);
    });
}

export function mountAllCurrentPrices() {
    document.querySelectorAll<HTMLElement>('.current-price').forEach(mountCurrentPrice);
}
