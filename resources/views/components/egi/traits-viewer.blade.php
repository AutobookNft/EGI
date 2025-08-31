{{-- resources/views/components/egi/traits-viewer.blade.php --}}
{{--
    EGI Traits Viewer Component - with EDIT MODE support
    Visualizzazione dei traits esistenti renderizzati con PHP
    + Edit mode per owner dell'EGI (add/remove immediate)
--}}
@props([
    'egi' => null,
    'canManage' => false
])

@php
use App\Helpers\FegiAuth;
// Controllo autorizzazione: solo il proprietario dell'EGI può editare E solo se non è pubblicato
$canEdit = $egi && $canManage && FegiAuth::check() && FegiAuth::id() === $egi->user_id && !$egi->is_published;
@endphp

{{-- Include CSS con Vite --}}
@vite(['resources/css/traits-manager.css'])

<div class="egi-traits-viewer"
     id="traits-viewer-{{ $egi ? $egi->id : 'new' }}"
     data-egi-id="{{ $egi ? $egi->id : '' }}"
     data-can-edit="{{ $canEdit ? 'true' : 'false' }}"
     style="position: relative !important; order: -1 !important; margin-top: 0 !important; margin-bottom: 2rem !important;">

    {{-- Header con counter --}}
    <div class="traits-header">
        <h3 class="traits-title">
            <span class="traits-icon">🎯</span>
            {{ __('Tratti e Attributi') }}
        </h3>
        <div class="traits-meta">
            <span class="trait-counter">
                <span class="traits-count">{{ $egi && $egi->traits ? $egi->traits->count() : 0 }}</span>/30
            </span>
        </div>
    </div>

    @if($canEdit)
        {{-- Add Trait Button --}}
        <div class="traits-editor-controls" style="margin-bottom: 1rem;">
            <button type="button"
                    class="add-trait-btn"
                    onclick="TraitsViewer.openModal()"
                    style="background: transparent !important;
                           border: 2px dashed #d4af37 !important;
                           color: #d4af37 !important;
                           padding: 0.75rem 1.5rem !important;
                           border-radius: 0.5rem !important;
                           font-weight: 600 !important;
                           cursor: pointer !important;
                           width: 100% !important;
                           margin-bottom: 1rem !important;
                           font-size: 1rem !important;
                           transition: all 0.2s ease !important;"
                    onmouseover="this.style.backgroundColor='rgba(212, 175, 55, 0.1)'"
                    onmouseout="this.style.backgroundColor='transparent'">
                <svg class="button-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 1.25rem; height: 1.25rem; margin-right: 0.5rem;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                {{ __('traits.add_trait') }}
            </button>
        </div>
    @endif

    {{-- Traits Grid (readonly) renderizzato con PHP --}}
    <div class="traits-list readonly">
        <div class="traits-grid" id="traits-grid-viewer">
            @if($egi && $egi->traits && $egi->traits->count() > 0)
                @foreach($egi->traits as $trait)
                    @php
                        // Carica colore e icona dal database
                        $category = $trait->category;
                        $categoryColor = $category ? $category->color : '#6B6B6B';
                        $categoryIcon = $category ? $category->icon : '🏷️';
                    @endphp

                    <div class="trait-card readonly" data-category="{{ $trait->category_id }}" data-trait-id="{{ $trait->id }}" style="position: relative;">
                        @if($canEdit)
                            <button type="button"
                                    class="trait-remove trait-action-button"
                                    onclick="console.log('Remove button clicked for trait:', {{ $trait->id }}); event.stopPropagation(); event.preventDefault(); if(window.TraitsViewer) { TraitsViewer.removeTrait({{ $trait->id }}); } else { console.error('TraitsViewer not found!'); } return false;"
                                    title="Rimuovi trait"
                                    style="position: absolute;
                                           right: 0.25rem;
                                           top: 0.25rem;
                                           background: rgba(220, 53, 69, 0.9) !important;
                                           color: white !important;
                                           border: none !important;
                                           border-radius: 50% !important;
                                           width: 1.25rem !important;
                                           height: 1.25rem !important;
                                           font-size: 0.875rem !important;
                                           line-height: 1 !important;
                                           cursor: pointer !important;
                                           display: flex !important;
                                           align-items: center !important;
                                           justify-content: center !important;
                                           z-index: 9999 !important;
                                           pointer-events: auto !important;
                                           transition: all 0.2s ease !important;"
                                    onmouseover="this.style.backgroundColor='rgba(220, 53, 69, 1)'; this.style.transform='scale(1.1)';"
                                    onmouseout="this.style.backgroundColor='rgba(220, 53, 69, 0.9)'; this.style.transform='scale(1)';">
                                ×
                            </button>
                        @endif
                        <div class="trait-header readonly">
                            <span class="trait-category-badge" style="background-color: {{ $categoryColor }}">
                                {{ $categoryIcon }}
                            </span>
                        </div>
                        <div class="trait-content">
                            <div class="trait-type">{{ $trait->traitType ? $trait->traitType->name : 'Unknown' }}</div>
                            <div class="trait-value">
                                <span>{{ $trait->display_value ?? $trait->value }}</span>
                                @if($trait->traitType && $trait->traitType->unit)
                                    <span class="trait-unit">{{ $trait->traitType->unit }}</span>
                                @endif
                            </div>

                            {{-- Barra di rarità --}}
                            @if(isset($trait->rarity_percentage) && $trait->rarity_percentage)
                                @php
                                    // Determina la classe di rarità in base alla percentuale
                                    if ($trait->rarity_percentage >= 70) {
                                        $rarityClass = 'common';
                                    } elseif ($trait->rarity_percentage >= 40) {
                                        $rarityClass = 'uncommon';
                                    } elseif ($trait->rarity_percentage >= 20) {
                                        $rarityClass = 'rare';
                                    } elseif ($trait->rarity_percentage >= 10) {
                                        $rarityClass = 'epic';
                                    } elseif ($trait->rarity_percentage >= 5) {
                                        $rarityClass = 'legendary';
                                    } else {
                                        $rarityClass = 'mythic';
                                    }

                                    // Formula semplice e diretta: più è raro, più la barra è lunga
                                    // Invertiamo direttamente la percentuale per creare differenze evidenti
                                    if ($trait->rarity_percentage <= 5) {
                                        $barWidth = 90; // Leggendario/Mitico - barra quasi piena
                                    } elseif ($trait->rarity_percentage <= 10) {
                                        $barWidth = 75; // Epico
                                    } elseif ($trait->rarity_percentage <= 20) {
                                        $barWidth = 60; // Raro
                                    } elseif ($trait->rarity_percentage <= 40) {
                                        $barWidth = 40; // Poco comune
                                    } elseif ($trait->rarity_percentage <= 70) {
                                        $barWidth = 25; // Comune
                                    } else {
                                        $barWidth = 10; // Molto comune - barra quasi vuota
                                    }
                                @endphp
                                <div class="trait-rarity">
                                    <div class="rarity-bar">
                                        <div class="rarity-fill {{ $rarityClass }}" style="width: {{ number_format($barWidth, 1) }}%"></div>
                                    </div>
                                    <span class="rarity-text">{{ number_format($trait->rarity_percentage, 1) }}% have this</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <div class="empty-state-viewer" style="text-align: center; padding: 2rem; color: #666; font-style: italic;">
                    {{ __('traits.empty_state') }}
                </div>
            @endif
        </div>
    </div>
</div>

@if($canEdit)
{{-- Trait Modal --}}
<div class="trait-modal" id="trait-modal-viewer" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">{{ __('traits.modal_title') }}</h4>
            <button type="button"
                    class="modal-close"
                    onclick="TraitsViewer.closeModal()">
                ×
            </button>
        </div>

        <div class="modal-body">
            {{-- Step 1: Select Category --}}
            <div class="form-group">
                <label class="form-label">{{ __('traits.select_category') }}</label>
                <div class="category-selector" id="category-selector-viewer">
                    {{-- Categories will be inserted here by JS --}}
                </div>
            </div>

            {{-- Step 2: Select Trait Type --}}
            <div class="form-group" id="type-selector-group-viewer" style="display: none;">
                <label class="form-label">{{ __('traits.select_type') }}</label>
                <select class="form-select" id="trait-type-select-viewer" onchange="TraitsViewer.onTypeSelected()">
                    <option value="">{{ __('traits.choose_type') }}</option>
                </select>
            </div>

            {{-- Step 3: Select/Input Value --}}
            <div class="form-group" id="value-selector-group-viewer" style="display: none;">
                <label class="form-label">{{ __('traits.select_value') }}</label>
                <div id="value-input-container-viewer">
                    {{-- Input will be inserted here based on type --}}
                </div>
            </div>

            {{-- Preview --}}
            <div class="trait-preview" id="trait-preview-viewer" style="display: none;">
                <div class="preview-label">{{ __('traits.preview') }}</div>
                <div class="preview-card">
                    <span class="preview-type"></span>:
                    <span class="preview-value"></span>
                    <span class="preview-unit"></span>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button"
                    class="btn-cancel"
                    onclick="TraitsViewer.closeModal()">
                {{ __('traits.cancel') }}
            </button>
            <button type="button"
                    class="btn-confirm"
                    id="confirm-trait-btn-viewer"
                    onclick="TraitsViewer.addTrait()"
                    disabled>
                {{ __('traits.add') }}
            </button>
        </div>
    </div>
</div>

{{-- Toast Container --}}
<div class="toast-container" id="toast-container-viewer"></div>

{{-- JavaScript per gestire edit mode --}}
<script>
// Translations for JavaScript (loaded from Laravel)
window.TraitsTranslations = {
    remove_success: @json(__('traits.remove_success')),
    remove_error: @json(__('traits.remove_error')),
    network_error: @json(__('traits.network_error_js')),
    unauthorized: @json(__('traits.unauthorized')),
    confirm_remove: @json(__('traits.confirm_remove')),
    creator_only_modify: @json(__('traits.creator_only_modify')),
    modal_open_error: @json(__('traits.modal_open_error')),
    add_trait_error: @json(__('traits.add_trait_error')),
    unknown_error: @json(__('traits.unknown_error_js')),
    network_error_general: @json(__('traits.network_error_general')),
    add_success: @json(__('traits.add_success')),

    // SweetAlert2 translations
    confirm_delete_title: @json(__('traits.confirm_delete_title')),
    confirm_delete_text: @json(__('traits.confirm_delete_text')),
    confirm_delete_button: @json(__('traits.confirm_delete_button')),
    cancel_button: @json(__('traits.cancel_button')),
    delete_success_title: @json(__('traits.delete_success_title')),
    delete_success_text: @json(__('traits.delete_success_text')),
    delete_error_title: @json(__('traits.delete_error_title')),
    delete_error_text: @json(__('traits.delete_error_text'))
};

// Toast Notification System (extracted from traits-editor)
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
            </div>
            <button class="toast-close" onclick="ToastManager.close(this.parentNode)">×</button>
            <div class="toast-progress animate"></div>
        `;

        toast.innerHTML = content;
        this.container.appendChild(toast);

        // Trigger animation
        setTimeout(() => toast.classList.add('show'), 10);

        // Auto remove
        setTimeout(() => this.close(toast), duration);

        return toast;
    },

    close(toast) {
        if (!toast || !toast.parentNode) return;

        toast.classList.add('hide');
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
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

// TraitsViewer JavaScript (enhanced with ToastManager)
const TraitsViewer = {
    state: {
        egiId: null,
        canEdit: false,
        categories: [],
        availableTypes: [],
        modalData: {
            category_id: null,
            trait_type_id: null,
            value: null,
            currentType: null
        }
    },

    init: function(egiId) {
        console.log('TraitsViewer: Initializing for EGI', egiId);

        const container = document.getElementById(`traits-viewer-${egiId}`);
        if (!container) {
            console.error('TraitsViewer: Container not found for EGI', egiId);
            return;
        }

        this.state.egiId = egiId;
        this.state.canEdit = container.getAttribute('data-can-edit') === 'true';

        // Initialize ToastManager
        ToastManager.init();

        // Setup event listeners for remove buttons
        this.setupEventListeners(container);

        console.log('TraitsViewer: Edit mode -', this.state.canEdit ? 'ENABLED' : 'DISABLED');
        console.log('TraitsViewer: Initialization complete. State:', this.state);
    },

    setupEventListeners: function(container) {
        console.log('TraitsViewer: Setting up event listeners');

        // Find all remove buttons and add event listeners
        const removeButtons = container.querySelectorAll('.trait-remove');
        console.log('TraitsViewer: Found remove buttons:', removeButtons.length);

        removeButtons.forEach((button, index) => {
            console.log(`TraitsViewer: Setting up listener for button ${index}`);

            button.addEventListener('click', (e) => {
                e.stopPropagation();
                e.preventDefault();

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

    async removeTrait(traitId) {
        console.log('TraitsViewer.removeTrait called with traitId:', traitId);

        // Enhanced removeTrait function with ToastManager (extracted from traits-editor)
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
                // Show toast success message (NOT SweetAlert2!)
                ToastManager.success(window.TraitsTranslations.remove_success, '🎯 Trait Rimosso');

                // Rimuovi il trait dal DOM con animazione smooth
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

                // Aggiorna il counter
                const counter = document.querySelector('.traits-count');
                if (counter) {
                    const currentCount = parseInt(counter.textContent) || 0;
                    counter.textContent = Math.max(0, currentCount - 1);
                    console.log('Updated counter to:', currentCount - 1);
                }

                console.log('TraitsViewer: Trait removed successfully');
            } else {
                // Error toast
                console.error('Server returned error:', data.message);
                ToastManager.error(window.TraitsTranslations.remove_error + ': ' + (data.message || window.TraitsTranslations.unknown_error), '❌ Errore');
            }
        } catch (error) {
            console.error('TraitsViewer: Error removing trait:', error);
            ToastManager.error(window.TraitsTranslations.network_error, '🌐 Errore di Rete');
        }
    },

    // Modal functions (copied from traits-editor)
    async openModal() {
        if (!this.state.canEdit) {
            console.warn('TraitsViewer: Modal access denied - readonly mode');
            ToastManager.warning(window.TraitsTranslations.creator_only_modify);
            return;
        }

        try {
            const modal = document.getElementById('trait-modal-viewer');
            if (!modal) return;

            modal.style.display = 'flex';
            this.resetModal();

            // Load categories if not loaded
            if (this.state.categories.length === 0) {
                await this.loadCategories();
            }

            this.renderModalCategories();
        } catch (error) {
            console.error('Error opening modal:', error);
            ToastManager.error(window.TraitsTranslations.modal_open_error, '❌ Errore');
        }
    },

    closeModal() {
        const modal = document.getElementById('trait-modal-viewer');
        if (modal) {
            modal.style.display = 'none';
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

        document.getElementById('type-selector-group-viewer').style.display = 'none';
        document.getElementById('value-selector-group-viewer').style.display = 'none';
        document.getElementById('trait-preview-viewer').style.display = 'none';
        document.getElementById('confirm-trait-btn-viewer').disabled = true;
    },

    async loadCategories() {
        try {
            const response = await fetch('/traits/categories');
            const data = await response.json();

            if (data.success && data.categories) {
                this.state.categories = data.categories;
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

// Assign to window for global access
window.TraitsViewer = TraitsViewer;

// Auto-initialize quando il DOM è pronto
document.addEventListener('DOMContentLoaded', function() {
    console.log('TraitsViewer: DOM loaded, initializing...');
    const container = document.querySelector('[id^="traits-viewer-"]');

    if (container) {
        const egiId = container.getAttribute('data-egi-id');
        if (egiId) {
            TraitsViewer.init(egiId);
        }
    }
});
</script>
@endif
