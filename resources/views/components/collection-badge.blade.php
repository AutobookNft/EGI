{{-- 
📜 Collection Badge Component Template
Componente autonomo per il badge della collection con TypeScript integrato
--}}

@if($shouldRender())
<div id="{{ $uniqueId }}" 
     class="collection-badge-component items-center {{ $getPositionClasses() }} {{ $getSizeClasses()['container'] }}"
     data-collection-id="{{ $collectionId }}"
     data-can-edit="{{ $canEdit ? 'true' : 'false' }}"
     data-size="{{ $size }}"
     data-position="{{ $position }}">
    
    <a href="{{ $getBadgeUrl() }}" 
       class="collection-badge-link flex items-center transition border rounded-lg border-sky-700 bg-sky-900/60 text-sky-300 hover:border-sky-600 hover:bg-sky-800 {{ $getSizeClasses()['container'] }}"
       title="{{ $getBadgeTitle() }}">
        
        <span class="collection-badge-icon material-symbols-outlined {{ $getSizeClasses()['icon'] }}" 
              aria-hidden="true">folder_managed</span>
        
        <span class="collection-badge-name {{ $getSizeClasses()['text'] }}">
            {{ $collectionName ?: __('collection.no_collection') }}
        </span>
    </a>
</div>

{{-- TypeScript integrato per gestione autonoma --}}
<script>
(function() {
    'use strict';

    /**
     * 🎯 Collection Badge Manager - Versione Autonoma
     * Gestisce un singolo badge della collection in modo completamente autonomo
     */
    class AutonomousCollectionBadge {
        constructor(badgeElement) {
            this.badgeElement = badgeElement;
            this.uniqueId = badgeElement.id;
            this.collectionId = badgeElement.dataset.collectionId;
            this.canEdit = badgeElement.dataset.canEdit === 'true';
            this.size = badgeElement.dataset.size;
            this.position = badgeElement.dataset.position;
            
            // Riferimenti agli elementi interni
            this.linkElement = badgeElement.querySelector('.collection-badge-link');
            this.nameElement = badgeElement.querySelector('.collection-badge-name');
            this.iconElement = badgeElement.querySelector('.collection-badge-icon');
            
            this.init();
        }

        init() {
            console.log(`🎯 Autonomous Collection Badge initialized: ${this.uniqueId}`);
            
            // Ascolta eventi globali di cambio collection
            document.addEventListener('collection-changed', this.handleCollectionChange.bind(this));
            document.addEventListener('collection-updated', this.handleCollectionUpdate.bind(this));
            document.addEventListener('user-logout', this.handleUserLogout.bind(this));
            
            // Auto-refresh dei dati dalla API se necessario
            this.startPeriodicUpdate();
            
            // Gestione click con analytics
            this.linkElement?.addEventListener('click', this.handleClick.bind(this));
        }

        /**
         * Gestisce il cambio di collection
         */
        handleCollectionChange(event) {
            const { id, name, can_edit } = event.detail;
            this.updateBadge(id, name, can_edit);
        }

        /**
         * Gestisce l'aggiornamento della collection
         */
        handleCollectionUpdate(event) {
            const { id, name, can_edit } = event.detail;
            if (id == this.collectionId) {
                this.updateBadge(id, name, can_edit);
            }
        }

        /**
         * Aggiorna il badge con nuovi dati
         */
        updateBadge(collectionId, collectionName, canEdit) {
            this.collectionId = collectionId;
            this.canEdit = canEdit;
            
            if (this.nameElement) {
                this.nameElement.textContent = collectionName || '{{ __("collection.no_collection") }}';
            }
            
            if (this.linkElement && collectionId) {
                const baseUrl = canEdit ? '/collections/' + collectionId + '/edit' : '/collections/' + collectionId;
                this.linkElement.href = baseUrl;
                
                const titleKey = canEdit ? '{{ __("collection.edit_collection", ["name" => "COLLECTION_NAME"]) }}' : '{{ __("collection.view_collection", ["name" => "COLLECTION_NAME"]) }}';
                this.linkElement.title = titleKey.replace('COLLECTION_NAME', collectionName);
            }
            
            // Aggiorna visibilità
            if (collectionId && collectionName) {
                this.show();
            } else {
                this.hide();
            }
            
            // Animazione di aggiornamento
            this.animateUpdate();
        }

        /**
         * Mostra il badge
         */
        show() {
            this.badgeElement.classList.remove('hidden');
            this.badgeElement.style.opacity = '1';
        }

        /**
         * Nasconde il badge
         */
        hide() {
            if (!{{ $showWhenEmpty ? 'true' : 'false' }}) {
                this.badgeElement.classList.add('hidden');
            }
        }

        /**
         * Animazione di aggiornamento
         */
        animateUpdate() {
            this.linkElement?.classList.add('ring-2', 'ring-sky-400', 'ring-opacity-50');
            setTimeout(() => {
                this.linkElement?.classList.remove('ring-2', 'ring-sky-400', 'ring-opacity-50');
            }, 1000);
        }

        /**
         * Gestisce il click sul badge
         */
        handleClick(event) {
            // Analytics o tracking se necessario
            console.log(`🎯 Collection badge clicked: ${this.collectionId}, canEdit: ${this.canEdit}`);
            
            // Dispatch evento personalizzato
            document.dispatchEvent(new CustomEvent('collection-badge-clicked', {
                detail: {
                    badgeId: this.uniqueId,
                    collectionId: this.collectionId,
                    canEdit: this.canEdit,
                    size: this.size,
                    position: this.position
                }
            }));
        }

        /**
         * Aggiornamento periodico dei dati
         */
        async startPeriodicUpdate() {
            // Aggiorna ogni 2 minuti per sincronizzare con eventuali cambi
            setInterval(async () => {
                try {
                    const response = await fetch('/api/user/current-collection');
                    if (response.ok) {
                        const data = await response.json();
                        if (data.collection) {
                            this.updateBadge(data.collection.id, data.collection.name, data.collection.can_edit);
                        }
                    }
                } catch (error) {
                    console.warn('🚨 Failed to refresh collection data:', error);
                }
            }, 120000); // 2 minuti
        }

        /**
         * Gestisce il logout
         */
        handleUserLogout() {
            this.updateBadge(null, null, false);
        }

        /**
         * Cleanup del badge
         */
        destroy() {
            document.removeEventListener('collection-changed', this.handleCollectionChange);
            document.removeEventListener('collection-updated', this.handleCollectionUpdate);
            document.removeEventListener('user-logout', this.handleUserLogout);
        }
    }

    // Auto-inizializzazione quando il DOM è pronto
    document.addEventListener('DOMContentLoaded', function() {
        const badgeElement = document.getElementById('{{ $uniqueId }}');
        if (badgeElement) {
            // Crea l'istanza del badge autonomo
            const autonomousBadge = new AutonomousCollectionBadge(badgeElement);
            
            // Salva l'istanza per eventuali cleanup
            badgeElement._autonomousBadge = autonomousBadge;
        }
    });

    // Cleanup globale su beforeunload
    window.addEventListener('beforeunload', function() {
        const badgeElement = document.getElementById('{{ $uniqueId }}');
        if (badgeElement && badgeElement._autonomousBadge) {
            badgeElement._autonomousBadge.destroy();
        }
    });

})();
</script>
@endif
