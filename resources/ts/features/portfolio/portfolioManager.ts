/**
 * 📜 Oracode TypeScript Module: PortfolioManager
 * 🎯 Purpose: Manages real-time portfolio updates and status changes
 * 🚀 Enhancement: Polling for reservation status changes and UI updates
 *
 * @version 2.0.0 - Portfolio Fix
 * @date 2025-08-08
 * @author Padmin D. Curtis (for Fabio Cherici)
 */

// Import reservation modal system
import { initReservationModal } from '../../services/reservationService';

// --- Types ---
interface ReservationUpdate {
    type: 'outbid' | 'winning' | 'expired';
    egi_id: number;
    egi_title: string;
    old_amount?: number;
    new_amount?: number;
    message: string;
    superseded_at?: string;
}

interface PortfolioStats {
    total_owned_egis: number;
    total_spent_eur: number;
    total_bids_made: number;
    active_winning_bids: number;
    outbid_count: number;
}

interface EgiReservationStatus {
    has_reservation: boolean;
    status: 'none' | 'winning' | 'outbid';
    offer_amount_eur?: number;
    is_winning?: boolean;
    reservation_id?: number;
}

// --- State ---
export class PortfolioManager {
    private updateInterval: number = 30000; // 30 seconds
    private intervalId: number | null = null;
    private isPolling: boolean = false;
    private lastUpdateCheck: Date | null = null;

    constructor() {
        console.log('🚀 PortfolioManager initialized');
        this.init();
    }

    /**
     * Initialize the portfolio manager
     */
    private init(): void {
        // Start polling only if we're on a portfolio or user dashboard page
        if (this.shouldEnablePolling()) {
            this.startPolling();
            this.bindEvents();
        }
    }

    /**
     * Check if we should enable polling based on current page
     */
    private shouldEnablePolling(): boolean {
        const path = window.location.pathname;
        return path.includes('/collector/') || path.includes('/dashboard') || path.includes('/portfolio');
    }

    /**
     * Start polling for status updates
     */
    public startPolling(): void {
        if (this.isPolling) return;

        console.log('📡 Starting portfolio polling every', this.updateInterval / 1000, 'seconds');
        this.isPolling = true;

        // First check immediately
        this.checkReservationUpdates();

        // Then poll regularly
        this.intervalId = window.setInterval(() => {
            this.checkReservationUpdates();
        }, this.updateInterval);
    }

    /**
     * Stop polling
     */
    public stopPolling(): void {
        if (this.intervalId) {
            clearInterval(this.intervalId);
            this.intervalId = null;
        }
        this.isPolling = false;
        console.log('⏹️ Portfolio polling stopped');
    }

    /**
     * Check for reservation status updates
     */
    private async checkReservationUpdates(): Promise<void> {
        try {
            console.log('🔍 Checking for portfolio updates...');
            const response = await fetch('/api/portfolio/status-updates', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'include'
            });

            if (!response.ok) {
                console.error('❌ Portfolio API error:', response.status);
                throw new Error(`HTTP ${response.status}`);
            }

            const data = await response.json();
            console.log('📊 Portfolio API response:', data);

            if (data.success && data.updates && data.updates.length > 0) {
                console.log('🚨 Found updates:', data.updates.length);
                this.handleStatusUpdates(data.updates);
                this.updatePortfolioStats(data.stats);
            } else {
                console.log('✅ No new updates found');
            }

            this.lastUpdateCheck = new Date();

        } catch (error) {
            console.error('❌ Error checking portfolio updates:', error);
        }
    }

    /**
     * Handle status updates from server
     */
    private handleStatusUpdates(updates: ReservationUpdate[]): void {
        updates.forEach(update => {
            console.log('📢 Portfolio update:', update);

            switch (update.type) {
                case 'outbid':
                    this.handleOutbidNotification(update);
                    this.markEgiAsOutbid(update.egi_id);
                    break;
                case 'winning':
                    this.handleWinningNotification(update);
                    this.markEgiAsWinning(update.egi_id);
                    break;
                case 'expired':
                    this.handleExpiredNotification(update);
                    this.markEgiAsOutbid(update.egi_id);
                    break;
            }
        });
    }

    /**
     * Mark card as OUTBID: keep it visible, dim it, swap badge to NOT OWNED (red)
     */
    private markEgiAsOutbid(egiId: number): void {
        const el = document.querySelector<HTMLElement>(`[data-egi-id="${egiId}"]`);
        if (!el) return;
        el.classList.add('opacity-35');
        // On hover, slightly increase opacity
        if (!el.classList.contains('hover:opacity-70')) {
            el.classList.add('hover:opacity-70');
        }
        // Update portfolio badge if present
        const badge = el.querySelector<HTMLElement>('[data-portfolio-badge="1"]');
        if (badge) {
            const lbl = badge.getAttribute('data-lbl-not-owned') || 'NON POSSEDUTO';
            // Caso composito: aggiorna solo la base .not-owned-base
            const compositeBase = badge.querySelector<HTMLElement>('.not-owned-base, .owned-base');
            if (compositeBase) {
                compositeBase.classList.remove('owned-base');
                compositeBase.classList.add('not-owned-base');
                // Trova l'icona (svg)
                const svg = compositeBase.querySelector('svg');
                // Rimuovi tutti i nodi di testo
                Array.from(compositeBase.childNodes).forEach(n => {
                    if (n.nodeType === Node.TEXT_NODE) compositeBase.removeChild(n);
                });
                // Inserisci nuovo nodo di testo SEMPRE dopo svg se presente
                if (svg) {
                    // Trova la posizione dell'icona tra i child
                    const children = Array.from(compositeBase.childNodes);
                    const idx = children.indexOf(svg);
                    if (idx >= 0 && children.length > idx + 1) {
                        compositeBase.insertBefore(document.createTextNode(' ' + lbl), children[idx + 1]);
                    } else {
                        compositeBase.appendChild(document.createTextNode(' ' + lbl));
                    }
                } else {
                    compositeBase.appendChild(document.createTextNode(' ' + lbl));
                }
            } else {
                // Badge semplice: aggiorna classi e testo
                badge.classList.remove('bg-green-500/90');
                badge.classList.add('bg-red-600/90');
                badge.textContent = lbl;
            }
                    const isFirefox = navigator.userAgent.toLowerCase().includes('firefox');
                    if (isFirefox) {
                        const svg = compositeBase.querySelector('svg');
                        compositeBase.innerHTML = svg ? svg.outerHTML + ' ' + lbl : lbl;
                    } else {
                        const svg = compositeBase.querySelector('svg');
                        Array.from(compositeBase.childNodes).forEach(n => {
                            if (n.nodeType === Node.TEXT_NODE) compositeBase.removeChild(n);
                        });
                        if (svg) {
                            const children = Array.from(compositeBase.childNodes);
                            const idx = children.indexOf(svg);
                            if (idx >= 0 && children.length > idx + 1) {
                                compositeBase.insertBefore(document.createTextNode(' ' + lbl), children[idx + 1]);
                            } else {
                                compositeBase.appendChild(document.createTextNode(' ' + lbl));
                            }
                        } else {
                            compositeBase.appendChild(document.createTextNode(' ' + lbl));
                        }
                    }
            badge.setAttribute('title', lbl);
        }
    }

    /**
     * Mark card as WINNING again: restore opacity and badge
     */
    private markEgiAsWinning(egiId: number): void {
        const el = document.querySelector<HTMLElement>(`[data-egi-id="${egiId}"]`);
        if (!el) return;
        el.classList.remove('opacity-35');
        el.classList.remove('hover:opacity-70');
        const badge = el.querySelector<HTMLElement>('[data-portfolio-badge="1"]');
        if (badge) {
            const lbl = badge.getAttribute('data-lbl-winning') || 'OFFERTA VINCENTE';
            const compositeBase = badge.querySelector<HTMLElement>('.not-owned-base, .owned-base');
            if (compositeBase) {
                compositeBase.classList.remove('not-owned-base');
                compositeBase.classList.add('owned-base');
                const isFirefox = navigator.userAgent.toLowerCase().includes('firefox');
                if (isFirefox) {
                    const svg = compositeBase.querySelector('svg');
                    compositeBase.innerHTML = svg ? svg.outerHTML + ' ' + lbl : lbl;
                } else {
                    const svg = compositeBase.querySelector('svg');
                    Array.from(compositeBase.childNodes).forEach(n => {
                        if (n.nodeType === Node.TEXT_NODE) compositeBase.removeChild(n);
                    });
                    if (svg) {
                        const children = Array.from(compositeBase.childNodes);
                        const idx = children.indexOf(svg);
                        if (idx >= 0 && children.length > idx + 1) {
                            compositeBase.insertBefore(document.createTextNode(' ' + lbl), children[idx + 1]);
                        } else {
                            compositeBase.appendChild(document.createTextNode(' ' + lbl));
                        }
                    } else {
                        compositeBase.appendChild(document.createTextNode(' ' + lbl));
                    }
                }
            } else {
                badge.classList.remove('bg-red-600/90');
                badge.classList.add('bg-green-500/90');
                badge.textContent = lbl;
            }
            badge.setAttribute('title', lbl);
        }
    }

    /**
     * Handle outbid notifications
     */
    private handleOutbidNotification(update: ReservationUpdate): void {
        console.log('🚨 Showing outbid notification for EGI:', update.egi_id);
        this.showNotification({
            type: 'warning',
            title: 'You\'ve been outbid!',
            message: update.message,
            actions: [
                {
                    label: 'Make Counter-Offer',
                    action: () => this.makeCounterOffer(update.egi_id, update.new_amount || 0)
                },
                {
                    label: 'View Details',
                    action: () => window.location.href = `/egis/${update.egi_id}`
                }
            ]
        });
    }

    /**
     * Handle winning notifications
     */
    private handleWinningNotification(update: ReservationUpdate): void {
        this.showNotification({
            type: 'success',
            title: 'Congratulations!',
            message: `Your bid on "${update.egi_title}" is now winning!`,
            actions: [
                {
                    label: 'View Certificate',
                    action: () => window.location.href = `/egis/${update.egi_id}`
                }
            ]
        });
    }

    /**
     * Handle expired notifications
     */
    private handleExpiredNotification(update: ReservationUpdate): void {
        this.showNotification({
            type: 'info',
            title: 'Reservation Expired',
            message: `Your reservation on "${update.egi_title}" has expired.`,
            actions: [
                {
                    label: 'Reserve Again',
                    action: () => window.location.href = `/egis/${update.egi_id}`
                }
            ]
        });
    }

    /**
     * Remove EGI from portfolio display
     */
    private removeEgiFromPortfolio(egiId: number): void {
        const egiCard = document.querySelector(`[data-egi-id="${egiId}"]`);
        if (egiCard) {
            egiCard.classList.add('fade-out');
            setTimeout(() => {
                egiCard.remove();
                this.updatePortfolioCount(-1);
            }, 300);
        }
    }

    /**
     * Add EGI to portfolio display (if not already there)
     */
    private addEgiToPortfolio(egiId: number): void {
        const egiCard = document.querySelector(`[data-egi-id="${egiId}"]`);
        if (!egiCard) {
            // In a real implementation, we'd fetch the EGI data and add it
            // For now, just refresh the page to get updated data
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }
    }

    /**
     * Update portfolio statistics
     */
    private updatePortfolioStats(stats: PortfolioStats | undefined): void {
        if (!stats) return;

        // Update owned EGIs count
        const ownedCountEl = document.querySelector('[data-stat="owned-egis"]');
        if (ownedCountEl) {
            ownedCountEl.textContent = stats.total_owned_egis.toString();
        }

        // Update total spent
        const totalSpentEl = document.querySelector('[data-stat="total-spent"]');
        if (totalSpentEl) {
            totalSpentEl.textContent = `€${stats.total_spent_eur.toFixed(2)}`;
        }

        // Update winning bids count
        const winningBidsEl = document.querySelector('[data-stat="winning-bids"]');
        if (winningBidsEl) {
            winningBidsEl.textContent = stats.active_winning_bids.toString();
        }

        // Update outbid count
        const outbidCountEl = document.querySelector('[data-stat="outbid-count"]');
        if (outbidCountEl) {
            outbidCountEl.textContent = stats.outbid_count.toString();
        }
    }

    /**
     * Update portfolio count display
     */
    private updatePortfolioCount(change: number): void {
        const countEl = document.querySelector('[data-stat="owned-egis"]');
        if (countEl) {
            const current = parseInt(countEl.textContent || '0');
            countEl.textContent = Math.max(0, current + change).toString();
        }
    }

    /**
     * Show notification to user
     */
    private showNotification(notification: {
        type: 'success' | 'warning' | 'info' | 'error',
        title: string,
        message: string,
        actions?: Array<{label: string, action: () => void}>
    }): void {
        // Create notification element
        const notificationEl = document.createElement('div');
        notificationEl.className = `notification notification-${notification.type} fixed top-4 right-4 z-50 max-w-md p-4 rounded-lg shadow-lg`;

        const iconClass = {
            success: '✅',
            warning: '⚠️',
            info: 'ℹ️',
            error: '❌'
        }[notification.type];

        notificationEl.innerHTML = `
            <div class="flex items-start gap-3">
                <div class="text-lg">${iconClass}</div>
                <div class="flex-1">
                    <h4 class="font-semibold text-white">${notification.title}</h4>
                    <p class="text-sm text-gray-300 mt-1">${notification.message}</p>
                    ${notification.actions ? `
                        <div class="flex gap-2 mt-3">
                            ${notification.actions.map(action => `
                                <button class="btn btn-sm btn-primary notification-action" data-action-index="${notification.actions?.indexOf(action)}">
                                    ${action.label}
                                </button>
                            `).join('')}
                        </div>
                    ` : ''}
                </div>
                <button class="notification-close text-gray-400 hover:text-white">×</button>
            </div>
        `;

        // Add to DOM
        document.body.appendChild(notificationEl);

        // Bind action events
        if (notification.actions) {
            notificationEl.querySelectorAll('.notification-action').forEach((button, index) => {
                button.addEventListener('click', () => {
                    notification.actions?.[index].action();
                    this.closeNotification(notificationEl);
                });
            });
        }

        // Bind close event
        notificationEl.querySelector('.notification-close')?.addEventListener('click', () => {
            this.closeNotification(notificationEl);
        });

        // Auto-close after 10 seconds
        setTimeout(() => {
            this.closeNotification(notificationEl);
        }, 10000);

        // Animate in
        requestAnimationFrame(() => {
            notificationEl.style.transform = 'translateX(0)';
            notificationEl.style.opacity = '1';
        });
    }

    /**
     * Close notification
     */
    private closeNotification(notificationEl: HTMLElement): void {
        notificationEl.style.transform = 'translateX(100%)';
        notificationEl.style.opacity = '0';
        setTimeout(() => {
            if (notificationEl.parentNode) {
                notificationEl.parentNode.removeChild(notificationEl);
            }
        }, 300);
    }

    /**
     * Make a counter-offer for an EGI using the reservation modal
     */
    private makeCounterOffer(egiId: number, currentHighestBid: number): void {
        // Calculate suggested counter-offer (10% higher than current highest bid)
        const suggestedAmount = Math.ceil(currentHighestBid * 1.1);

        console.log(`🎯 Opening counter-offer modal for EGI ${egiId} with suggested amount: €${suggestedAmount}`);

        // Open the reservation modal
        const modal = initReservationModal(egiId);
        modal.open();

        // Pre-fill the offer amount after modal opens
        setTimeout(() => {
            this.setModalOfferAmount(suggestedAmount);
        }, 100); // Small delay to ensure modal is fully rendered
    }

    /**
     * Set the offer amount in the reservation modal
     */
    private setModalOfferAmount(amount: number): void {
        const offerInput = document.querySelector('#reservation-modal input[name="offer_amount_eur"]') as HTMLInputElement;
        if (offerInput) {
            offerInput.value = amount.toString();
            offerInput.focus();

            // Trigger input event to update any computed values (like ALGO equivalent)
            const event = new Event('input', { bubbles: true });
            offerInput.dispatchEvent(event);

            console.log(`✅ Pre-filled offer amount: €${amount}`);
        } else {
            console.error('❌ Could not find offer input field in reservation modal');
        }
    }

    /**
     * Open rebid modal for EGI (legacy method, now redirects to makeCounterOffer)
     */
    private openRebidModal(egiId: number): void {
        // Navigate to EGI page
        window.location.href = `/egis/${egiId}`;
    }

    /**
     * Bind general events
     */
    private bindEvents(): void {
        // Handle page visibility changes
        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'visible') {
                // Page became visible, check for updates
                this.checkReservationUpdates();
            }
        });

        // Handle focus events
        window.addEventListener('focus', () => {
            this.checkReservationUpdates();
        });
    }

    /**
     * Get current polling status
     */
    public getStatus(): {isPolling: boolean, lastUpdate: Date | null} {
        return {
            isPolling: this.isPolling,
            lastUpdate: this.lastUpdateCheck
        };
    }
}

// --- Initialize ---
let portfolioManager: PortfolioManager | null = null;

export function initPortfolioManager(): PortfolioManager {
    if (!portfolioManager) {
        portfolioManager = new PortfolioManager();
    }
    return portfolioManager;
}

export function getPortfolioManager(): PortfolioManager | null {
    return portfolioManager;
}

// Auto-initialize on DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        initPortfolioManager();
    });
} else {
    initPortfolioManager();
}

export default PortfolioManager;
