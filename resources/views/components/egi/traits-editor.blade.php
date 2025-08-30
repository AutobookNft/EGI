{{-- resources/views/components/egi/traits-editor.blade.php --}}
{{-- 
    EGI Traits Editor Component - EDITING
    Gestione aggiunta/rimozione traits
--}}
@props([
    'egi' => null
])

{{-- Include CSS con Vite --}}
@vite(['resources/css/traits-manager.css'])

<div class="egi-traits-editor" 
     id="traits-editor-{{ $egi ? $egi->id : 'new' }}"
     data-egi-id="{{ $egi ? $egi->id : '' }}">
    
    {{-- Header con counter --}}
    <div class="traits-header">
        <h3 class="traits-title">
            <span class="traits-icon">🎯</span>
            {{ __('Gestione Tratti') }}
        </h3>
        <div class="traits-meta">
            <span class="trait-counter">
                <span class="traits-count">0</span>/30
            </span>
        </div>
    </div>

    {{-- Categories Navigation --}}
    <div class="trait-categories" id="categories-nav">
        {{-- Categories will be inserted here by JS --}}
    </div>

    {{-- Editing Area --}}
    <div class="traits-list editing">
        {{-- Empty State --}}
        <div class="empty-state" id="empty-state" style="display: block;">
            <svg class="empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
            </svg>
            <p class="empty-text">Nessun tratto aggiunto. I tratti rendono il tuo EGI unico e ricercabile.</p>
            <button type="button" 
                    class="empty-cta"
                    onclick="TraitsEditor.openModal()">
                Aggiungi il primo tratto
            </button>
        </div>

        <!-- Add Trait Button -->
        <div class="add-trait-section" style="display: none;">
            <button type="button" 
                    class="btn btn-primary add-trait-btn"
                    onclick="TraitsEditor.openModal()">
                <span class="btn-icon">+</span>
                <span class="btn-text">Aggiungi Nuovo Tratto</span>
            </button>
        </div>
        </div>

        {{-- Traits Grid --}}
        <div class="traits-grid" id="traits-grid-editor">
            {{-- Editing traits will be inserted here by JS --}}
        </div>
    </div>

    {{-- Add Trait Button --}}
    <button type="button" 
            class="add-trait-button"
            id="add-trait-btn"
            onclick="TraitsEditor.openModal()"
            style="display: none;">
        <svg class="button-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Aggiungi Tratto
    </button>
    
    {{-- Save All Traits Button --}}
    <button type="button" 
            class="save-traits-button"
            onclick="TraitsEditor.saveTraits()"
            style="display: none;"
            id="save-traits-btn">
        <svg class="button-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"/>
        </svg>
        Save All Traits
    </button>

    {{-- Hidden input for form submission --}}
    <input type="hidden" 
           name="traits" 
           id="traits-json-{{ $egi ? $egi->id : 'new' }}"
           value="[]">
</div>

{{-- Trait Modal (shared) --}}
@once
<div class="trait-modal" id="trait-modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Aggiungi Nuovo Tratto</h4>
            <button type="button" 
                    class="modal-close"
                    onclick="TraitsEditor.closeModal()">
                ×
            </button>
        </div>

        <div class="modal-body">
            {{-- Step 1: Select Category --}}
            <div class="form-group">
                <label class="form-label">Seleziona Categoria</label>
                <div class="category-selector" id="category-selector">
                    {{-- Categories will be inserted here by JS --}}
                </div>
            </div>

            {{-- Step 2: Select Trait Type --}}
            <div class="form-group" id="type-selector-group" style="display: none;">
                <label class="form-label">Seleziona Tipo</label>
                <select class="form-select" id="trait-type-select" onchange="TraitsEditor.onTypeSelected()">
                    <option value="">Scegli un tipo...</option>
                </select>
            </div>

            {{-- Step 3: Select/Input Value --}}
            <div class="form-group" id="value-selector-group" style="display: none;">
                <label class="form-label">Inserisci Valore</label>
                <div id="value-input-container">
                    {{-- Input will be inserted here based on type --}}
                </div>
            </div>

            {{-- Preview --}}
            <div class="trait-preview" id="trait-preview" style="display: none;">
                <div class="preview-label">Anteprima</div>
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
                Annulla
            </button>
            <button type="button" 
                    class="btn-confirm"
                    id="confirm-trait-btn"
                    onclick="TraitsEditor.addTrait()"
                    disabled>
                Aggiungi
            </button>
        </div>
    </div>
</div>
@endonce

<script>
// Include traits-common.js functions here if needed, or load external file
</script>

<script>
(function() {
    'use strict';

    // Editor-specific JavaScript
    window.TraitsEditor = {
        state: {
            egiId: null,
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
            this.loadCategories();
            this.loadExistingTraits(); // Carica trait esistenti per la modifica
        },

        async loadExistingTraits() {
            try {
                const response = await fetch(`/egis/${this.state.egiId}/traits`);
                const data = await response.json();
                
                if (data.success && data.traits) {
                    // Converte i trait esistenti nel formato per editing
                    this.state.editingTraits = data.traits.map(trait => ({
                        id: trait.id, // ID reale dal database
                        tempId: trait.id, // Usa l'ID reale anche come tempId
                        category_id: trait.category_id,
                        category_name: trait.category ? trait.category.name : 'Unknown',
                        trait_type_id: trait.trait_type_id,
                        type_name: trait.trait_type ? trait.trait_type.name : 'Unknown',
                        value: trait.value,
                        display_value: trait.display_value || trait.value,
                        display_type: trait.trait_type ? trait.trait_type.display_type : 'text',
                        unit: trait.trait_type ? trait.trait_type.unit : null,
                        sort_order: trait.sort_order || 0
                    }));
                    
                    console.log('TraitsEditor: Loaded existing traits for editing:', this.state.editingTraits);
                    this.updateUI();
                }
            } catch (error) {
                console.error('TraitsEditor: Error loading existing traits:', error);
            }
        },

        async loadCategories() {
            try {
                const response = await fetch('/traits/categories');
                const data = await response.json();
                
                if (data.success) {
                    this.state.categories = data.categories;
                    this.renderCategories();
                }
            } catch (error) {
                console.error('Error loading categories:', error);
                // Fallback data
                this.state.categories = [
                    {id: 1, name: 'Materials', slug: 'materials', icon: '📦'},
                    {id: 2, name: 'Visual', slug: 'visual', icon: '🎨'},
                    {id: 3, name: 'Dimensions', slug: 'dimensions', icon: '📐'},
                    {id: 4, name: 'Special', slug: 'special', icon: '⚡'},
                    {id: 5, name: 'Sustainability', slug: 'sustainability', icon: '🌿'}
                ];
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
        },

        async openModal() {
            try {
                const modal = document.getElementById('trait-modal');
                if (!modal) return;

                // Assicurati che le categorie siano caricate prima di aprire la modale
                if (this.state.categories.length === 0) {
                    console.log('TraitsEditor: Categories not loaded, loading now...');
                    await this.loadCategories();
                }

                modal.style.display = 'flex';
                this.resetModal();
                this.renderModalCategories();
            } catch (error) {
                console.error('Error opening modal:', error);
                // Mostra un messaggio di errore all'utente se necessario
                alert('Errore nell\'apertura della modale. Riprova.');
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
                selector.innerHTML = '<div class="loading-placeholder">Caricamento categorie...</div>';
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

            select.innerHTML = '<option value="">Scegli un tipo...</option>' + 
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
                        <option value="">Scegli un valore...</option>
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
                               placeholder="Inserisci valore numerico"
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
                           placeholder="Inserisci valore"
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
            this.updateButtons();
            this.updateHiddenInput();
        },

        renderEditingTraits() {
            const grid = document.getElementById('traits-grid-editor');
            const emptyState = document.getElementById('empty-state');
            
            if (!grid || !emptyState) return;

            if (this.state.editingTraits.length === 0) {
                emptyState.style.display = 'block';
                grid.innerHTML = '';
                return;
            }

            emptyState.style.display = 'none';
            
            grid.innerHTML = this.state.editingTraits.map((trait, index) => {
                const categoryColor = this.getCategoryColor(trait.category_id);
                
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
            }).join('');
        },

        updateCounter() {
            const counter = document.querySelector(`#traits-editor-${this.state.egiId} .traits-count`);
            if (counter) {
                counter.textContent = this.state.editingTraits.length;
            }
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
            if (this.state.editingTraits.length === 0) return;

            try {
                const response = await fetch(`/egis/${this.state.egiId}/traits`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        traits: this.state.editingTraits
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    alert('Traits salvati con successo!');
                    // Reset editing state
                    this.state.editingTraits = [];
                    this.updateUI();
                    
                    // Reload page to show updated viewer
                    location.reload();
                } else {
                    alert('Errore nel salvataggio: ' + (data.message || 'Errore sconosciuto'));
                }
            } catch (error) {
                console.error('Error saving traits:', error);
                alert('Errore di rete nel salvataggio');
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

    // Auto-initialize quando il DOM è pronto
    document.addEventListener('DOMContentLoaded', function() {
        const editor = document.querySelector('.egi-traits-editor');
        if (editor) {
            const egiId = editor.dataset.egiId;
            if (egiId) {
                TraitsEditor.init(egiId);
            }
        }
    });

})();
</script>
