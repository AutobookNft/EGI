{{-- resources/views/components/egi/traits-editor.blade.php --}}
{{--
    EGI Traits Editor Component - EDITING
    Gestion                {{-- resources/views/components/egi/traits-editor.blade.php --}}
{{--
    EGI Traits Editor Component - EDITING & VIEWING
    Gestione aggiunta/rimozione traits con controllo autorizzazioni
--}}
@props([
    'egi' => null,
    'canEdit' => false
])

{{-- Include CSS con Vite --}}
@vite(['resources/css/traits-manager.css'])

<div class="egi-traits-editor"
     id="traits-editor-{{ $egi ? $egi->id : 'new' }}"
     data-egi-id="{{ $egi ? $egi->id : '' }}"
     data-can-edit="{{ $canEdit ? 'true' : 'false' }}">

    {{-- Categories Navigation --}}
    <div class="trait-categories" id="categories-nav">
        {{-- Categories will be inserted here by JS --}}
    </div>

    @if($canEdit)
        {{-- Area Editor - Solo bottoni di controllo per il creator --}}
        <div class="traits-editor-controls" style="margin-top: 1rem;">

            {{-- Add Trait Button --}}
            <button type="button"
                    class="add-trait-btn"
                    onclick="TraitsEditor.openModal()"
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

            {{-- Save Button --}}
            <button type="button"
                    onclick="TraitsEditor.saveTraits()"
                    class="save-traits-btn"
                    style="background: #2d5016 !important;
                           color: white !important;
                           border: none !important;
                           padding: 0.75rem 1.5rem !important;
                           border-radius: 0.5rem !important;
                           font-weight: 600 !important;
                           cursor: pointer !important;
                           width: 100% !important;
                           font-size: 1rem !important;
                           box-shadow: 0 2px 4px rgba(45, 80, 22, 0.2) !important;"
                    onmouseover="this.style.backgroundColor='#3d6026'"
                    onmouseout="this.style.backgroundColor='#2d5016'">
                {{ __('traits.save_all_traits') }}
            </button>

            {{-- Hidden area for editing state (used by JS) --}}
            {{-- <div class="traits-list editing" style="display: none;">
                <div class="traits-grid" id="traits-grid">

                </div>
            </div> --}}
        </div>

        {{-- Hidden input for form submission --}}
        <input type="hidden"
               name="traits"
               id="traits-json-{{ $egi ? $egi->id : 'new' }}"
               value="[]">
    @else
        {{-- Visualizzazione pubblica - Solo traits list e categorie con conteggi --}}
        <div class="traits-list readonly">
            {{-- Message for non-authorized users --}}
            <div class="public-view-notice" style="text-align: center; padding: 1rem; background: rgba(45, 80, 22, 0.1); border-radius: 0.5rem; margin-bottom: 1rem; color: #2d5016;">
                <svg style="width: 1.5rem; height: 1.5rem; margin: 0 auto 0.5rem; display: block;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p style="margin: 0; font-size: 0.875rem;">{{ __('Solo il creator può modificare i traits di questo EGI') }}</p>
            </div>

            {{-- Traits Grid (readonly mode) --}}
            <div class="traits-grid readonly" id="traits-grid-readonly">
                {{-- Traits will be inserted here by JS --}}
            </div>
        </div>
    @endif
</div>

{{-- Trait Modal (shared) - Solo per modalità editing --}}
@if($canEdit)
@once
<div class="trait-modal" id="trait-modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">{{ __('traits.modal_title') }}</h4>
            <button type="button"
                    class="modal-close"
                    onclick="TraitsEditor.closeModal()">
                ×
            </button>
        </div>

        <div class="modal-body">
            {{-- Step 1: Select Category --}}
            <div class="form-group">
                <label class="form-label">{{ __('traits.select_category') }}</label>
                <div class="category-selector" id="category-selector">
                    {{-- Categories will be inserted here by JS --}}
                </div>
            </div>

            {{-- Step 2: Select Trait Type --}}
            <div class="form-group" id="type-selector-group" style="display: none;">
                <label class="form-label">{{ __('traits.select_type') }}</label>
                <select class="form-select" id="trait-type-select" onchange="TraitsEditor.onTypeSelected()">
                    <option value="">{{ __('traits.choose_type') }}</option>
                </select>
            </div>

            {{-- Step 3: Select/Input Value --}}
            <div class="form-group" id="value-selector-group" style="display: none;">
                <label class="form-label">{{ __('traits.select_value') }}</label>
                <div id="value-input-container">
                    {{-- Input will be inserted here based on type --}}
                </div>
            </div>

            {{-- Preview --}}
            <div class="trait-preview" id="trait-preview" style="display: none;">
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
                    onclick="TraitsEditor.closeModal()">
                {{ __('traits.cancel') }}
            </button>
            <button type="button"
                    class="btn-confirm"
                    id="confirm-trait-btn"
                    onclick="TraitsEditor.addTrait()"
                    disabled>
                {{ __('traits.add') }}
            </button>
        </div>
    </div>
</div>
@endonce
@endif

{{-- Toast Container --}}
@once
<div class="toast-container" id="toast-container"></div>
@endonce

<script>
// Traduzioni per JavaScript
window.TraitsTranslations = {
    loading_categories: '{{ __('traits.loading_categories') }}',
    loading_types: '{{ __('traits.loading_types') }}',
    choose_type: '{{ __('traits.choose_type') }}',
    choose_value: '{{ __('traits.choose_value') }}',
    insert_value: '{{ __('traits.insert_value') }}',
    insert_numeric_value: '{{ __('traits.insert_numeric_value') }}',
    preview: '{{ __('traits.preview') }}',
    modal_error: '{{ __('traits.modal_error') }}',
    save_success: '{{ __('traits.save_success') }}',
    save_error: '{{ __('traits.save_error') }}',
    network_error: '{{ __('traits.network_error') }}',
    unknown_error: '{{ __('traits.unknown_error') }}'
};

// Include traits-common.js functions here if needed, or load external file

// Toast Notification System
window.ToastManager = {
    container: null,

    init() {
        this.container = document.getElementById('toast-container');
        if (!this.container) {
            this.container = document.createElement('div');
            this.container.className = 'toast-container';
            this.container.id = 'toast-container';
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
</script>

<script>
(function() {
    'use strict';

    // Editor-specific JavaScript
    window.TraitsEditor = {
        state: {
            egiId: null,
            canEdit: false,
            categories: [],
            availableTypes: [],
            editingTraits: [],
            modalData: {
                category_id: null,
                trait_type_id: null,
                value: null,
                currentType: null
            }
        },

        init: function(egiId) {
            console.log('TraitsEditor: Initializing for EGI', egiId);

            const container = document.getElementById(`traits-editor-${egiId}`);
            if (!container) {
                console.error('TraitsEditor: Container not found for EGI', egiId);
                return;
            }

            this.state.egiId = egiId;

            // Controlla se è in modalità editing o readonly
            const canEdit = container.getAttribute('data-can-edit') === 'true';
            this.state.canEdit = canEdit;

            console.log('TraitsEditor: Mode -', canEdit ? 'EDITING' : 'READONLY');

            this.loadCategories();
            // I traits vengono caricati dal viewer via PHP, non qui
        },

        async loadCategories() {
            try {
                console.log('TraitsEditor: Loading categories...');

                // Aggiungi timeout per server lenti
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 10000); // 10 secondi timeout

                const response = await fetch('/traits/categories', {
                    signal: controller.signal,
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                clearTimeout(timeoutId);

                console.log('TraitsEditor: Response status:', response.status);

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                console.log('TraitsEditor: Categories response:', data);

                if (data.success && data.categories && Array.isArray(data.categories)) {
                    if (data.categories.length > 0) {
                        this.state.categories = data.categories;
                        console.log('TraitsEditor: Categories loaded successfully:', this.state.categories.length);
                        this.renderCategories();
                    } else {
                        console.warn('TraitsEditor: Server returned empty categories array - using fallback');
                        throw new Error('Server returned empty categories array');
                    }
                } else {
                    throw new Error(data.message || 'Failed to load categories - invalid response format');
                }
            } catch (error) {
                console.error('TraitsEditor: Error loading categories:', error);

                if (error.name === 'AbortError') {
                    console.error('TraitsEditor: Request timeout - server too slow');
                }

                // Fallback: usa categorie statiche se il server fallisce
                this.state.categories = [
                    {id: 1, name: 'Materials', slug: 'materials', icon: '📦'},
                    {id: 2, name: 'Visual', slug: 'visual', icon: '🎨'},
                    {id: 3, name: 'Dimensions', slug: 'dimensions', icon: '📐'},
                    {id: 4, name: 'Special', slug: 'special', icon: '⚡'},
                    {id: 5, name: 'Sustainability', slug: 'sustainability', icon: '🌿'}
                ];

                console.log('TraitsEditor: Using fallback categories');
                this.renderCategories();
            }
        },

        renderCategories() {
            const nav = document.getElementById('categories-nav');
            if (!nav) return;

            nav.innerHTML = this.state.categories.map(cat => `
                <button type="button"
                        class="category-tab"
                        data-category-id="${cat.id}"
                        onclick="TraitsEditor.filterByCategory(${cat.id})">
                    <span class="category-icon">${cat.icon}</span>
                    <span class="category-name">${cat.name}</span>
                    <span class="category-count" data-category="${cat.id}">0</span>
                </button>
            `).join('');

            // Aggiorna i conteggi dopo aver renderizzato le categorie
            this.updateCategoryCounters();
        },

        async openModal() {
            // Blocca l'apertura del modal se non in modalità editing
            if (!this.state.canEdit) {
                console.warn('TraitsEditor: Modal access denied - readonly mode');
                ToastManager.warning('Solo il creator può modificare i traits di questo EGI');
                return;
            }

            try {
                const modal = document.getElementById('trait-modal');
                if (!modal) return;

                modal.style.display = 'flex';
                this.resetModal();

                // Mostra subito il placeholder
                this.renderModalCategories();

                // Forza il caricamento delle categorie se non sono caricate
                if (this.state.categories.length === 0) {
                    console.log('TraitsEditor: Modal opened, categories not loaded, forcing load...');
                    await this.loadCategoriesWithRetry();
                } else {
                    console.log('TraitsEditor: Modal opened with cached categories');
                }

                // Rendi nuovamente le categorie dopo il caricamento
                this.renderModalCategories();
            } catch (error) {
                console.error('Error opening modal:', error);
                // Mostra un toast di errore elegante
                ToastManager.error(window.TraitsTranslations.modal_error, '🎯 Traits Editor');
            }
        },

        async loadCategoriesWithRetry(maxRetries = 3) {
            for (let attempt = 1; attempt <= maxRetries; attempt++) {
                try {
                    console.log(`TraitsEditor: Loading categories attempt ${attempt}/${maxRetries}`);
                    await this.loadCategories();

                    // Se siamo arrivati qui senza errori e abbiamo categorie, esce
                    if (this.state.categories.length > 0) {
                        console.log('TraitsEditor: Categories loaded successfully on attempt', attempt);
                        return;
                    }
                } catch (error) {
                    console.error(`TraitsEditor: Attempt ${attempt} failed:`, error);

                    if (attempt === maxRetries) {
                        console.log('TraitsEditor: All attempts failed, using fallback categories');
                        throw error;
                    }

                    // Aspetta prima di ritentare (backoff esponenziale)
                    const delay = Math.pow(2, attempt - 1) * 1000; // 1s, 2s, 4s
                    console.log(`TraitsEditor: Waiting ${delay}ms before retry...`);
                    await new Promise(resolve => setTimeout(resolve, delay));
                }
            }
        },

        closeModal() {
            const modal = document.getElementById('trait-modal');
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

            document.getElementById('type-selector-group').style.display = 'none';
            document.getElementById('value-selector-group').style.display = 'none';
            document.getElementById('trait-preview').style.display = 'none';
            document.getElementById('confirm-trait-btn').disabled = true;
        },

        renderModalCategories() {
            const selector = document.getElementById('category-selector');
            if (!selector) return;

            // Se le categorie non sono ancora caricate, mostra un placeholder
            if (this.state.categories.length === 0) {
                selector.innerHTML = '<div class="loading-placeholder">' + window.TraitsTranslations.loading_categories + '</div>';
                return;
            }

            selector.innerHTML = this.state.categories.map(cat => `
                <button type="button"
                        class="category-option"
                        onclick="TraitsEditor.selectCategory(${cat.id})"
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

            // Load trait types for this category
            try {
                const response = await fetch(`/traits/categories/${categoryId}/types`);
                const data = await response.json();

                if (data.success) {
                    this.state.availableTypes = data.types;
                    this.renderTypeSelector();
                    document.getElementById('type-selector-group').style.display = 'block';
                }
            } catch (error) {
                console.error('Error loading trait types:', error);
            }
        },

        renderTypeSelector() {
            const select = document.getElementById('trait-type-select');
            if (!select) return;

            select.innerHTML = '<option value="">' + window.TraitsTranslations.choose_type + '</option>' +
                this.state.availableTypes.map(type =>
                    `<option value="${type.id}">${type.name}</option>`
                ).join('');
        },

        onTypeSelected() {
            const select = document.getElementById('trait-type-select');
            const typeId = select.value;

            if (!typeId) return;

            const type = this.state.availableTypes.find(t => t.id == typeId);
            this.state.modalData.trait_type_id = typeId;
            this.state.modalData.currentType = type;

            this.renderValueInput(type);
            document.getElementById('value-selector-group').style.display = 'block';
        },

        renderValueInput(type) {
            const container = document.getElementById('value-input-container');
            if (!container) return;

            let inputHtml = '';

            // Parse allowed_values se è una stringa JSON
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
                // Dropdown for predefined values
                inputHtml = `
                    <select class="form-select" id="trait-value-input" onchange="TraitsEditor.onValueChanged()">
                        <option value="">{{ __('traits.choose_value') }}</option>
                        ${allowedValues.map(val => `<option value="${val}">${val}</option>`).join('')}
                    </select>
                `;
            } else if (type.display_type === 'number' || type.display_type === 'percentage' || type.display_type === 'boost_number') {
                // Number input
                const min = type.display_type === 'percentage' ? '0' : '';
                const max = (type.display_type === 'percentage' || type.display_type === 'boost_number') ? '100' : '';
                inputHtml = `
                    <div class="input-group">
                        <input type="number"
                               class="form-input"
                               id="trait-value-input"
                               ${min !== '' ? `min="${min}"` : ''}
                               ${max !== '' ? `max="${max}"` : ''}
                               step="0.01"
                               placeholder="${window.TraitsTranslations.insert_numeric_value}"
                               oninput="TraitsEditor.onValueChanged()">
                        ${type.unit ? `<span class="input-suffix">${type.unit}</span>` : ''}
                    </div>
                `;
            } else if (type.display_type === 'date') {
                // Date input
                inputHtml = `
                    <input type="date"
                           class="form-input"
                           id="trait-value-input"
                           oninput="TraitsEditor.onValueChanged()">
                `;
            } else {
                // Text input
                inputHtml = `
                    <input type="text"
                           class="form-input"
                           id="trait-value-input"
                           placeholder="${window.TraitsTranslations.insert_value}"
                           oninput="TraitsEditor.onValueChanged()">
                `;
            }

            container.innerHTML = inputHtml;
        },

        onValueChanged() {
            const input = document.getElementById('trait-value-input');
            const value = input.value.trim();

            this.state.modalData.value = value;

            if (value) {
                this.updatePreview();
                document.getElementById('confirm-trait-btn').disabled = false;
            } else {
                document.getElementById('trait-preview').style.display = 'none';
                document.getElementById('confirm-trait-btn').disabled = true;
            }
        },

        updatePreview() {
            const preview = document.getElementById('trait-preview');
            const type = this.state.modalData.currentType;

            if (!preview || !type) return;

            preview.querySelector('.preview-type').textContent = type.name;
            preview.querySelector('.preview-value').textContent = this.state.modalData.value;
            preview.querySelector('.preview-unit').textContent = type.unit || '';

            preview.style.display = 'block';
        },

        addTrait() {
            // Blocca l'aggiunta se non in modalità editing
            if (!this.state.canEdit) {
                console.warn('TraitsEditor: Add trait denied - readonly mode');
                ToastManager.warning('Solo il creator può modificare i traits di questo EGI');
                return;
            }

            if (!this.state.modalData.value || !this.state.modalData.trait_type_id) {
                return;
            }

            const category = this.state.categories.find(c => c.id === this.state.modalData.category_id);
            const type = this.state.modalData.currentType;

            const newTrait = {
                // Per i nuovi trait, non includiamo l'ID (sarà creato dal backend)
                tempId: Date.now(), // ID temporaneo per il frontend
                category_id: this.state.modalData.category_id,
                category_name: category.name,
                trait_type_id: this.state.modalData.trait_type_id,
                type_name: type.name,
                value: this.state.modalData.value,
                display_value: this.state.modalData.value,
                display_type: type.display_type,
                unit: type.unit,
                sort_order: this.state.editingTraits.length
            };

            this.state.editingTraits.push(newTrait);
            this.updateUI();
            this.closeModal();
        },

        removeTrait(index) {
            // Blocca la rimozione se non in modalità editing
            if (!this.state.canEdit) {
                console.warn('TraitsEditor: Remove trait denied - readonly mode');
                ToastManager.warning('Solo il creator può modificare i traits di questo EGI');
                return;
            }

            this.state.editingTraits.splice(index, 1);
            // Reorder
            this.state.editingTraits.forEach((trait, i) => {
                trait.sort_order = i;
            });
            this.updateUI();
        },

        updateUI() {
            this.renderEditingTraits();
            this.updateCounter();
            this.updateCategoryCounters();
            this.updateButtons();
            this.updateHiddenInput();
        },

        renderEditingTraits() {
            // Determina quale grid utilizzare in base alla modalità
            const gridId = this.state.canEdit ? 'traits-grid' : 'traits-grid-readonly';
            const grid = document.getElementById(gridId);
            const emptyState = document.getElementById('empty-state');

            if (!grid) {
                console.error('TraitsEditor: Grid not found -', gridId);
                return;
            }

            if (this.state.editingTraits.length === 0) {
                if (emptyState && this.state.canEdit) {
                    emptyState.style.display = 'block';
                }
                grid.innerHTML = this.state.canEdit ? '' : '<div class="no-traits-message" style="text-align: center; padding: 2rem; color: #666;">Nessun trait disponibile</div>';
                return;
            }

            if (emptyState && this.state.canEdit) {
                emptyState.style.display = 'none';
            }

            // Forza layout mobile se necessario
            if (window.innerWidth <= 768) {
                grid.style.display = 'grid';
                grid.style.gridTemplateColumns = 'repeat(3, 1fr)';
                grid.style.gap = '0.5rem';
            }

            grid.innerHTML = this.state.editingTraits.map((trait, index) => {
                const categoryColor = this.getCategoryColor(trait.category_id);

                // Renderizza diversamente in base alla modalità
                if (this.state.canEdit) {
                    // Modalità editing - con pulsante rimuovi
                    return `
                        <div class="trait-card" data-category="${trait.category_id}">
                            <div class="trait-header">
                                <span class="trait-category-badge" style="background-color: ${categoryColor}">
                                    ${this.getCategoryIcon(trait.category_id)}
                                </span>
                                <button type="button"
                                        class="trait-remove"
                                        onclick="TraitsEditor.removeTrait(${index})">
                                    ×
                                </button>
                            </div>
                            <div class="trait-content">
                                <div class="trait-type">${trait.type_name}</div>
                                <div class="trait-value">
                                    <span>${this.formatTraitValue(trait)}</span>
                                    ${trait.unit ? `<span class="trait-unit">${trait.unit}</span>` : ''}
                                </div>
                            </div>
                        </div>
                    `;
                } else {
                    // Modalità readonly - senza pulsante rimuovi
                    return `
                        <div class="trait-card readonly" data-category="${trait.category_id}">
                            <div class="trait-header readonly">
                                <span class="trait-category-badge" style="background-color: ${categoryColor}">
                                    ${this.getCategoryIcon(trait.category_id)}
                                </span>
                            </div>
                            <div class="trait-content">
                                <div class="trait-type">${trait.type_name}</div>
                                <div class="trait-value">
                                    <span>${this.formatTraitValue(trait)}</span>
                                    ${trait.unit ? `<span class="trait-unit">${trait.unit}</span>` : ''}
                                </div>
                            </div>
                        </div>
                    `;
                }
            }).join('');
        },

        updateCounter() {
            const counter = document.querySelector(`#traits-editor-${this.state.egiId} .traits-count`);
            if (counter) {
                counter.textContent = this.state.editingTraits.length;
            }
        },

        updateCategoryCounters() {
            // Conteggio traits per categoria
            const categoryCounts = {};

            // Inizializza tutti i conteggi a 0
            this.state.categories.forEach(cat => {
                categoryCounts[cat.id] = 0;
            });

            // Conta i traits per categoria (include sia editing che esistenti)
            this.state.editingTraits.forEach(trait => {
                if (categoryCounts.hasOwnProperty(trait.category_id)) {
                    categoryCounts[trait.category_id]++;
                }
            });

            // Aggiorna i badge delle categorie
            Object.keys(categoryCounts).forEach(categoryId => {
                const badge = document.querySelector(`[data-category="${categoryId}"]`);
                if (badge) {
                    badge.textContent = categoryCounts[categoryId];
                }
            });
        },

        updateButtons() {
            const addBtn = document.getElementById('add-trait-btn');
            const saveBtn = document.getElementById('save-traits-btn');

            if (addBtn) {
                addBtn.style.display = this.state.editingTraits.length > 0 && this.state.editingTraits.length < 30 ? 'flex' : 'none';
            }

            if (saveBtn) {
                saveBtn.style.display = this.state.editingTraits.length > 0 ? 'flex' : 'none';
            }
        },

        updateHiddenInput() {
            const input = document.getElementById(`traits-json-${this.state.egiId}`);
            if (input) {
                input.value = JSON.stringify(this.state.editingTraits);
            }
        },

        async saveTraits() {
            // Blocca il salvataggio se non in modalità editing
            if (!this.state.canEdit) {
                console.warn('TraitsEditor: Save traits denied - readonly mode');
                ToastManager.warning('Solo il creator può modificare i traits di questo EGI');
                return;
            }

            if (this.state.editingTraits.length === 0) return;

            try {
                // PRIMA: Carica i traits esistenti dal server
                console.log('Loading existing traits before save...');
                const existingResponse = await fetch(`/egis/${this.state.egiId}/traits`);
                const existingData = await existingResponse.json();

                let allTraits = [];

                // Aggiungi i traits esistenti (con i loro ID originali)
                if (existingData.success && existingData.traits) {
                    allTraits = existingData.traits.map(trait => ({
                        id: trait.id, // ID reale dal database
                        category_id: trait.category_id,
                        trait_type_id: trait.trait_type_id,
                        value: trait.value,
                        display_value: trait.display_value || trait.value,
                        sort_order: trait.sort_order
                    }));
                }

                // Aggiungi i nuovi traits (senza ID, saranno creati)
                this.state.editingTraits.forEach(newTrait => {
                    allTraits.push({
                        // Nessun ID per i nuovi traits
                        category_id: newTrait.category_id,
                        trait_type_id: newTrait.trait_type_id,
                        value: newTrait.value,
                        display_value: newTrait.display_value || newTrait.value,
                        sort_order: allTraits.length // Aggiungi alla fine
                    });
                });

                console.log('Saving all traits (existing + new):', allTraits);

                const response = await fetch(`/egis/${this.state.egiId}/traits`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        traits: allTraits
                    })
                });

                const data = await response.json();

                if (data.success) {
                    ToastManager.success(window.TraitsTranslations.save_success, '🎯 Traits Salvati');
                    // Reset editing state
                    this.state.editingTraits = [];
                    this.updateUI();

                    // Reload page to show updated viewer
                    setTimeout(() => location.reload(), 1500);
                } else {
                    ToastManager.error(window.TraitsTranslations.save_error + ': ' + (data.message || window.TraitsTranslations.unknown_error), '❌ Errore Salvataggio');
                }
            } catch (error) {
                console.error('Error saving traits:', error);
                ToastManager.error(window.TraitsTranslations.network_error, '🌐 Errore di Rete');
            }
        },

        getCategoryColor(categoryId) {
            const colors = {
                1: '#D4A574', // Materials - Oro
                2: '#8E44AD', // Visual - Viola
                3: '#1B365D', // Dimensions - Blu
                4: '#E67E22', // Special - Arancio
                5: '#2D5016'  // Sustainability - Verde
            };
            return colors[categoryId] || '#6B6B6B';
        },

        getCategoryIcon(categoryId) {
            const icons = {
                1: '📦', // Materials
                2: '🎨', // Visual
                3: '📐', // Dimensions
                4: '⚡', // Special
                5: '🌿'  // Sustainability
            };
            return icons[categoryId] || '🏷️';
        },

        formatTraitValue(trait) {
            if (trait.display_type === 'number' && trait.value) {
                return parseFloat(trait.value).toLocaleString();
            }
            return trait.value || '';
        },

        filterByCategory(categoryId) {
            // Implementation for category filtering if needed
            console.log('Filter by category:', categoryId);
        }

    };

    // Auto-initialize quando il DOM è pronto - SOLO se l'editor è in modalità editing
    document.addEventListener('DOMContentLoaded', function() {
        const editor = document.querySelector('.egi-traits-editor');
        if (editor) {
            const canEdit = editor.getAttribute('data-can-edit') === 'true';
            if (canEdit) {
                const egiId = editor.dataset.egiId;
                if (egiId) {
                    TraitsEditor.init(egiId);
                }
            }
        }
    });

})();
</script>
