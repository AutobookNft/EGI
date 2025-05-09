

/**
 * 📜 Oracode TypeScript Class: ModalManager
 * Manages the opening, closing, and associated logic specifically for the UPLOAD modal (#upload-modal).
 * Handles authorization checks when opened via dedicated buttons (typically for authenticated users)
 * and provides public methods for programmatic control (e.g., after guest wallet connection).
 *
 * @class ModalManager
 * @dependency Fetch API for authorization check.
 * @dependency DOM API for element manipulation.
 * @dependency Assumes presence of global `window.fileUploadManager` for form reset.
 * @dependency Assumes presence of global `window.redirectToURL` (optional).
 */
class ModalManager {
    private elements: ModalElements;
    private isOpen: boolean;
    private csrfToken: string;
    private lastFocusedElement: Element | null = null; // Stores element that had focus before modal opened

    /**
     * Creates an instance of ModalManager.
     * @param modalId ID of the upload modal element.
     * @param openButtonSelector CSS selector for buttons that directly trigger this modal (with auth check).
     * @param returnButtonId ID of the button inside the modal to close it (e.g., "Return").
     * @param contentId ID of the main content container within the modal (for focus).
     * @param csrfToken CSRF token for API requests.
     */
    constructor(modalId: string, openButtonSelector: string, returnButtonId: string, contentId: string, csrfToken: string) {
        this.elements = {
            modal: document.getElementById(modalId),
            openButtons: document.querySelectorAll(openButtonSelector),
            returnButton: document.getElementById(returnButtonId),
            modalContent: document.getElementById(contentId),
        };
        this.isOpen = false;
        this.csrfToken = csrfToken;

        this.initialize();
    }

    /**
     * Initializes event listeners and sets up the global instance.
     * @private
     */
    private initialize(): void {
        // Basic element checks
        if (!this.elements.modal || !this.elements.returnButton || !this.elements.modalContent) {
            console.warn('ModalManager: Critical elements (modal, return button, or content) not found. Manager may not function correctly.');
            return;
        }
        if (this.elements.openButtons.length === 0) {
            console.warn(`ModalManager: No open buttons found with selector: "${this.elements.openButtons}". Modal can only be opened programmatically.`);
        }

        // Listener for opening via dedicated AUTHENTICATED buttons
        this.elements.openButtons.forEach(button => {
            button.addEventListener('click', async (event: Event) => {
                event.preventDefault();
                await this.handleOpenModalWithAuth(button as HTMLElement);
            });
        });

        // Listener for the return/close button inside the modal
        this.elements.returnButton.addEventListener('click', (event: Event) => {
            event.preventDefault(); // Prevent potential form submission if it's a button type submit
            this.closeModal();
        });

        // Listener for custom event indicating upload completion
        document.addEventListener('upload-completed', () => {
            console.log('Upload completed event received by ModalManager, closing upload modal.');
            this.closeModal();
        });

        // Make this instance globally accessible
        window.globalModalManager = this;
        console.log('ModalManager (for #upload-modal) initialized and assigned to window.globalModalManager');
    }

    /**
     * Handles opening the modal when triggered by a dedicated button,
     * performing an authorization check first.
     * @private
     * @param button The button element that was clicked.
     */
    private async handleOpenModalWithAuth(button: HTMLElement): Promise<void> {
        const uploadType = button?.dataset.uploadType || 'egi';
        console.log(`Upload button clicked. Type: ${uploadType}. Checking auth...`);
        try {
            const response = await fetch('/api/check-upload-authorization', { // Ensure this API route exists and works
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                },
            });

            // Check for non-JSON or error responses
             if (!response.ok) {
                 console.error(`Auth check failed with status: ${response.status}`);
                 throw new Error(`Authorization check failed (${response.statusText})`);
             }

             const result = await response.json();
             console.log('Auth check response:', result);

            if (result.authorized) {
                console.log('Auth check passed, opening upload modal.');
                this.openModal(uploadType); // Call the public open method
            } else {
                console.warn('Auth check failed (user not authorized), redirecting.', result.redirect);
                window.location.href = result.redirect || '/login'; // Redirect if not authorized
            }
        } catch (error) {
            console.error('Error during authorization check:', error);
            alert("Could not verify your authorization to upload. Please ensure you are logged in and have the correct permissions.");
            // Optionally redirect to login: window.location.href = '/login';
        }
    }

    /**
     * Opens the upload modal programmatically (e.g., after wallet connect).
     * Skips the internal authorization check.
     * Manages focus, ARIA attributes, and body scroll lock.
     *
     * @public
     * @param uploadType Type of upload ('egi' by default).
     */
    public openModal(uploadType: string = 'egi'): void {
        if (this.isOpen || !this.elements.modal || !this.elements.modalContent) {
            console.warn('Attempted to open upload modal when it was already open or elements were missing.');
            return;
        }
        console.log(`Opening upload modal programmatically. Type: ${uploadType}`);

        // Store focus and close connect modal if it's open
        this.lastFocusedElement = document.activeElement;
        const connectModal = document.getElementById('connect-wallet-modal');
        if (connectModal && !connectModal.classList.contains('hidden')) {
            const closeButton = connectModal.querySelector('#close-connect-wallet-modal') as HTMLElement | null;
            if (closeButton) closeButton.click(); else connectModal.classList.add('hidden'); // Fallback
            console.log('Connect wallet modal closed before opening upload modal.');
        }

        // Open this (upload) modal
        this.elements.modal.classList.remove('hidden');
        this.elements.modal.classList.add('flex'); // Or appropriate display class
        this.elements.modalContent.dataset.uploadType = uploadType;
        this.isOpen = true;
        window.uploadType = uploadType;

        // Accessibility & UI
        this.elements.modal.setAttribute('aria-hidden', 'false');
        this.elements.modalContent.setAttribute('tabindex', '-1'); // Make content focusable
        this.elements.modalContent.focus();
        document.body.style.overflow = 'hidden'; // Lock body scroll
    }

    /**
     * Closes the upload modal.
     * Manages focus restoration, ARIA attributes, body scroll unlock, and form reset.
     *
     * @public
     */
    public closeModal(): void {
        if (!this.isOpen || !this.elements.modal) {
            console.log('Attempted to close upload modal when it was not open or missing.');
            return;
        }

        this.elements.modal.classList.add('hidden');
        this.elements.modal.classList.remove('flex');
        this.isOpen = false;

        // Accessibility & UI
        this.elements.modal.setAttribute('aria-hidden', 'true');

        // Unlock body scroll ONLY if the connect modal is also hidden
        const connectModal = document.getElementById('connect-wallet-modal');
        if (!connectModal || connectModal.classList.contains('hidden')) {
            document.body.style.overflow = '';
        }

        // Reset the upload form via the global manager reference
        if (window.fileUploadManager && typeof window.fileUploadManager.resetUploadForm === 'function') {
            window.fileUploadManager.resetUploadForm();
            console.log('Upload form reset via fileUploadManager.');
        } else {
            console.warn('window.fileUploadManager or resetUploadForm not found.');
        }

        // Restore focus to the element that opened the modal
        if (this.lastFocusedElement instanceof HTMLElement) {
            console.log('Restoring focus to:', this.lastFocusedElement);
            this.lastFocusedElement.focus();
        }
        this.lastFocusedElement = null; // Clear stored element

        console.log('Upload modal closed');

        // Optional Redirect (handle with care)
        if (typeof window.redirectToURL === 'function') {
             console.warn('window.redirectToURL is defined and will be called after closing upload modal.');
            window.redirectToURL();
        }
    }
}



/**
 * Initializes the *upload* modal manager, making it globally available.
 * Should be called once when the DOM is ready.
 */
export function initializeModal(): void {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    // Selector for buttons in the AUTHENTICATED layout that open the upload modal directly
    const openButtonSelector = '#open-connect-modal-or-create-egi, #open-connect-modal-or-create-egi-mobile'; // Use specific IDs/classes for auth layout

    // Create the instance (it self-registers to window.globalModalManager)
    new ModalManager(
        'upload-modal',
        openButtonSelector,
        'returnToCollection', // ID of the "Return" button inside #upload-modal
        'upload-container',   // ID of the content area inside #upload-modal
        csrfToken
    );
}
