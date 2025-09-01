/**
 * Integrated Traits Viewer & Image Manager
 * Handles trait display, editing, removal, and image management in a single module
 *
 * @package FlorenceEGI\Traits\Assets
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 2.0.0 (Integrated System)
 * @date 2025-09-01
 */

// =============================================================================
// TRANSLATIONS & GLOBALS
// =============================================================================

// These will be populated by the Blade template
window.TraitsTranslations = window.TraitsTranslations || {};
window.traitTranslations = window.traitTranslations || {};

// =============================================================================
// TOAST MANAGER
// =============================================================================

window.ToastManager = {
    container: null,

    init() {
        this.container = document.getElementById('toast-container-viewer');
        if (!this.container) {
            this.container = document.createElement('div');
            this.container.className = 'toast-container';
            this.container.id = 'toast-container-viewer';
            document.body.appendChild(this.container);
        }
    },

    show(message, type = 'info', title = null, duration = 4000) {
        this.init();

        const toast = document.createElement('div');
        toast.className = `toast ${type}`;

        const icons = {
            success: '✅',
            error: '❌',
            warning: '⚠️',
            info: 'ℹ️'
        };

        const content = `
            <div class="toast-content">
                <span class="toast-icon">${icons[type] || icons.info}</span>
                <div class="toast-text">
                    ${title ? `<div class="toast-title">${title}</div>` : ''}
                    <div class="toast-message">${message}</div>
                </div>
                <button class="toast-close" onclick="this.parentElement.parentElement.remove()">×</button>
            </div>
        `;

        toast.innerHTML = content;
        this.container.appendChild(toast);

        // Auto-remove
        setTimeout(() => {
            if (toast.parentNode) {
                toast.remove();
            }
        }, duration);

        return toast;
    },

    success(message, title = null) {
        return this.show(message, 'success', title);
    },

    error(message, title = null) {
        return this.show(message, 'error', title);
    },

    warning(message, title = null) {
        return this.show(message, 'warning', title);
    },

    info(message, title = null) {
        return this.show(message, 'info', title);
    }
};

// =============================================================================
// TRAITS VIEWER (Original Logic)
// =============================================================================

const TraitsViewer = {
    state: {
        egiId: null,
        canEdit: false,
        container: null,
        categories: [],
        availableTypes: [],
        modalData: {
            category_id: null,
            trait_type_id: null,
            value: null,
            currentType: null
        }
    },

    init(egiId) {
        console.log('TraitsViewer: Initializing for EGI', egiId);

        this.state.egiId = egiId;
        this.state.container = document.querySelector(`#traits-viewer-${egiId}`);

        if (!this.state.container) {
            console.error('TraitsViewer: Container not found for EGI', egiId);
            return;
        }

        this.state.canEdit = this.state.container.getAttribute('data-can-edit') === 'true';
        console.log('TraitsViewer: Edit mode:', this.state.canEdit);

        this.setupEventListeners();
        this.setupAddTraitButton();

        console.log('TraitsViewer: Initialization complete. State:', this.state);
    },

    setupEventListeners() {
        console.log('TraitsViewer: Setting up event listeners');

        const removeButtons = this.state.container.querySelectorAll('.trait-remove');
        console.log('TraitsViewer: Found remove buttons:', removeButtons.length);

        removeButtons.forEach((button, index) => {
            console.log(`TraitsViewer: Setting up listener for button ${index}`);

            button.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();

                const traitCard = button.closest('[data-trait-id]');
                const traitId = traitCard ? traitCard.getAttribute('data-trait-id') : null;

                console.log('Remove button clicked via event listener for trait:', traitId);

                if (traitId) {
                    this.removeTrait(parseInt(traitId));
                } else {
                    console.error('Could not find trait ID');
                }
            });
        });
    },

    setupAddTraitButton() {
        const addButton = this.state.container.querySelector('.add-trait-btn');
        if (addButton) {
            addButton.addEventListener('click', () => {
                this.openModal();
            });
        }
    },

    async removeTrait(traitId) {
        console.log('TraitsViewer.removeTrait called with traitId:', traitId);

        if (!this.state.canEdit) {
            console.warn('TraitsViewer: Remove trait denied - readonly mode');
            ToastManager.warning(window.TraitsTranslations.creator_only_modify);
            return;
        }

        console.log('Showing SweetAlert2 confirmation dialog...');

        // Use SweetAlert2 for confirmation
        const result = await Swal.fire({
            title: window.TraitsTranslations.confirm_delete_title,
            text: window.TraitsTranslations.confirm_delete_text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: window.TraitsTranslations.confirm_delete_button,
            cancelButtonText: window.TraitsTranslations.cancel_button,
            reverseButtons: true
        });

        if (!result.isConfirmed) {
            console.log('User cancelled removal');
            return;
        }

        console.log('Sending DELETE request to:', `/egis/${this.state.egiId}/traits/${traitId}`);

        try {
            const response = await fetch(`/egis/${this.state.egiId}/traits/${traitId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            console.log('Response status:', response.status);
            const data = await response.json();
            console.log('Response data:', data);

            if (data.success) {
                ToastManager.success(window.TraitsTranslations.remove_success, '🎯 Trait Rimosso');

                const traitCard = document.querySelector(`[data-trait-id="${traitId}"]`);
                console.log('Found trait card:', traitCard);

                if (traitCard) {
                    traitCard.style.transition = 'all 0.3s ease';
                    traitCard.style.opacity = '0';
                    traitCard.style.transform = 'scale(0.8)';

                    setTimeout(() => {
                        traitCard.remove();
                        console.log('Trait card removed from DOM');
                    }, 300);
                }

                const counter = document.querySelector('.traits-count');
                if (counter) {
                    const currentCount = parseInt(counter.textContent) || 0;
                    counter.textContent = Math.max(0, currentCount - 1);
                    console.log('Updated counter to:', currentCount - 1);
                }

                console.log('TraitsViewer: Trait removed successfully');
            } else {
                console.error('Server returned error:', data.message);
                ToastManager.error(window.TraitsTranslations.remove_error + ': ' + (data.message || window.TraitsTranslations.unknown_error), '❌ Errore');
            }
        } catch (error) {
            console.error('TraitsViewer: Error removing trait:', error);
            ToastManager.error(window.TraitsTranslations.network_error, '🌐 Errore di Rete');
        }
    },

    async openModal() {
        // Opens the traits editor modal for adding new traits
        console.log('TraitsViewer: Opening modal for adding traits');

        if (!this.state.canEdit) {
            console.warn('TraitsViewer: Modal access denied - readonly mode');
            ToastManager.warning(window.TraitsTranslations.creator_only_modify);
            return;
        }

        const modal = document.querySelector('#trait-modal-viewer');
        if (modal) {
            console.log('TraitsViewer: Found modal element:', modal);

            // Move modal to body to avoid parent positioning issues - CRUCIAL!
            if (modal.parentNode !== document.body) {
                console.log('TraitsViewer: Moving modal to body');
                document.body.appendChild(modal);
            }

            // Show the modal with proper styling
            modal.classList.remove('hidden');
            modal.style.display = 'flex';
            modal.style.position = 'fixed';
            modal.style.top = '0';
            modal.style.left = '0';
            modal.style.width = '100vw';
            modal.style.height = '100vh';
            modal.style.zIndex = '99999';  // Changed to 99999 like original
            modal.style.backgroundColor = 'rgba(0, 0, 0, 0.7)';
            modal.style.alignItems = 'center';
            modal.style.justifyContent = 'center';
            modal.style.padding = '1rem';
            modal.style.visibility = 'visible';
            modal.style.opacity = '1';

            // Ensure modal content is visible
            const modalContent = modal.querySelector('.modal-content');
            if (modalContent) {
                console.log('TraitsViewer: Found modal content:', modalContent);
                modalContent.style.cssText = `
                    display: block !important;
                    visibility: visible !important;
                    opacity: 1 !important;
                    transform: scale(1) !important;
                    z-index: 10001 !important;
                    background: #ffffff !important;
                    border-radius: 0.75rem !important;
                    max-width: 500px !important;
                    width: 90% !important;
                    max-height: 80vh !important;
                    overflow-y: auto !important;
                    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4) !important;
                    position: relative !important;
                    margin: auto !important;
                    pointer-events: auto !important;
                `;

                // Also ensure all child elements are visible
                const modalHeader = modalContent.querySelector('.modal-header');
                const modalBody = modalContent.querySelector('.modal-body');
                const modalFooter = modalContent.querySelector('.modal-footer');

                if (modalHeader) modalHeader.style.cssText = 'display: block !important; visibility: visible !important;';
                if (modalBody) modalBody.style.cssText = 'display: block !important; visibility: visible !important;';
                if (modalFooter) modalFooter.style.cssText = 'display: block !important; visibility: visible !important;';

            } else {
                console.error('TraitsViewer: Modal content (.modal-content) not found!');
            }

            // Prevent body scroll when modal is open
            document.body.style.overflow = 'hidden';

            console.log('TraitsViewer: Modal styling applied');
            console.log('TraitsViewer: Modal computed style:', window.getComputedStyle(modal));

            // Reset modal state
            this.resetModal();

            // Load categories if not loaded
            if (this.state.categories.length === 0) {
                await this.loadCategories();
            }

            this.renderModalCategories();
        } else {
            console.error('TraitsViewer: Modal #trait-modal-viewer not found');
            ToastManager.error(window.TraitsTranslations.modal_open_error || 'Modal not found');
        }
    },

    closeModal() {
        const modal = document.getElementById('trait-modal-viewer');
        if (modal) {
            modal.style.display = 'none';
            modal.classList.add('hidden');
            // Restore body scroll
            document.body.style.overflow = '';
            this.resetModal();
        }
    },

    resetModal() {
        this.state.modalData = {
            category_id: null,
            trait_type_id: null,
            value: null,
            currentType: null
        };

        const typeGroup = document.getElementById('type-selector-group-viewer');
        const valueGroup = document.getElementById('value-selector-group-viewer');
        const preview = document.getElementById('trait-preview-viewer');
        const confirmBtn = document.getElementById('confirm-trait-btn-viewer');

        if (typeGroup) typeGroup.style.display = 'none';
        if (valueGroup) valueGroup.style.display = 'none';
        if (preview) preview.style.display = 'none';
        if (confirmBtn) confirmBtn.disabled = true;
    },

    async loadCategories() {
        try {
            const response = await fetch('/traits/categories');
            const data = await response.json();

            if (data.success && data.categories) {
                this.state.categories = data.categories;
                console.log('TraitsViewer: Loaded categories:', this.state.categories.length);
            }
        } catch (error) {
            console.error('Error loading categories:', error);
        }
    },

    renderModalCategories() {
        const selector = document.getElementById('category-selector-viewer');
        if (!selector) return;

        selector.innerHTML = this.state.categories.map(cat => `
            <button type="button"
                    class="category-option"
                    onclick="TraitsViewer.selectCategory(${cat.id})"
                    data-category-id="${cat.id}">
                <span class="category-icon">${cat.icon}</span>
                <span class="category-name">${cat.name}</span>
            </button>
        `).join('');
    },

    async selectCategory(categoryId) {
        this.state.modalData.category_id = categoryId;

        // Highlight selected category
        document.querySelectorAll('.category-option').forEach(btn => {
            btn.classList.toggle('selected', btn.dataset.categoryId == categoryId);
        });

        try {
            const response = await fetch(`/traits/categories/${categoryId}/types`);
            const data = await response.json();

            if (data.success) {
                this.state.availableTypes = data.types;
                this.renderTypeSelector();
                document.getElementById('type-selector-group-viewer').style.display = 'block';
            }
        } catch (error) {
            console.error('Error loading trait types:', error);
        }
    },

    renderTypeSelector() {
        const select = document.getElementById('trait-type-select-viewer');
        if (!select) return;

        select.innerHTML = '<option value="">Scegli tipo</option>' +
            this.state.availableTypes.map(type =>
                `<option value="${type.id}">${type.name}</option>`
            ).join('');
    },

    onTypeSelected() {
        const select = document.getElementById('trait-type-select-viewer');
        const typeId = select.value;

        if (!typeId) return;

        const type = this.state.availableTypes.find(t => t.id == typeId);
        this.state.modalData.trait_type_id = typeId;
        this.state.modalData.currentType = type;

        this.renderValueInput(type);
        document.getElementById('value-selector-group-viewer').style.display = 'block';
    },

    renderValueInput(type) {
        const container = document.getElementById('value-input-container-viewer');
        if (!container) return;

        let inputHtml = '';
        let allowedValues = null;

        if (type.allowed_values) {
            try {
                allowedValues = typeof type.allowed_values === 'string'
                    ? JSON.parse(type.allowed_values)
                    : type.allowed_values;
            } catch (e) {
                console.error('Error parsing allowed values:', e);
            }
        }

        if (allowedValues && allowedValues.length > 0) {
            inputHtml = `
                <select class="form-select" id="trait-value-input-viewer" onchange="TraitsViewer.onValueChanged()">
                    <option value="">Scegli valore</option>
                    ${allowedValues.map(val => `<option value="${val}">${val}</option>`).join('')}
                </select>
            `;
        } else {
            inputHtml = `
                <input type="text"
                       class="form-input"
                       id="trait-value-input-viewer"
                       placeholder="Inserisci valore"
                       oninput="TraitsViewer.onValueChanged()">
            `;
        }

        container.innerHTML = inputHtml;
    },

    onValueChanged() {
        const input = document.getElementById('trait-value-input-viewer');
        const value = input.value.trim();

        this.state.modalData.value = value;

        if (value) {
            this.updatePreview();
            document.getElementById('confirm-trait-btn-viewer').disabled = false;
        } else {
            document.getElementById('trait-preview-viewer').style.display = 'none';
            document.getElementById('confirm-trait-btn-viewer').disabled = true;
        }
    },

    updatePreview() {
        const preview = document.getElementById('trait-preview-viewer');
        const type = this.state.modalData.currentType;

        if (!preview || !type) return;

        preview.querySelector('.preview-type').textContent = type.name;
        preview.querySelector('.preview-value').textContent = this.state.modalData.value;
        preview.querySelector('.preview-unit').textContent = type.unit || '';

        preview.style.display = 'block';
    },

    async addTrait() {
        if (!this.state.modalData.value || !this.state.modalData.trait_type_id) {
            return;
        }

        try {
            const response = await fetch(`/egis/${this.state.egiId}/traits/add`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    traits: [{
                        category_id: this.state.modalData.category_id,
                        trait_type_id: this.state.modalData.trait_type_id,
                        value: this.state.modalData.value,
                        display_value: this.state.modalData.value
                    }]
                })
            });

            const data = await response.json();

            if (data.success) {
                ToastManager.success(window.TraitsTranslations.add_success, '🎯 Nuovo Trait');
                this.closeModal();

                // Reload page to show new trait
                setTimeout(() => location.reload(), 1500);
            } else {
                ToastManager.error(window.TraitsTranslations.add_trait_error + ': ' + (data.message || window.TraitsTranslations.unknown_error), '❌ Errore');
            }
        } catch (error) {
            console.error('Error adding trait:', error);
            ToastManager.error(window.TraitsTranslations.network_error_general, '🌐 Errore di Rete');
        }
    }
};

// =============================================================================
// TRAIT IMAGE MANAGER (Enhanced with integrated functionality)
// =============================================================================

class TraitImageManager {
    constructor() {
        this.init();
        this.translations = this.loadTranslations();
        this.uploadInProgress = new Set();
    }

    init() {
        // Setup trait card click listeners for image modals
        this.setupTraitCardListeners();

        // Image modal functionality
        this.setupImageUpload();
        this.setupImageDeletion();
        this.setupFilePreview();
        this.setupModalCloseEvents();
        this.setupDragAndDrop();
    }

    loadTranslations() {
        return window.traitTranslations || {
            upload_success: 'Image uploaded successfully',
            upload_error: 'Error uploading image',
            delete_success: 'Image deleted successfully',
            delete_error: 'Error deleting image',
            confirm_delete: 'Are you sure you want to delete this image?',
            uploading: 'Uploading...',
            file_too_large: 'File is too large',
            invalid_file_type: 'Invalid file type'
        };
    }

    setupTraitCardListeners() {
        console.log('TraitImageManager: Setting up trait card listeners...');

        // Use event delegation on document to catch dynamically added cards
        document.addEventListener('click', (e) => {
            const traitCard = e.target.closest('[data-trait-id]:not(.trait-remove):not(.trait-remove *)');

            if (traitCard && !e.target.closest('.trait-remove')) {
                console.log('TraitImageManager: Trait card clicked:', traitCard.dataset.traitId);
                e.preventDefault();
                e.stopPropagation();
                this.openImageModal(traitCard.dataset.traitId);
            }
        });

        // Also try direct setup for existing cards
        this.setupExistingCards();
    }

    setupExistingCards() {
        // Wait for DOM and try to setup existing cards
        const setupCards = () => {
            const traitCards = document.querySelectorAll('[data-trait-id]');
            console.log('TraitImageManager: Found', traitCards.length, 'trait cards');

            traitCards.forEach(card => {
                // Make sure card is clickable
                card.style.cursor = 'pointer';
                card.style.pointerEvents = 'auto';

                console.log('TraitImageManager: Card setup for trait:', card.dataset.traitId);
            });
        };

        // Try immediately and with delays
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', setupCards);
        } else {
            setupCards();
        }

        // Also setup with short delays to catch dynamically added content
        setTimeout(setupCards, 100);
        setTimeout(setupCards, 500);
        setTimeout(setupCards, 1000);
    }

    openImageModal(traitId) {
        console.log('TraitImageManager: Opening image modal for trait:', traitId);

        const modal = document.querySelector(`#trait-modal-${traitId}`);
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            modal.style.display = 'flex';
            console.log('TraitImageManager: Modal opened successfully');
        } else {
            console.error('TraitImageManager: Modal not found for trait:', traitId);
            ToastManager.error('Modal not found for this trait');
        }
    }

    closeImageModal(traitId) {
        const modal = document.querySelector(`#trait-modal-${traitId}`);
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            modal.style.display = 'none';
        }
    }

    setupModalCloseEvents() {
        // Handle modal close buttons with correct selectors
        document.addEventListener('click', (e) => {
            if (e.target.matches('.trait-modal-close')) {
                const modal = e.target.closest('.trait-modal');
                if (modal) {
                    modal.style.display = 'none';
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
            }
        });

        // Handle backdrop clicks
        document.addEventListener('click', (e) => {
            if (e.target.matches('.trait-modal')) {
                e.target.style.display = 'none';
                e.target.classList.add('hidden');
                e.target.classList.remove('flex');
            }
        });
    }

    setupDragAndDrop() {
        // Setup drag and drop on all trait upload areas
        document.addEventListener('dragover', (e) => {
            const uploadArea = e.target.closest('.trait-upload-area');
            if (uploadArea) {
                e.preventDefault();
                e.stopPropagation();
                uploadArea.classList.add('border-blue-500', 'bg-blue-50');
                uploadArea.style.borderWidth = '3px';
            }
        });

        document.addEventListener('dragenter', (e) => {
            const uploadArea = e.target.closest('.trait-upload-area');
            if (uploadArea) {
                e.preventDefault();
                e.stopPropagation();
            }
        });

        document.addEventListener('dragleave', (e) => {
            const uploadArea = e.target.closest('.trait-upload-area');
            if (uploadArea) {
                // Only remove styles if we're really leaving the area
                if (!uploadArea.contains(e.relatedTarget)) {
                    uploadArea.classList.remove('border-blue-500', 'bg-blue-50');
                    uploadArea.style.borderWidth = '2px';
                }
            }
        });

        document.addEventListener('drop', (e) => {
            const uploadArea = e.target.closest('.trait-upload-area');
            if (uploadArea) {
                e.preventDefault();
                e.stopPropagation();
                uploadArea.classList.remove('border-blue-500', 'bg-blue-50');
                uploadArea.style.borderWidth = '2px';

                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    const fileInput = uploadArea.querySelector('input[name="trait_image"]');
                    if (fileInput) {
                        // Create a new FileList
                        const dt = new DataTransfer();
                        dt.items.add(files[0]);
                        fileInput.files = dt.files;

                        // Trigger change event
                        const changeEvent = new Event('change', { bubbles: true });
                        fileInput.dispatchEvent(changeEvent);

                        ToastManager.info('File selezionato tramite drag & drop!');
                    }
                }
            }
        });
    }

    setupImageUpload() {
        document.addEventListener('change', (e) => {
            if (e.target.matches('input[name="trait_image"]')) {
                this.handleImageUpload(e);
            }
        });

        // Setup form submissions
        document.addEventListener('submit', (e) => {
            if (e.target.matches('form[id^="trait-image-form-"]')) {
                e.preventDefault();
                this.handleFormUpload(e);
            }
        });

        // DISATTIVATO: Setup clicks on labels - ora usiamo onclick diretto nell'HTML
        /*
        document.addEventListener('click', (e) => {
            if (e.target.matches('label[for^="trait-image-input-"]') ||
                e.target.closest('label[for^="trait-image-input-"]')) {
                e.preventDefault();
                e.stopPropagation();
                const label = e.target.matches('label[for^="trait-image-input-"]') ?
                             e.target : e.target.closest('label[for^="trait-image-input-"]');
                const inputId = label.getAttribute('for');
                const input = document.getElementById(inputId);
                if (input) {
                    console.log('Clicking file input:', inputId);
                    input.click();
                }
            }
        });
        */

        // DISATTIVATO: Setup clicks on upload area - ora usiamo onclick diretto nell'HTML
        /*
        document.addEventListener('click', (e) => {
            const uploadArea = e.target.closest('.trait-upload-area');
            if (uploadArea && !e.target.matches('input, button, textarea')) {
                const fileInput = uploadArea.querySelector('input[name="trait_image"]');
                if (fileInput) {
                    console.log('Clicking file input from upload area');
                    fileInput.click();
                }
            }
        });
        */
    }

    setupImageDeletion() {
        document.addEventListener('click', (e) => {
            if (e.target.matches('button[id^="trait-delete-image-btn-"]')) {
                e.preventDefault();
                this.handleImageDeletion(e);
            }
        });
    }

    setupFilePreview() {
        document.addEventListener('change', (e) => {
            if (e.target.matches('input[name="trait_image"]')) {
                this.previewFile(e.target);
            }
        });
    }

    async handleImageUpload(event) {
        const fileInput = event.target;
        const file = fileInput.files[0];
        const form = fileInput.closest('form');
        const traitId = form.querySelector('input[name="trait_id"]').value;

        if (!file) return;

        // Validate file
        if (!this.validateFile(file)) {
            fileInput.value = '';
            return;
        }

        // Prevent duplicate uploads
        if (this.uploadInProgress.has(traitId)) {
            ToastManager.warning('Upload already in progress for this trait');
            return;
        }

        this.uploadInProgress.add(traitId);

        const formData = new FormData();
        formData.append('trait_image', file);  // CORRETTO: usa 'trait_image' come nel form HTML
        formData.append('trait_id', traitId);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

        try {
            ToastManager.info(this.translations.uploading, 'Upload');

            const response = await fetch('/traits/image/upload', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (data.success) {
                ToastManager.success(this.translations.upload_success, 'Success');
                this.updateImageDisplay(traitId, data.image_url, data.thumbnail_url);
            } else {
                ToastManager.error(data.message || this.translations.upload_error, 'Error');
            }
        } catch (error) {
            console.error('Upload error:', error);
            ToastManager.error(this.translations.upload_error, 'Error');
        } finally {
            this.uploadInProgress.delete(traitId);
            fileInput.value = '';
        }
    }

    async handleFormUpload(event) {
        event.preventDefault();
        const form = event.target;
        const traitId = form.querySelector('input[name="trait_id"]').value;
        const fileInput = form.querySelector('input[name="trait_image"]');
        const file = fileInput.files[0];

        if (!file) {
            ToastManager.warning('Seleziona un file prima di caricare');
            return;
        }

        // Validate file
        if (!this.validateFile(file)) {
            fileInput.value = '';
            return;
        }

        // Prevent duplicate uploads
        if (this.uploadInProgress.has(traitId)) {
            ToastManager.warning('Upload già in corso per questo trait');
            return;
        }

        this.uploadInProgress.add(traitId);

        const formData = new FormData(form);

        try {
            ToastManager.info('Caricamento in corso...', 'Upload');

            const response = await fetch('/traits/image/upload', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (data.success) {
                ToastManager.success('Immagine caricata con successo!', 'Successo');
                this.updateImageDisplay(traitId, data.image_url, data.thumbnail_url);
                // Reset form
                form.reset();
            } else {
                ToastManager.error(data.message || 'Errore durante il caricamento', 'Errore');
            }
        } catch (error) {
            console.error('Upload error:', error);
            ToastManager.error('Errore durante il caricamento', 'Errore');
        } finally {
            this.uploadInProgress.delete(traitId);
        }
    }

    async handleImageDeletion(event) {
        const button = event.target;
        const traitId = button.dataset.traitId;

        if (!confirm(this.translations.confirm_delete)) {
            return;
        }

        try {
            const response = await fetch(`/traits/image/delete/${traitId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                ToastManager.success(this.translations.delete_success, 'Success');
                this.clearImageDisplay(traitId);
            } else {
                ToastManager.error(data.message || this.translations.delete_error, 'Error');
            }
        } catch (error) {
            console.error('Delete error:', error);
            ToastManager.error(this.translations.delete_error, 'Error');
        }
    }

    validateFile(file) {
        const maxSize = 5 * 1024 * 1024; // 5MB
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        if (file.size > maxSize) {
            ToastManager.error(this.translations.file_too_large, 'File Error');
            return false;
        }

        if (!allowedTypes.includes(file.type)) {
            ToastManager.error(this.translations.invalid_file_type, 'File Error');
            return false;
        }

        return true;
    }

    previewFile(input) {
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                // Trova il modal corretto usando l'ID del trait
                const form = input.closest('form[id^="trait-image-form-"]');
                if (form) {
                    const traitId = form.querySelector('input[name="trait_id"]').value;
                    const modal = document.querySelector(`#trait-modal-${traitId}`);
                    if (modal) {
                        const preview = modal.querySelector('#trait-image-preview-' + traitId + ' img');
                        if (preview) {
                            preview.src = e.target.result;
                            preview.classList.remove('hidden');
                        }
                    }
                }
            };
            reader.readAsDataURL(file);
        }
    }

    updateImageDisplay(traitId, imageUrl, thumbnailUrl) {
        console.log('Updating image display for trait:', traitId, 'with URL:', imageUrl);

        const modal = document.querySelector(`#trait-modal-${traitId}`);
        if (modal) {
            const previewContainer = modal.querySelector(`#trait-image-preview-${traitId}`);
            const deleteBtn = modal.querySelector(`#trait-delete-image-btn-${traitId}`);

            if (previewContainer) {
                console.log('Preview container found, updating...');

                // Rimuovi il contenuto "no image"
                const noImageDiv = previewContainer.querySelector('.py-8.text-gray-500');
                if (noImageDiv) {
                    noImageDiv.remove();
                }

                // Cerca l'immagine esistente o creane una nuova
                let img = previewContainer.querySelector('img');
                if (!img) {
                    console.log('Creating new img element');
                    img = document.createElement('img');
                    img.className = 'object-contain h-auto max-w-full mx-auto rounded-lg max-h-64';
                    previewContainer.appendChild(img);
                }

                img.src = imageUrl;
                img.alt = 'Trait image';
                console.log('Image src updated to:', imageUrl);
            } else {
                console.error('Preview container not found for trait:', traitId);
            }

            if (deleteBtn) {
                deleteBtn.style.display = 'block';
                console.log('Delete button shown');
            }
        } else {
            console.error('Modal not found for trait:', traitId);
        }
    }

    clearImageDisplay(traitId) {
        const modal = document.querySelector(`#trait-modal-${traitId}`);
        if (modal) {
            const preview = modal.querySelector('.image-preview img');
            const deleteBtn = modal.querySelector('.delete-trait-image');

            if (preview) {
                preview.src = '';
                preview.classList.add('hidden');
            }

            if (deleteBtn) {
                deleteBtn.classList.add('hidden');
            }
        }
    }
}

// =============================================================================
// INITIALIZATION
// =============================================================================

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('Integrated Traits System: DOM loaded, initializing...');

    // Initialize TraitsViewer
    const container = document.querySelector('[id^="traits-viewer-"]');
    if (container) {
        const egiId = container.getAttribute('data-egi-id');
        if (egiId) {
            TraitsViewer.init(egiId);
        }
    }

    // Initialize TraitImageManager
// Initialize managers only once (Singleton pattern)
if (typeof window.TraitImageManagerInstance === 'undefined') {
    window.TraitImageManagerInstance = new TraitImageManager();
    console.log('TraitImageManager initialized once');
}

    console.log('Integrated Traits System: Initialization complete');
});

// Export for global access
window.TraitsViewer = TraitsViewer;
window.TraitImageManager = TraitImageManager;
