/**
 * 📜 Real-time EGI Structure Updates
 * 🎯 Purpose: Handle real-time structural changes to EGI components
 * 🧱 Core Logic: Sync structural changes across all connected browsers
 *
 * @author GitHub Copilot for Fabio Cherici
 * @version 1.0.0
 * @date 2025-08-23
 */

export interface StructureChanges {
    is_first_reservation: boolean;
    reservation_count: number;
    activator: {
        name: string;
        avatar?: string | null;
        is_commissioner: boolean;
        wallet_address?: string | null;
    };
    button_state: 'prenota' | 'rilancia';
}

/**
 * Handle structural updates for EGI cards and components
 */
export class EgiStructureUpdater {

    /**
     * Update structure for any EGI element (card, list, show page)
     *
     * @param element The DOM element to update
     * @param changes The structure changes to apply
     */
    public static updateStructure(element: HTMLElement, changes: StructureChanges): void {
        // console.log('🔄 EgiStructureUpdater: Applying structural changes', {
        //     egiId: element.dataset.egiId,
        //     changes
        // });

        // Determine element type and apply appropriate updates
        if (element.classList.contains('egi-card-list') || element.closest('.egi-card-list')) {
            this.updateEgiCardList(element, changes);
        } else if (element.classList.contains('egi-card') || element.closest('.egi-card')) {
            this.updateEgiCard(element, changes);
        } else {
            // Generic update for show page or other elements
            this.updateGenericElement(element, changes);
        }
    }

    /**
     * Update EGI card structure
     */
    private static updateEgiCard(element: HTMLElement, changes: StructureChanges): void {
        const card = (element.closest('.egi-card') || element) as HTMLElement;
        console.log('🎨 Updating egi-card structure');

        // Update activator section
        this.updateActivatorSection(card, changes.activator);

        // Update reservation button
        this.updateReservationButton(card, changes.button_state);

        // Update reservation count
        this.updateReservationCount(card, changes.reservation_count);
    }

    /**
     * Update EGI card list structure
     */
    private static updateEgiCardList(element: HTMLElement, changes: StructureChanges): void {
        const cardList = (element.closest('.egi-card-list') || element) as HTMLElement;
        console.log('📋 Updating egi-card-list structure');

        // Update activator section
        this.updateActivatorSection(cardList, changes.activator);

        // Update reservation button
        this.updateReservationButton(cardList, changes.button_state);

        // Update reservation count
        this.updateReservationCount(cardList, changes.reservation_count);
    }

    /**
     * Update generic element (show page, etc.)
     */
    private static updateGenericElement(element: HTMLElement, changes: StructureChanges): void {
        console.log('🌐 Updating generic element structure');

        // Apply updates based on element content
        this.updateActivatorSection(element, changes.activator);
        this.updateReservationButton(element, changes.button_state);
        this.updateReservationCount(element, changes.reservation_count);
    }

    /**
     * Update or create activator section
     */
    private static updateActivatorSection(container: HTMLElement, activator: StructureChanges['activator']): void {
        console.log('👤 Updating activator section', activator);

        // Look for existing activator elements
        const existingActivatorName = container.querySelector('[data-activator-name]');
        const existingActivatorSection = container.querySelector('[data-activator-section]');

        if (existingActivatorName) {
            // Update existing activator name
            existingActivatorName.textContent = activator.name;
            this.addVisualFeedback(existingActivatorName as HTMLElement, 'activator');
        } else if (existingActivatorSection) {
            // Update existing section
            const nameElement = existingActivatorSection.querySelector('[data-activator-name]');
            if (nameElement) {
                nameElement.textContent = activator.name;
                this.addVisualFeedback(nameElement as HTMLElement, 'activator');
            }
        } else {
            // Create new activator section
            this.createActivatorSection(container, activator);
        }
    }

    /**
     * Create new activator section
     */
    private static createActivatorSection(container: HTMLElement, activator: StructureChanges['activator']): void {
        // console.log('🆕 Creating new activator section');

        // Find appropriate insertion point (price section for cards)
        const priceSection = container.querySelector('.border-green-500\\/30') ||
            container.querySelector('[data-price-display]')?.closest('div');

        if (priceSection) {
            const activatorSection = document.createElement('div');
            activatorSection.className = 'flex items-center gap-2 pt-2 border-t border-green-500/20';
            activatorSection.setAttribute('data-activator-section', 'true');

            // Create avatar element
            let avatarElement = '';
            if (activator.avatar) {
                avatarElement = `<img src="${activator.avatar}" alt="${activator.name}" class="object-cover w-4 h-4 border rounded-full border-white/20 activator-avatar">`;
            } else {
                avatarElement = `
                    <div class="flex items-center justify-center flex-shrink-0 w-4 h-4 ${activator.is_commissioner ? 'bg-amber-600' : 'bg-green-600'} rounded-full activator-avatar">
                        <svg class="w-2 h-2 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                        </svg>
                    </div>
                `;
            }

            activatorSection.innerHTML = `
                ${avatarElement}
                <span class="text-xs text-green-200 truncate">
                    Attivatore: <span class="font-semibold" data-activator-name>${activator.name}</span>
                </span>
            `;

            priceSection.appendChild(activatorSection);
            this.addVisualFeedback(activatorSection, 'new-section');
        }
    }

    /**
     * Update reservation button state
     */
    private static updateReservationButton(container: HTMLElement, buttonState: StructureChanges['button_state']): void {
        console.log('🔘 Updating reservation button to state:', buttonState);

        const reserveButton = container.querySelector('.reserve-button') as HTMLElement;

        if (reserveButton && buttonState === 'rilancia') {
            // Update button text and icon
            reserveButton.innerHTML = `
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                </svg>
                Rilancia
            `;

            // Update button colors (purple → amber/orange)
            reserveButton.className = reserveButton.className
                .replace(/bg-gradient-to-r from-purple-500 to-purple-600/, 'bg-gradient-to-r from-amber-500 to-orange-600')
                .replace(/hover:from-purple-600 hover:to-purple-700/, 'hover:from-amber-600 hover:to-orange-700');

            this.addVisualFeedback(reserveButton, 'button-update');
        } else if (!reserveButton) {
            // Try fallback search by button text content
            const buttons = container.querySelectorAll('button');
            const buttonByText = Array.from(buttons).find(btn =>
                btn.textContent?.includes('Prenota') ||
                btn.textContent?.includes('Reserve')
            );

            if (buttonByText && buttonState === 'rilancia') {
                buttonByText.innerHTML = `
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                    Rilancia
                `;

                buttonByText.className = buttonByText.className
                    .replace(/bg-gradient-to-r from-purple-500 to-purple-600/, 'bg-gradient-to-r from-amber-500 to-orange-600')
                    .replace(/hover:from-purple-600 hover:to-purple-700/, 'hover:from-amber-600 hover:to-orange-700');

                this.addVisualFeedback(buttonByText as HTMLElement, 'button-update');
            }
        }
    }

    /**
     * Update reservation count display
     */
    private static updateReservationCount(container: HTMLElement, count: number): void {
        // console.log('📊 Updating reservation count to:', count);

        const existingCountSection = container.querySelector('[data-reservation-count]');

        if (existingCountSection) {
            // Update existing count
            const countText = existingCountSection.querySelector('.text-gray-300');
            if (countText) {
                countText.textContent = `${count} ${count === 1 ? 'Prenotazione' : 'Prenotazioni'}`;
                this.addVisualFeedback(countText as HTMLElement, 'count-update');
            }
        } else if (count >= 1) {
            // Create new count section
            this.createReservationCountSection(container, count);
        }
    }

    /**
     * Create new reservation count section
     */
    private static createReservationCountSection(container: HTMLElement, count: number): void {
        // console.log('🆕 Creating new reservation count section');

        // Find insertion point (after collection/creator info)
        const insertAfter = container.querySelector('[data-collection-info]') ||
            container.querySelector('[data-creator-info]') ||
            container.querySelector('.flex.items-center.gap-2:has(.text-purple-500)') ||
            container.querySelector('.flex.items-center.gap-2:has(.text-blue-500)');

        if (insertAfter) {
            const countSection = document.createElement('div');
            countSection.className = 'flex items-center gap-2 p-2 mb-2 border rounded-lg border-gray-700/50 bg-gray-800/50';
            countSection.setAttribute('data-reservation-count', 'true');

            countSection.innerHTML = `
                <div class="flex items-center justify-center flex-shrink-0 w-5 h-5 rounded-full bg-gradient-to-r from-green-500 to-emerald-500">
                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 1 1 0 000 2H6a2 2 0 00-2 2v6a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-1a1 1 0 100-2 2 2 0 012 2v8a2 2 0 01-2 2H6a2 2 0 01-2-2V5z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <span class="text-xs font-medium text-gray-300">
                        ${count} ${count === 1 ? 'Prenotazione' : 'Prenotazioni'}
                    </span>
                </div>
            `;

            insertAfter.parentNode?.insertBefore(countSection, insertAfter.nextSibling);
            this.addVisualFeedback(countSection, 'new-section');
        }
    }

    /**
     * Add visual feedback for changes
     */
    private static addVisualFeedback(element: HTMLElement, type: 'activator' | 'button-update' | 'count-update' | 'new-section'): void {
        const feedbackStyles = {
            'activator': {
                backgroundColor: '#dcfce7',
                borderColor: '#16a34a',
                fontWeight: 'bold',
                duration: 3000
            },
            'button-update': {
                transform: 'scale(1.05)',
                transition: 'all 0.3s ease',
                duration: 1000
            },
            'count-update': {
                backgroundColor: '#dcfce7',
                fontWeight: 'bold',
                duration: 2000
            },
            'new-section': {
                backgroundColor: '#dcfce7',
                borderColor: '#16a34a',
                duration: 3000
            }
        };

        const config = feedbackStyles[type];

        // Apply styles
        Object.entries(config).forEach(([prop, value]) => {
            if (prop !== 'duration' && typeof value === 'string') {
                (element.style as any)[prop] = value;
            }
        });

        // Remove styles after duration
        setTimeout(() => {
            Object.keys(config).forEach(prop => {
                if (prop !== 'duration') {
                    (element.style as any)[prop] = '';
                }
            });
        }, config.duration);
    }
}
