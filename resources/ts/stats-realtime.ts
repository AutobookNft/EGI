// Real-time statistics updates using existing Echo instance from bootstrap.js

/**
 * 📊 Stats Real-Time Module
 * @purpose Gestisce aggiornamenti real-time delle statistiche globali via WebSocket
 * @author Fabio Cherici & GitHub Copilot
 * @date 2025-08-23
 */

interface StatsUpdateMessage {
    stats: {
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
    };
    updated_at: string;
    trigger?: string;
}

/**
 * Inizializza il sistema di aggiornamento real-time delle statistiche
 */
export function initializeStatsRealTime(): void {
    console.log('📊 Initializing Stats Real-Time System...');

    // Verifica che Echo sia disponibile
    if (!window.Echo) {
        console.warn('Stats Real-Time: Echo not available, skipping real-time updates');
        return;
    }

    // Sottoscrivi al canale globale delle statistiche
    const statsChannel = window.Echo.channel('global.stats');
    
    console.log('📊 Stats channel created:', statsChannel);

    // Ascolta gli aggiornamenti delle statistiche
    statsChannel.listen('.stats.updated', (message: StatsUpdateMessage) => {
        console.log('📊 STATS UPDATE RECEIVED!', message);
        
        // Aggiorna tutte le statistiche trovate nella pagina
        updateAllStatsElements(message.stats);
        
        // Opzionale: mostra notifica visiva dell'aggiornamento
        if (message.trigger) {
            showStatsUpdateNotification(message.trigger);
        }
    });

    console.log('✅ Stats Real-Time System initialized');
}

/**
 * Aggiorna tutti gli elementi delle statistiche presenti nella pagina
 */
function updateAllStatsElements(stats: StatsUpdateMessage['stats']): void {
    // Funzione helper per formattazione abbreviata (replica della logica PHP/JS)
    function formatNumberAbbreviated(number: number, decimals = 0): string {
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

    // Aggiorna elementi delle statistiche globali (payment-distribution-stats)
    updateGlobalStatsElements(stats);
    
    // Aggiorna elementi del hero banner
    updateHeroBannerStatsElements(stats);
    
    // Aggiorna altri componenti che potrebbero avere statistiche
    updateMobileStatsElements(stats);
}

/**
 * Aggiorna gli elementi delle statistiche globali (desktop)
 */
function updateGlobalStatsElements(stats: StatsUpdateMessage['stats']): void {
    const elements = [
        { pattern: 'statVolume_', value: stats.formatted.volume },
        { pattern: 'statEpp_', value: stats.formatted.epp },
        { pattern: 'statCollections_', value: stats.formatted.collections },
        { pattern: 'statSellCollections_', value: stats.formatted.sell_collections },
        { pattern: 'statTotalEgis_', value: stats.formatted.total_egis },
        { pattern: 'statSellEgis_', value: stats.formatted.sell_egis }
    ];

    elements.forEach(({ pattern, value }) => {
        const elementsFound = document.querySelectorAll(`[id^="${pattern}"]`);
        elementsFound.forEach(element => {
            updateStatElement(element as HTMLElement, value, stats.data);
        });
    });
}

/**
 * Aggiorna gli elementi delle statistiche del hero banner
 */
function updateHeroBannerStatsElements(stats: StatsUpdateMessage['stats']): void {
    // Gli elementi del hero banner usano gli stessi pattern delle statistiche globali
    // ma potrebbero avere formattazione responsive diversa
    updateGlobalStatsElements(stats);
}

/**
 * Aggiorna gli elementi delle statistiche mobile
 */
function updateMobileStatsElements(stats: StatsUpdateMessage['stats']): void {
    // Gli elementi mobile potrebbero avere nomi diversi o logiche specifiche
    updateGlobalStatsElements(stats);
}

/**
 * Aggiorna un singolo elemento statistico con effetto visivo
 */
function updateStatElement(element: HTMLElement, formattedValue: string, rawData: any): void {
    if (!element) return;

    const currentValue = element.textContent?.trim();
    
    // Controlla se l'elemento ha formattazione responsive
    const desktopSpan = element.querySelector('.hidden.md\\:inline') as HTMLElement;
    const mobileSpan = element.querySelector('.md\\:hidden') as HTMLElement;
    
    if (desktopSpan && mobileSpan) {
        // Formattazione responsive
        const desktopValue = formattedValue;
        const mobileValue = shouldUseAbbreviated(element.id) ? 
            getAbbreviatedValue(formattedValue, rawData, element.id) : 
            formattedValue;
        
        if (desktopSpan.textContent?.trim() !== desktopValue || 
            mobileSpan.textContent?.trim() !== mobileValue) {
            
            // Applica effetto brillamento
            addShineEffect(element);
            
            desktopSpan.textContent = desktopValue;
            mobileSpan.textContent = mobileValue;
        }
    } else {
        // Formattazione semplice
        if (currentValue !== formattedValue) {
            // Applica effetto brillamento
            addShineEffect(element);
            element.textContent = formattedValue;
        }
    }
}

/**
 * Determina se l'elemento dovrebbe usare la formattazione abbreviata
 */
function shouldUseAbbreviated(elementId: string): boolean {
    // Gli elementi numerici (non monetari) usano abbreviazione se >= 1000
    return elementId.includes('TotalEgis') || elementId.includes('SellEgis') || 
           elementId.includes('Collections') || elementId.includes('SellCollections');
}

/**
 * Ottiene il valore abbreviato appropriato per mobile
 */
function getAbbreviatedValue(formattedValue: string, rawData: any, elementId: string): string {
    // Funzione per formattazione abbreviata
    function formatNumberAbbreviated(number: number, decimals = 0): string {
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

    // Mappa gli ID agli appropriati valori raw
    const fieldMap: { [key: string]: string } = {
        'statVolume_': 'volume',
        'statEpp_': 'epp',
        'statCollections_': 'collections',
        'statSellCollections_': 'sell_collections',
        'statTotalEgis_': 'total_egis',
        'statSellEgis_': 'sell_egis'
    };

    const field = Object.keys(fieldMap).find(key => elementId.includes(key.slice(0, -1)));
    if (field && rawData[fieldMap[field]]) {
        const rawValue = rawData[fieldMap[field]];
        
        // Per i valori monetari, usa il formato già fornito
        if (field.includes('Volume') || field.includes('Epp')) {
            return formattedValue;
        }
        
        // Per i numeri, usa abbreviazione se >= 1000
        if (typeof rawValue === 'number' && rawValue >= 1000) {
            return formatNumberAbbreviated(rawValue);
        }
    }
    
    return formattedValue;
}

/**
 * Aggiunge effetto brillamento all'elemento
 */
function addShineEffect(element: HTMLElement): void {
    // Rimuovi effetti precedenti
    element.style.transition = 'all 0.3s ease';
    element.style.transform = 'scale(1.05)';
    element.style.textShadow = '0 0 8px rgba(34, 197, 94, 0.6)'; // Verde per le statistiche
    
    // Reset dell'effetto dopo 300ms
    setTimeout(() => {
        element.style.transform = 'scale(1)';
        element.style.textShadow = 'none';
    }, 300);
}

/**
 * Mostra una notifica visiva dell'aggiornamento delle statistiche
 */
function showStatsUpdateNotification(trigger: string): void {
    const notification = document.createElement('div');
    notification.className = 'fixed z-50 px-3 py-1 text-xs text-white transition-all duration-300 bg-green-500 rounded-lg shadow-lg top-4 right-4';
    
    const triggerMessages: { [key: string]: string } = {
        'reservation_created': '📊 Statistiche aggiornate: nuova prenotazione',
        'reservation_cancelled': '📊 Statistiche aggiornate: prenotazione cancellata',
        'payment_distributed': '📊 Statistiche aggiornate: pagamento distribuito'
    };
    
    notification.textContent = triggerMessages[trigger] || '📊 Statistiche aggiornate';
    document.body.appendChild(notification);
    
    // Rimuovi dopo 3 secondi
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}
