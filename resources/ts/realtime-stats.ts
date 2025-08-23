// Real-time statistics updates using existing Echo instance from bootstrap.js

interface StatsData {
    data: {
        volume: number;
        epp: number;
        collections: number;
        sell_collections: number;
        total_egis: number;
        sell_egis: number;
    };
    formatted: {
        volume: string;
        epp: string;
        collections: string;
        sell_collections: string;
        total_egis: string;
        sell_egis: string;
    };
}

interface StatsBroadcastMessage {
    stats: StatsData;
    updated_at: string;
    trigger: string;
}

// Funzione helper per formattazione abbreviata (replica della PHP)
function formatNumberAbbreviated(number: number, decimals: number = 0): string {
    if (number === null || number === undefined) return '0';
    
    const num = Math.abs(number);
    const suffixes = [
        { threshold: 1000000000000, suffix: 'T' },
        { threshold: 1000000000, suffix: 'B' },
        { threshold: 1000000, suffix: 'M' },
        { threshold: 1000, suffix: 'K' }
    ];
    
    for (const { threshold, suffix } of suffixes) {
        if (num >= threshold) {
            const value = num / threshold;
            if (value >= 100) {
                return Math.round(value) + suffix;
            } else {
                return value.toFixed(decimals) + suffix;
            }
        }
    }
    
    return number.toLocaleString('it-IT');
}

// Funzione per aggiornare elemento con effetto brillamento e formattazione responsive
function updateStatElementWithEffect(element: HTMLElement, rawValue: number, formattedValue: string): void {
    if (!element) return;
    
    // Se l'elemento ha formattazione responsive
    const desktopSpan = element.querySelector('.hidden.md\\:inline') as HTMLElement;
    const mobileSpan = element.querySelector('.md\\:hidden') as HTMLElement;
    
    if (desktopSpan && mobileSpan) {
        // Formattazione responsive: desktop standard, mobile abbreviated
        let desktopValue = formattedValue;
        let mobileValue = formattedValue;
        
        // Per i numeri, usa la formattazione abbreviata su mobile
        if (typeof rawValue === 'number' && rawValue >= 1000) {
            mobileValue = formatNumberAbbreviated(rawValue);
        }
        
        // Controlla se ci sono cambiamenti
        if (desktopSpan.textContent?.trim() !== desktopValue || 
            mobileSpan.textContent?.trim() !== mobileValue) {
            
            element.style.transition = 'all 0.3s ease';
            element.style.transform = 'scale(1.05)';
            element.style.textShadow = '0 0 8px rgba(255, 255, 255, 0.6)';
            
            desktopSpan.textContent = desktopValue;
            mobileSpan.textContent = mobileValue;
            
            // Reset dell'effetto dopo 300ms
            setTimeout(() => {
                element.style.transform = 'scale(1)';
                element.style.textShadow = 'none';
            }, 300);
        }
    } else {
        // Formattazione semplice
        const currentValue = element.textContent?.trim();
        if (currentValue !== formattedValue) {
            element.style.transition = 'all 0.3s ease';
            element.style.transform = 'scale(1.05)';
            element.style.textShadow = '0 0 8px rgba(255, 255, 255, 0.6)';
            element.textContent = formattedValue;
            
            setTimeout(() => {
                element.style.transform = 'scale(1)';
                element.style.textShadow = 'none';
            }, 300);
        }
    }
}

// Funzione per inizializzare l'ascolto delle statistiche real-time
function initializeStatsRealTime(): void {
    // Use existing Echo instance from bootstrap.js
    if (!window.Echo) {
        console.warn('Real-time Stats: Echo not available, skipping real-time updates');
        return;
    }

    console.log('📊 Setting up real-time stats channel...');
    const channel = window.Echo.channel('global.stats');

    let pending: StatsBroadcastMessage | null = null;

    channel.listen('.stats.updated', (msg: StatsBroadcastMessage) => {
        console.log('📊 STATISTICHE AGGIORNATE!', msg);
        pending = msg;
        
        // Throttle/coalesce: applica dopo un micro ritardo
        setTimeout(() => {
            if (!pending) return;
            
            console.log('🎯 Aggiornamento statistiche:', pending);
            
            // Trova tutti i componenti di statistiche nella pagina
            updateAllStatsComponents(pending.stats);
            
            pending = null;
        }, 100);
    });
}

// Funzione per aggiornare tutti i componenti di statistiche nella pagina
function updateAllStatsComponents(stats: StatsData): void {
    // 1. Aggiorna hero banner stats
    updateHeroBannerStats(stats);
    
    // 2. Aggiorna payment distribution stats (desktop)
    updatePaymentDistributionStats(stats);
    
    // 3. Aggiorna payment distribution stats mobile
    updatePaymentDistributionStatsMobile(stats);
    
    // 4. Altri componenti di statistiche che potrebbero esistere
    updateGenericStatsElements(stats);
}

// Aggiorna hero banner stats
function updateHeroBannerStats(stats: StatsData): void {
    // Cerca tutti i container delle statistiche hero banner
    const containers = document.querySelectorAll('[id^="heroBannerStatsContainer_"]');
    
    containers.forEach(container => {
        const volumeElement = container.querySelector('[id^="statVolume_"]') as HTMLElement;
        const eppElement = container.querySelector('[id^="statEpp_"]') as HTMLElement;
        const totalEgisElement = container.querySelector('[id^="statTotalEgis_"]') as HTMLElement;
        const sellEgisElement = container.querySelector('[id^="statSellEgis_"]') as HTMLElement;
        
        if (volumeElement) {
            updateStatElementWithEffect(volumeElement, stats.data.volume, stats.formatted.volume);
        }
        
        if (eppElement) {
            updateStatElementWithEffect(eppElement, stats.data.epp, stats.formatted.epp);
        }
        
        if (totalEgisElement) {
            updateStatElementWithEffect(totalEgisElement, stats.data.total_egis, stats.formatted.total_egis);
        }
        
        if (sellEgisElement) {
            updateStatElementWithEffect(sellEgisElement, stats.data.sell_egis, stats.formatted.sell_egis);
        }
    });
}

// Aggiorna payment distribution stats desktop
function updatePaymentDistributionStats(stats: StatsData): void {
    const containers = document.querySelectorAll('[id^="globalStatsContainer_"]');
    
    containers.forEach(container => {
        const volumeElement = container.querySelector('[id^="statVolume_"]') as HTMLElement;
        const eppElement = container.querySelector('[id^="statEpp_"]') as HTMLElement;
        const collectionsElement = container.querySelector('[id^="statCollections_"]') as HTMLElement;
        const sellCollectionsElement = container.querySelector('[id^="statSellCollections_"]') as HTMLElement;
        const totalEgisElement = container.querySelector('[id^="statTotalEgis_"]') as HTMLElement;
        const sellEgisElement = container.querySelector('[id^="statSellEgis_"]') as HTMLElement;
        
        if (volumeElement) {
            updateStatElementWithEffect(volumeElement, stats.data.volume, stats.formatted.volume);
        }
        
        if (eppElement) {
            updateStatElementWithEffect(eppElement, stats.data.epp, stats.formatted.epp);
        }
        
        if (collectionsElement) {
            updateStatElementWithEffect(collectionsElement, stats.data.collections, stats.formatted.collections);
        }
        
        if (sellCollectionsElement) {
            updateStatElementWithEffect(sellCollectionsElement, stats.data.sell_collections, stats.formatted.sell_collections);
        }
        
        if (totalEgisElement) {
            updateStatElementWithEffect(totalEgisElement, stats.data.total_egis, stats.formatted.total_egis);
        }
        
        if (sellEgisElement) {
            updateStatElementWithEffect(sellEgisElement, stats.data.sell_egis, stats.formatted.sell_egis);
        }
    });
}

// Aggiorna payment distribution stats mobile
function updatePaymentDistributionStatsMobile(stats: StatsData): void {
    // Mobile stats potrebbero avere ID o classi diverse, adattare se necessario
    const containers = document.querySelectorAll('[id^="mobileStatsContainer_"], .mobile-stats-container');
    
    containers.forEach(container => {
        const volumeElement = container.querySelector('[id^="statVolume_"], .stat-volume') as HTMLElement;
        const eppElement = container.querySelector('[id^="statEpp_"], .stat-epp') as HTMLElement;
        const collectionsElement = container.querySelector('[id^="statCollections_"], .stat-collections') as HTMLElement;
        const sellCollectionsElement = container.querySelector('[id^="statSellCollections_"], .stat-sell-collections') as HTMLElement;
        const totalEgisElement = container.querySelector('[id^="statTotalEgis_"], .stat-total-egis') as HTMLElement;
        const sellEgisElement = container.querySelector('[id^="statSellEgis_"], .stat-sell-egis') as HTMLElement;
        
        if (volumeElement) {
            updateStatElementWithEffect(volumeElement, stats.data.volume, stats.formatted.volume);
        }
        
        if (eppElement) {
            updateStatElementWithEffect(eppElement, stats.data.epp, stats.formatted.epp);
        }
        
        if (collectionsElement) {
            updateStatElementWithEffect(collectionsElement, stats.data.collections, stats.formatted.collections);
        }
        
        if (sellCollectionsElement) {
            updateStatElementWithEffect(sellCollectionsElement, stats.data.sell_collections, stats.formatted.sell_collections);
        }
        
        if (totalEgisElement) {
            updateStatElementWithEffect(totalEgisElement, stats.data.total_egis, stats.formatted.total_egis);
        }
        
        if (sellEgisElement) {
            updateStatElementWithEffect(sellEgisElement, stats.data.sell_egis, stats.formatted.sell_egis);
        }
    });
}

// Aggiorna elementi generici con ID o classi standard
function updateGenericStatsElements(stats: StatsData): void {
    // Cerca elementi generici con classi/ID standard
    const volumeElements = document.querySelectorAll('.global-volume-stat, [data-stat="volume"]') as NodeListOf<HTMLElement>;
    const eppElements = document.querySelectorAll('.global-epp-stat, [data-stat="epp"]') as NodeListOf<HTMLElement>;
    const egisElements = document.querySelectorAll('.global-egis-stat, [data-stat="total_egis"]') as NodeListOf<HTMLElement>;
    const sellEgisElements = document.querySelectorAll('.global-sell-egis-stat, [data-stat="sell_egis"]') as NodeListOf<HTMLElement>;
    
    volumeElements.forEach(el => updateStatElementWithEffect(el, stats.data.volume, stats.formatted.volume));
    eppElements.forEach(el => updateStatElementWithEffect(el, stats.data.epp, stats.formatted.epp));
    egisElements.forEach(el => updateStatElementWithEffect(el, stats.data.total_egis, stats.formatted.total_egis));
    sellEgisElements.forEach(el => updateStatElementWithEffect(el, stats.data.sell_egis, stats.formatted.sell_egis));
}

// Inizializza quando il DOM è pronto
document.addEventListener('DOMContentLoaded', function() {
    // Ritarda leggermente l'inizializzazione per assicurarsi che Echo sia pronto
    setTimeout(initializeStatsRealTime, 1000);
});

// Export per uso in altri moduli se necessario
export { initializeStatsRealTime, updateAllStatsComponents };
