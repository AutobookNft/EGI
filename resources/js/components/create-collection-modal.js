/**
 * @Oracode OS1: Create Collection Modal Controller
 * 🎯 Purpose: Intelligent modal management with robust UX and accessibility
 * 🧱 Core Logic: Event-driven modal lifecycle with AJAX form handling and state management
 * 🛡️ Privacy: No data persistence beyond session, respects user privacy
 * 📥 Input: User interactions (clicks, keyboard, form submission)
 * 📤 Output: Modal state changes, AJAX requests, navigation actions
 * 🔄 Flow: Open → Validate → Submit → Handle Response → Close/Redirect
 *
 * @accessibility Full keyboard support, focus management, ARIA states
 * @performance Lazy initialization, event delegation, minimal DOM manipulation
 * @error-handling UEM-aware error processing with graceful degradation
 * @ux-enhancement Progressive states, real-time feedback, intelligent validation
 *
 * @since OS1-v1.0
 * @author Padmin D. Curtis (for Fabio Cherici)
 */

class CreateCollectionModal {
    /**
     * @Oracode OS1: Initialize modal controller with intelligent defaults
     * 🎯 Purpose: Setup modal with all required event handlers and state management
     */
    constructor() {
        // OS1 Pillar 1: Explicit Intention - Declare all operational properties
        this.modal = null;
        this.modalContainer = null;
        this.form = null;
        this.nameInput = null;
        this.submitButton = null;
        this.isOpen = false;
        this.isSubmitting = false;
        this.focusedElementBeforeModal = null;
        this.redirectTimer = null;
        this.validationTimeout = null;

        // OS1 Pillar 2: Empowering Simplicity - Single initialization entry point
        this.initialize();
    }

    /**
     * @Oracode OS1: Initialize modal system with comprehensive setup
     * 🎯 Purpose: Setup DOM references, event listeners, and initial state
     */
    initialize() {
        // Wait for DOM ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setup());
        } else {
            this.setup();
        }
    }

    /**
     * @Oracode OS1: Setup modal components and bindings
     * 🎯 Purpose: Establish all DOM references and event handlers
     */
    setup() {
        try {
            // OS1 Pillar 3: Semantic Coherence - Consistent element targeting
            this.modal = document.getElementById('create-collection-modal');
            this.modalContainer = document.getElementById('create-collection-modal-container');
            this.form = document.getElementById('create-collection-form');
            this.nameInput = document.getElementById('collection_name');
            this.submitButton = document.getElementById('submit-create-collection');

            // Enhanced validation for critical elements
            if (!this.modal || !this.form || !this.nameInput || !this.submitButton) {
                console.warn('[CreateCollectionModal] Missing required DOM elements');
                return;
            }

            this.bindEvents();
            this.loadUserStats();

            // OS1 Pillar 5: Recursive Evolution - Log successful initialization
            console.info('[CreateCollectionModal] Initialized successfully');

        } catch (error) {
            console.error('[CreateCollectionModal] Setup failed:', error);
        }
    }

    /**
     * @Oracode OS1: Comprehensive event binding with accessibility support
     * 🎯 Purpose: Establish all user interaction handlers
     */
    bindEvents() {
        // OS1 Enhanced: Form submission with robust validation
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));

        // Modal control events
        document.getElementById('close-create-collection-modal')?.addEventListener('click', () => this.close());
        document.getElementById('cancel-create-collection')?.addEventListener('click', () => this.close());

        // OS1 Accessibility: Enhanced keyboard support
        document.addEventListener('keydown', (e) => this.handleKeydown(e));

        // Modal backdrop click to close
        this.modal.addEventListener('click', (e) => {
            if (e.target === this.modal) {
                this.close();
            }
        });

        // OS1 UX Enhancement: Real-time character counter and validation
        this.nameInput.addEventListener('input', () => this.handleInputChange());
        this.nameInput.addEventListener('blur', () => this.validateField());

        // OS1 Performance: Debounced validation
        this.nameInput.addEventListener('input', () => {
            clearTimeout(this.validationTimeout);
            this.validationTimeout = setTimeout(() => this.validateField(), 300);
        });

        // OS1 Trigger buttons throughout the application
        this.bindTriggerButtons();
    }

    /**
     * @Oracode OS1: Bind trigger buttons across different layouts
     * 🎯 Purpose: Enable modal opening from any context (guest, dashboard, etc.)
     */
    bindTriggerButtons() {
        // Generic trigger selector for flexibility
        const triggers = document.querySelectorAll('[data-action="open-create-collection-modal"]');

        triggers.forEach(trigger => {
            trigger.addEventListener('click', (e) => {
                e.preventDefault();
                this.open();
            });
        });

        // OS1 Evolution: Legacy support for existing buttons
        const legacyTriggers = document.querySelectorAll('.create-collection-trigger, #create-collection-button');
        legacyTriggers.forEach(trigger => {
            trigger.addEventListener('click', (e) => {
                e.preventDefault();
                this.open();
            });
        });
    }

    /**
     * @Oracode OS1: Open modal with enhanced UX and accessibility
     * 🎯 Purpose: Display modal with proper focus management and state setup
     */
    open() {
        if (this.isOpen) return;

        try {
            // OS1 Accessibility: Store current focus for restoration
            this.focusedElementBeforeModal = document.activeElement;

            // OS1 State Management: Reset modal to clean state
            this.resetModal();

            // OS1 UX: Smooth modal appearance
            this.modal.classList.remove('hidden');
            this.modal.setAttribute('aria-hidden', 'false');

            // Trigger transition after DOM update
            requestAnimationFrame(() => {
                this.modal.classList.add('modal-open');
                this.modalContainer.style.transform = 'scale(1)';
                this.modalContainer.style.opacity = '1';
            });

            // OS1 Accessibility: Focus management
            setTimeout(() => {
                this.nameInput.focus();
            }, 150);

            // OS1 Body scroll prevention
            document.body.style.overflow = 'hidden';

            this.isOpen = true;

            // OS1 Analytics: Track modal opening
            this.trackEvent('modal_opened');

        } catch (error) {
            console.error('[CreateCollectionModal] Open failed:', error);
        }
    }

    /**
     * @Oracode OS1: Close modal with complete cleanup
     * 🎯 Purpose: Hide modal and restore application state
     */
    close() {
        if (!this.isOpen) return;

        try {
            // OS1 UX: Smooth modal disappearance
            this.modal.classList.remove('modal-open');
            this.modalContainer.style.transform = 'scale(0.95)';
            this.modalContainer.style.opacity = '0';

            // Complete closure after transition
            setTimeout(() => {
                this.modal.classList.add('hidden');
                this.modal.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';

                // OS1 Accessibility: Restore previous focus
                if (this.focusedElementBeforeModal) {
                    this.focusedElementBeforeModal.focus();
                }
            }, 300);

            // OS1 Cleanup: Clear any pending operations
            if (this.redirectTimer) {
                clearTimeout(this.redirectTimer);
                this.redirectTimer = null;
            }

            this.isOpen = false;

            // OS1 Analytics: Track modal closing
            this.trackEvent('modal_closed');

        } catch (error) {
            console.error('[CreateCollectionModal] Close failed:', error);
        }
    }

    /**
     * @Oracode OS1: Reset modal to pristine state
     * 🎯 Purpose: Clear all form data and error states
     */
    resetModal() {
        // Reset form
        this.form.reset();

        // Clear error states
        this.clearErrors();

        // Reset UI states
        document.getElementById('create-collection-success-state').classList.add('hidden');
        this.form.classList.remove('hidden');

        // Reset button state
        this.setSubmitButtonState('default');

        // Reset character counter
        this.updateCharacterCounter();

        // Reset submission flag
        this.isSubmitting = false;
    }

    /**
     * @Oracode OS1: Handle form submission with comprehensive validation
     * 🎯 Purpose: Process form data with robust error handling and UX feedback
     */
    async handleSubmit(event) {
        event.preventDefault();

        if (this.isSubmitting) return;

        try {
            // OS1 Validation: Client-side pre-validation
            if (!this.validateForm()) {
                return;
            }

            this.isSubmitting = true;
            this.setSubmitButtonState('loading');
            this.clearErrors();

            // OS1 Data Preparation
            const formData = new FormData(this.form);
            const requestData = {
                collection_name: formData.get('collection_name').trim(),
                _token: formData.get('_token')
            };

            // OS1 AJAX Request with comprehensive error handling
            const response = await fetch('/collections/create', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(requestData)
            });

            const result = await response.json();

            if (result.success) {
                this.handleSuccess(result);
            } else {
                this.handleError(result);
            }

        } catch (error) {
            this.handleNetworkError(error);
        } finally {
            this.isSubmitting = false;
        }
    }

    /**
     * @Oracode OS1: Handle successful collection creation
     * 🎯 Purpose: Display success state and manage redirection
     */
    handleSuccess(result) {
        // OS1 UX: Transition to success state
        this.form.classList.add('hidden');
        const successState = document.getElementById('create-collection-success-state');
        successState.classList.remove('hidden');

        // Update success message
        const successMessage = document.getElementById('success-message');
        successMessage.textContent = result.message;

        // OS1 Progress Indication: Animate redirect progress
        const progressBar = document.getElementById('redirect-progress');
        requestAnimationFrame(() => {
            progressBar.style.width = '100%';
        });

        // OS1 Analytics: Track successful creation
        this.trackEvent('collection_created', {
            collection_id: result.collection?.id,
            collection_name: result.collection?.name
        });

        // OS1 Navigation: Intelligent redirect with delay
        this.redirectTimer = setTimeout(() => {
            if (result.next_action?.url) {
                window.location.href = result.next_action.url;
            } else {
                // Fallback redirect
                window.location.reload();
            }
        }, 3000);
    }

    /**
     * @Oracode OS1: Handle server errors with intelligent feedback
     * 🎯 Purpose: Display appropriate error messages and enable recovery
     */
    handleError(result) {
        this.setSubmitButtonState('default');

        // OS1 Error Classification: Handle different error types
        if (result.errors) {
            // Validation errors
            this.displayValidationErrors(result.errors);
        } else {
            // General server errors
            this.displayGlobalError(result.message || 'An unexpected error occurred');
        }

        // OS1 Accessibility: Announce error to screen readers
        const errorElement = document.getElementById('global-error-message');
        if (errorElement && !errorElement.classList.contains('hidden')) {
            errorElement.focus();
        }

        // OS1 Analytics: Track errors for improvement
        this.trackEvent('creation_error', {
            error_type: result.error,
            error_message: result.message
        });
    }

    /**
     * @Oracode OS1: Handle network errors with graceful degradation
     * 🎯 Purpose: Provide fallback UX for connectivity issues
     */
    handleNetworkError(error) {
        console.error('[CreateCollectionModal] Network error:', error);

        this.setSubmitButtonState('default');
        this.displayGlobalError('Network error. Please check your connection and try again.');

        // OS1 Analytics: Track network issues
        this.trackEvent('network_error', { error: error.message });
    }

    /**
     * @Oracode OS1: Client-side form validation
     * 🎯 Purpose: Immediate feedback before server submission
     */
    validateForm() {
        const name = this.nameInput.value.trim();

        // Clear previous errors
        this.clearFieldError('collection_name');

        // Required validation
        if (!name) {
            this.displayFieldError('collection_name', 'Collection name is required');
            return false;
        }

        // Length validation
        if (name.length < 2) {
            this.displayFieldError('collection_name', 'Collection name must be at least 2 characters');
            return false;
        }

        if (name.length > 100) {
            this.displayFieldError('collection_name', 'Collection name cannot exceed 100 characters');
            return false;
        }

        // Character validation
        const validPattern = /^[a-zA-Z0-9\s\-_'"À-ÿ]+$/u;
        if (!validPattern.test(name)) {
            this.displayFieldError('collection_name', 'Collection name contains invalid characters');
            return false;
        }

        return true;
    }

    /**
     * @Oracode OS1: Real-time field validation
     * 🎯 Purpose: Provide immediate feedback during typing
     */
    validateField() {
        const name = this.nameInput.value.trim();

        // Clear previous error
        this.clearFieldError('collection_name');

        if (name && name.length > 0 && name.length < 2) {
            this.displayFieldError('collection_name', 'Minimum 2 characters required');
        }
    }

    /**
     * @Oracode OS1: Handle input changes with character counter
     * 🎯 Purpose: Update character counter and provide visual feedback
     */
    handleInputChange() {
        this.updateCharacterCounter();

        // Clear validation error while typing
        this.clearFieldError('collection_name');
    }

    /**
     * @Oracode OS1: Update character counter with visual states
     * 🎯 Purpose: Provide real-time character count feedback
     */
    updateCharacterCounter() {
        const currentLength = this.nameInput.value.length;
        const maxLength = 100;
        const counter = document.getElementById('character-counter');
        const currentSpan = document.getElementById('current-length');

        if (currentSpan) {
            currentSpan.textContent = currentLength;
        }

        // OS1 UX: Visual feedback based on character count
        if (counter) {
            counter.classList.remove('text-warning', 'text-danger');

            if (currentLength > 80) {
                counter.classList.add('text-warning');
            }
            if (currentLength > 95) {
                counter.classList.add('text-danger');
            }
        }
    }

    /**
     * @Oracode OS1: Enhanced keyboard event handling
     * 🎯 Purpose: Provide comprehensive keyboard accessibility
     */
    handleKeydown(event) {
        if (!this.isOpen) return;

        // ESC key to close modal
        if (event.key === 'Escape') {
            event.preventDefault();
            this.close();
            return;
        }

        // Tab key focus management
        if (event.key === 'Tab') {
            this.handleTabNavigation(event);
        }
    }

    /**
     * @Oracode OS1: Focus trap for modal accessibility
     * 🎯 Purpose: Keep focus within modal during keyboard navigation
     */
    handleTabNavigation(event) {
        const focusableElements = this.modal.querySelectorAll(
            'button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
        );

        const firstElement = focusableElements[0];
        const lastElement = focusableElements[focusableElements.length - 1];

        if (event.shiftKey) {
            // Shift + Tab
            if (document.activeElement === firstElement) {
                event.preventDefault();
                lastElement.focus();
            }
        } else {
            // Tab
            if (document.activeElement === lastElement) {
                event.preventDefault();
                firstElement.focus();
            }
        }
    }

    /**
     * @Oracode OS1: Submit button state management
     * 🎯 Purpose: Provide clear visual feedback during submission process
     */
    setSubmitButtonState(state) {
        const defaultText = document.getElementById('submit-text-default');
        const loadingText = document.getElementById('submit-text-loading');

        switch (state) {
            case 'loading':
                this.submitButton.disabled = true;
                defaultText.classList.add('hidden');
                loadingText.classList.remove('hidden');
                loadingText.classList.add('flex');
                break;

            case 'default':
            default:
                this.submitButton.disabled = false;
                defaultText.classList.remove('hidden');
                loadingText.classList.add('hidden');
                loadingText.classList.remove('flex');
                break;
        }
    }

    /**
     * @Oracode OS1: Display field-specific validation errors
     * 🎯 Purpose: Show contextual error messages for form fields
     */
    displayFieldError(fieldName, message) {
        const errorContainer = document.getElementById(`${fieldName}-error`);
        if (errorContainer) {
            errorContainer.textContent = message;
            errorContainer.classList.remove('hidden');
        }

        // Add error styling to input
        const input = document.getElementById(fieldName);
        if (input) {
            input.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
        }
    }

    /**
     * @Oracode OS1: Clear field-specific errors
     * 🎯 Purpose: Remove error states from form fields
     */
    clearFieldError(fieldName) {
        const errorContainer = document.getElementById(`${fieldName}-error`);
        if (errorContainer) {
            errorContainer.classList.add('hidden');
        }

        // Remove error styling from input
        const input = document.getElementById(fieldName);
        if (input) {
            input.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
        }
    }

    /**
     * @Oracode OS1: Display validation errors from server
     * 🎯 Purpose: Show server-side validation feedback
     */
    displayValidationErrors(errors) {
        Object.keys(errors).forEach(fieldName => {
            const messages = errors[fieldName];
            if (messages && messages.length > 0) {
                this.displayFieldError(fieldName, messages[0]);
            }
        });
    }

    /**
     * @Oracode OS1: Display global error messages
     * 🎯 Purpose: Show application-level error feedback
     */
    displayGlobalError(message) {
        const errorContainer = document.getElementById('global-error-message');
        const errorText = document.getElementById('global-error-text');

        if (errorContainer && errorText) {
            errorText.textContent = message;
            errorContainer.classList.remove('hidden');
        }
    }

    /**
     * @Oracode OS1: Clear all error states
     * 🎯 Purpose: Reset form to clean state
     */
    clearErrors() {
        // Clear field errors
        this.clearFieldError('collection_name');

        // Clear global error
        const globalError = document.getElementById('global-error-message');
        if (globalError) {
            globalError.classList.add('hidden');
        }
    }

    /**
     * @Oracode OS1: Load and display user collection statistics
     * 🎯 Purpose: Provide contextual information about user's collection usage
     */
    loadUserStats() {
        const userDataScript = document.getElementById('user-collection-data');
        const statsElement = document.getElementById('user-collection-stats');

        if (userDataScript && statsElement) {
            try {
                const userData = JSON.parse(userDataScript.textContent);
                const remaining = userData.max_allowed - userData.total_collections;

                statsElement.textContent = `${userData.total_collections}/${userData.max_allowed} collections used`;

                if (remaining <= 2) {
                    statsElement.classList.add('text-yellow-400');
                }
                if (remaining <= 0) {
                    statsElement.classList.add('text-red-400');
                }

            } catch (error) {
                console.warn('[CreateCollectionModal] Failed to load user stats:', error);
            }
        }
    }

    /**
     * @Oracode OS1: Analytics event tracking
     * 🎯 Purpose: Track user interactions for system improvement
     */
    trackEvent(eventName, data = {}) {
        // OS1 Pillar 5: Recursive Evolution - Track for improvement
        try {
            // Integration with analytics platform (Google Analytics, etc.)
            if (typeof gtag !== 'undefined') {
                gtag('event', eventName, {
                    event_category: 'Collection Modal',
                    ...data
                });
            }

            // Custom analytics endpoint if available
            if (window.analytics && typeof window.analytics.track === 'function') {
                window.analytics.track(eventName, data);
            }

            // Console logging for development
            if (process.env.NODE_ENV === 'development') {
                console.info(`[Analytics] ${eventName}:`, data);
            }

        } catch (error) {
            console.warn('[CreateCollectionModal] Analytics tracking failed:', error);
        }
    }

    /**
     * @Oracode OS1: Public API for programmatic control
     * 🎯 Purpose: Enable external systems to control modal
     */
    destroy() {
        // OS1 Cleanup: Remove all event listeners and timers
        if (this.redirectTimer) {
            clearTimeout(this.redirectTimer);
        }
        if (this.validationTimeout) {
            clearTimeout(this.validationTimeout);
        }

        // Close modal if open
        if (this.isOpen) {
            this.close();
        }

        console.info('[CreateCollectionModal] Destroyed successfully');
    }
}

// OS1 Global Initialization and Exposure
let createCollectionModalInstance = null;

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    createCollectionModalInstance = new CreateCollectionModal();
});

// OS1 Public API
window.CreateCollectionModal = {
    open: () => createCollectionModalInstance?.open(),
    close: () => createCollectionModalInstance?.close(),
    instance: () => createCollectionModalInstance
};

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = CreateCollectionModal;
}
