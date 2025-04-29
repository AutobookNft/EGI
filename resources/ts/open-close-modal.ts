// /home/fabio/EGI/resources/ts/open-close-modal.ts

/**
 * Interface for modal elements used by ModalManager.
 * @interface ModalElements
 */
interface ModalElements {
    modal: HTMLElement | null;
    openButton: HTMLElement | null;
    returnButton: HTMLElement | null;
    modalContent: HTMLElement | null;
}

/**
 * Manages the opening and closing of the upload modal.
 * Handles authorization, accessibility (ARIA), scroll locking, and form reset.
 * Opens the modal only after authorization and closes it via specific controls.
 *
 * @class ModalManager
 * @oracode.semantically_coherent Clear and predictable modal management.
 * @oracode.testable Authorization and modal operations are deterministic.
 * @oracode.explicitly_intentional Integrates with UUM's upload flow.
 * @gdpr No personal data is stored beyond setting window.uploadType.
 */
class ModalManager {
    private elements: ModalElements;
    private isOpen: boolean;
    private csrfToken: string;

    constructor(modalId: string, openButtonId: string, returnButtonId: string, contentId: string, csrfToken: string) {
        this.elements = {
            modal: document.getElementById(modalId),
            openButton: document.getElementById(openButtonId),
            returnButton: document.getElementById(returnButtonId),
            modalContent: document.getElementById(contentId),
        };
        this.isOpen = false;
        this.csrfToken = csrfToken;

        this.initialize();
    }

    private initialize(): void {
        // Verifica che tutti gli elementi necessari esistano
        if (!this.elements.modal || !this.elements.openButton || !this.elements.returnButton || !this.elements.modalContent) {
            console.warn('One or more modal elements not found. Check IDs:', {
                modal: this.elements.modal,
                openButton: this.elements.openButton,
                returnButton: this.elements.returnButton,
                modalContent: this.elements.modalContent,
            });
            return;
        }

        // Listener per aprire il modale con autorizzazione
        this.elements.openButton.addEventListener('click', async (event: Event) => {
            event.preventDefault();
            await this.handleOpenModal();
        });

        // Listener per il bottone "Return to collection"
        this.elements.returnButton.addEventListener('click', () => this.closeModal());

        // Listener per la fine dell'upload
        document.addEventListener('upload-completed', () => {
            console.log('Upload completed, closing modal');
            this.closeModal();
        });
    }

    /**
     * Handles modal opening with authorization check.
     *
     * @oracode.explicitly_intentional Authorization check before opening.
     */
    private async handleOpenModal(): Promise<void> {
        const uploadType = this.elements.openButton?.dataset.uploadType || 'egi';
        try {
            const response = await fetch('/api/check-upload-authorization', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                },
            });

            const result = await response.json();
            console.log('Authorization response:', result);

            if (result.authorized) {
                console.log('User authorized, opening modal');
                this.openModal(uploadType);
                window.uploadType = uploadType;
            } else {
                console.warn('User not authorized, redirecting to:', result.redirect || '/login');
                window.location.href = result.redirect || '/login';
            }
        } catch (error) {
            console.error('Error checking authorization:', error);
            window.location.href = '/login';
        }
    }

    /**
     * Opens the modal with the specified upload type.
     *
     * @param uploadType - The type of upload (e.g., 'egi', 'epp', 'utility').
     * @oracode.explicitly_intentional Called after authorization check.
     */
    public openModal(uploadType: string): void {
        if (this.elements.modal && this.elements.modalContent) {
            this.elements.modal.classList.remove('hidden');
            this.elements.modal.classList.add('flex');
            this.elements.modalContent.dataset.uploadType = uploadType;
            this.isOpen = true;

            // Imposta ARIA attributes
            this.elements.modal.setAttribute('aria-hidden', 'false');
            this.elements.modalContent.focus();

            // Blocca lo scroll della pagina
            document.body.style.overflow = 'hidden';

            console.log(`Upload modal opened with type: ${uploadType}`);
        }
    }

    /**
     * Closes the modal and resets the upload form.
     *
     * @oracode.semantically_coherent Resets UI state cleanly.
     */
    public closeModal(): void {
        if (this.elements.modal && this.elements.modalContent) {
            this.elements.modal.classList.add('hidden');
            this.elements.modal.classList.remove('flex');
            this.isOpen = false;

            // Ripristina ARIA attributes
            this.elements.modal.setAttribute('aria-hidden', 'true');

            // Ripristina lo scroll della pagina
            document.body.style.overflow = '';

            // Resetta il form di upload
            if (window.fileUploadManager) {
                window.fileUploadManager.resetUploadForm();
            }

            console.log('Upload modal closed');

            // Esegui redirectToCollection se definita
            if (typeof window.redirectToURL === 'function') {
                window.uploadRedirectToUrl();
            }
        }
    }
}

/**
 * Initializes the upload modal manager.
 *
 * @oracode.explicitly_intentional Initializes modal for application-specific use.
 */
export function initializeModal(): void {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    new ModalManager(
        'upload-modal',
        'open-upload-modal',
        'returnToCollection', // ID corretto
        'upload-container',
        csrfToken
    );
}
