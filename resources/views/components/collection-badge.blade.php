{{--
📜 Collection Badge Component Template
Componente autonomo per il badge della collection con TypeScript integrato
--}}

<div id="{{ $uniqueId }}"
    class="collection-badge-component items-center {{ $responsiveClasses }} {{ $getPositionClasses() }} {{ $getSizeClasses()['container'] }}"
    data-collection-id="{{ $collectionId }}" data-can-edit="{{ $canEdit ? 'true' : 'false' }}" data-size="{{ $size }}"
    data-position="{{ $position }}" data-egi-count="{{ $egiCount }}">

    <a href="{{ $getBadgeUrl() }}"
        class="collection-badge-link flex items-center transition border rounded-lg border-sky-700 bg-sky-900/60 text-sky-300 hover:border-sky-600 hover:bg-sky-800 {{ $getSizeClasses()['container'] }}"
        title="{{ $getBadgeTitle() }}">

        <span class="collection-badge-icon material-symbols-outlined {{ $getSizeClasses()['icon'] }}"
            aria-hidden="true">folder_managed</span>

        <span class="collection-badge-name {{ $getSizeClasses()['text'] }}">
            @if($collectionName)
            {{ $collectionName }}
            <span
                class="collection-badge-count ml-1 px-1.5 py-0.5 text-xs bg-sky-700/60 text-sky-200 rounded-full border border-sky-600">
                {{ $egiCount }}
            </span>
            @else
            {{ __('collection.no_collection') }}
            @endif
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
            
            // Memorizza il conteggio EGI corrente per rilevare i cambiamenti
            this.egiCount = parseInt(badgeElement.dataset.egiCount) || 0;

            // Riferimenti agli elementi interni
            this.linkElement = badgeElement.querySelector('.collection-badge-link');
            this.nameElement = badgeElement.querySelector('.collection-badge-name');
            this.countElement = badgeElement.querySelector('.collection-badge-count');
            this.iconElement = badgeElement.querySelector('.collection-badge-icon');

            console.log('🔧 [CollectionBadge] Constructor completed:', {
                uniqueId: this.uniqueId,
                collectionId: this.collectionId,
                currentEgiCount: this.egiCount,
                size: this.size,
                position: this.position
            });

            this.init();
        }

        init() {
            console.log(`🎯 [CollectionBadge] Autonomous Collection Badge initialized: ${this.uniqueId}`);

            // Ascolta eventi globali di cambio collection
            document.addEventListener('collection-changed', this.handleCollectionChange.bind(this));
            document.addEventListener('collection-updated', this.handleCollectionUpdate.bind(this));
            document.addEventListener('user-logout', this.handleUserLogout.bind(this));

            // Auto-refresh dei dati dalla API se necessario
            console.log(`⏰ [CollectionBadge] Starting periodic updates every 5 seconds for badge: ${this.uniqueId}`);
            this.startPeriodicUpdate();

            // Gestione click con analytics
            this.linkElement?.addEventListener('click', this.handleClick.bind(this));
        }

        /**
         * Gestisce il cambio di collection
         */
        handleCollectionChange(event) {
            const { id, name, can_edit, egi_count } = event.detail;
            this.updateBadge(id, name, can_edit, egi_count);
        }

        /**
         * Gestisce l'aggiornamento della collection
         */
        handleCollectionUpdate(event) {
            const { id, name, can_edit, egi_count } = event.detail;
            if (id == this.collectionId) {
                this.updateBadge(id, name, can_edit, egi_count);
            }
        }

        /**
         * Aggiorna il badge con nuovi dati
         */
        updateBadge(collectionId, collectionName, canEdit, egiCount = 0) {
            console.log('🔄 [CollectionBadge] updateBadge called:', {
                collectionId,
                collectionName,
                canEdit,
                egiCount,
                previousCount: this.egiCount
            });
            
            this.collectionId = collectionId;
            this.canEdit = canEdit;
            this.egiCount = egiCount; // Aggiorna il conteggio corrente

            if (this.nameElement && collectionName) {
                // Aggiorna il nome
                const nameTextNode = this.nameElement.childNodes[0];
                if (nameTextNode && nameTextNode.nodeType === Node.TEXT_NODE) {
                    nameTextNode.textContent = collectionName + ' ';
                } else {
                    // Se non c'è un text node, ricostruisce il contenuto
                    this.nameElement.innerHTML = collectionName + ' <span class="collection-badge-count ml-1 px-1.5 py-0.5 text-xs bg-sky-700/60 text-sky-200 rounded-full border border-sky-600">' + egiCount + '</span>';
                }

                // Aggiorna il contatore
                const countElement = this.nameElement.querySelector('.collection-badge-count');
                if (countElement) {
                    countElement.textContent = egiCount;
                }
            } else if (this.nameElement) {
                this.nameElement.textContent = '{{ __("collection.no_collection") }}';
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
         * Animazione pulso e brillamento quando il conteggio cambia
         */
        pulseAndShine() {
            console.log('✨ [CollectionBadge] Triggering pulse and shine animation');
            
            if (!this.linkElement) return;
            
            // Effetto pulso
            this.linkElement.style.transform = 'scale(1.05)';
            this.linkElement.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
            
            // Effetto brillamento
            this.linkElement.style.boxShadow = '0 0 20px rgba(14, 165, 233, 0.6), 0 0 40px rgba(14, 165, 233, 0.3)';
            this.linkElement.style.borderColor = 'rgba(14, 165, 233, 0.8)';
            
            // Ring effect
            this.linkElement.classList.add('ring-4', 'ring-sky-400', 'ring-opacity-75');
            
            setTimeout(() => {
                // Ripristina lo stato normale
                this.linkElement.style.transform = 'scale(1)';
                this.linkElement.style.boxShadow = '';
                this.linkElement.style.borderColor = '';
                this.linkElement.classList.remove('ring-4', 'ring-sky-400', 'ring-opacity-75');
            }, 800);
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
            // Aggiorna ogni 5 secondi per sincronizzare rapidamente con eventuali cambi di conteggio EGI
            setInterval(async () => {
                try {
                    console.log('🔄 [CollectionBadge] Fetching collection data...', {
                        badgeId: this.uniqueId,
                        endpoint: '/user/preferences/current-collection'
                    });
                    
                    const response = await fetch('/user/preferences/current-collection', {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    
                    console.log('📡 [CollectionBadge] Response received:', {
                        status: response.status,
                        statusText: response.statusText,
                        ok: response.ok
                    });
                    
                    if (response.ok) {
                        const data = await response.json();
                        console.log('📦 [CollectionBadge] Data received:', data);
                        
                        if (data.success && data.data) {
                            const oldCount = this.egiCount;
                            const newCount = data.data.egi_count || 0;
                            
                            console.log('🔢 [CollectionBadge] Count comparison:', {
                                oldCount,
                                newCount,
                                changed: oldCount !== newCount
                            });
                            
                            this.updateBadge(
                                data.data.collection_id || null,
                                data.data.collection_name || null,
                                data.data.can_edit || false,
                                newCount
                            );
                            
                            // Se il conteggio è cambiato, attiva l'animazione
                            if (oldCount !== newCount) {
                                this.pulseAndShine();
                            }
                        }
                    } else {
                        console.error('❌ [CollectionBadge] Failed to fetch data:', response.status, response.statusText);
                    }
                } catch (error) {
                    console.error('🚨 [CollectionBadge] Failed to refresh collection data:', error);
                }
            }, 5000); // 5 secondi per aggiornamenti frequenti del conteggio EGI
        }

        /**
         * Gestisce il logout
         */
        handleUserLogout() {
            this.updateBadge(null, null, false, 0);
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
