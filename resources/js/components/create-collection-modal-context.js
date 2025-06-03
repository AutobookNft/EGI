/**
 * @Oracode OS1: Context-Aware Enhancements for Create Collection Modal
 * 🎯 Purpose: Provide layout-specific behavior and styling
 * 🧱 Core Logic: Detect layout context and adapt modal behavior accordingly
 * 🛡️ Privacy: No personal data processing, context detection only
 *
 * @since OS1-v1.0
 * @author Padmin D. Curtis (for Fabio Cherici)
 */

class CreateCollectionModalContext {
    constructor() {
        this.context = this.detectContext();
        this.initialize();
    }

    /**
     * @Oracode OS1: Detect current layout context
     * 🎯 Purpose: Determine whether we're in guest or dashboard layout
     */
    detectContext() {
        // Check for dashboard-specific elements
        if (document.querySelector('[x-data]') || document.querySelector('.livewire')) {
            return 'dashboard';
        }

        // Check for guest-specific elements
        if (document.getElementById('hero-section') || document.querySelector('.guest-layout')) {
            return 'guest';
        }

        // Check user data script for context hint
        const userDataScript = document.getElementById('user-collection-data');
        if (userDataScript) {
            try {
                const userData = JSON.parse(userDataScript.textContent);
                return userData.context || 'guest';
            } catch (e) {
                // Fallback detection
            }
        }

        // Default fallback
        return 'guest';
    }

    /**
     * @Oracode OS1: Initialize context-specific enhancements
     * 🎯 Purpose: Apply layout-specific modifications
     */
    initialize() {
        this.applyContextualStyling();
        this.setupContextualBehavior();
        this.trackContext();
    }

    /**
     * @Oracode OS1: Apply context-specific styling
     * 🎯 Purpose: Adapt modal appearance to layout theme
     */
    applyContextualStyling() {
        const modal = document.getElementById('create-collection-modal');
        if (!modal) return;

        // Add context class for specific styling
        modal.classList.add(`modal-context-${this.context}`);

        // Dashboard-specific styling adjustments
        if (this.context === 'dashboard') {
            const modalContainer = document.getElementById('create-collection-modal-container');
            if (modalContainer) {
                // Lighter theme for dashboard
                modalContainer.classList.add('dashboard-theme');
            }
        }
    }

    /**
     * @Oracode OS1: Setup context-specific behavior
     * 🎯 Purpose: Modify modal behavior based on layout
     */
    setupContextualBehavior() {
        // Dashboard-specific: Close modal on successful creation without redirect delay
        if (this.context === 'dashboard') {
            this.setupDashboardBehavior();
        } else {
            this.setupGuestBehavior();
        }
    }

    /**
     * @Oracode OS1: Dashboard-specific behavior setup
     * 🎯 Purpose: Optimize UX for dashboard context
     */
    setupDashboardBehavior() {
        // Override success handler for dashboard
        const originalHandleSuccess = window.CreateCollectionModal?.instance()?.handleSuccess;

        if (originalHandleSuccess) {
            window.CreateCollectionModal.instance().handleSuccess = (result) => {
                // Show success state briefly
                this.showQuickSuccess(result);

                // Shorter redirect delay for dashboard
                setTimeout(() => {
                    if (result.next_action?.url) {
                        window.location.href = result.next_action.url;
                    } else {
                        // Dashboard fallback: reload current page
                        window.location.reload();
                    }
                }, 1500); // Shorter delay for dashboard
            };
        }
    }

    /**
     * @Oracode OS1: Guest-specific behavior setup
     * 🎯 Purpose: Optimize UX for guest/marketing context
     */
    setupGuestBehavior() {
        // Guest layout keeps default behavior (longer success display)
        // Could add guest-specific enhancements here
        console.info('[CreateCollectionModalContext] Guest behavior initialized');
    }

    /**
     * @Oracode OS1: Quick success display for dashboard
     * 🎯 Purpose: Faster feedback cycle for dashboard users
     */
    showQuickSuccess(result) {
        const modal = window.CreateCollectionModal?.instance();
        if (!modal) return;

        // Quick transition to success state
        const form = document.getElementById('create-collection-form');
        const successState = document.getElementById('create-collection-success-state');

        if (form && successState) {
            form.classList.add('hidden');
            successState.classList.remove('hidden');

            // Update success message
            const successMessage = document.getElementById('success-message');
            if (successMessage) {
                successMessage.textContent = result.message;
            }

            // Faster progress bar
            const progressBar = document.getElementById('redirect-progress');
            if (progressBar) {
                progressBar.style.transition = 'width 1.5s linear';
                requestAnimationFrame(() => {
                    progressBar.style.width = '100%';
                });
            }
        }
    }

    /**
     * @Oracode OS1: Track context for analytics
     * 🎯 Purpose: Understand usage patterns across layouts
     */
    trackContext() {
        if (window.CreateCollectionModal?.instance()?.trackEvent) {
            window.CreateCollectionModal.instance().trackEvent('modal_context_detected', {
                context: this.context,
                user_agent: navigator.userAgent,
                viewport: `${window.innerWidth}x${window.innerHeight}`
            });
        }
    }
}

// OS1 Initialization
document.addEventListener('DOMContentLoaded', () => {
    // Wait for main modal to initialize
    setTimeout(() => {
        new CreateCollectionModalContext();
    }, 100);
});
