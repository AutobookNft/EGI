<div class="min-h-screen bg-gradient-to-br from-gray-900 via-purple-900 to-blue-900">
    <div class="container px-4 py-8 mx-auto">
        <!-- Notification Center Header -->
        <div class="mb-8 text-center">
            <h1 class="mb-2 text-4xl font-bold text-white font-display">
                {{ __('Notification Center') }}
            </h1>
            <p class="text-lg text-gray-300">
                {{ __('Stay updated with your latest activities') }}
            </p>
        </div>

        <!-- Main Notification Container -->
        <div class="p-6 text-white border shadow-2xl bg-white/10 backdrop-blur-lg border-white/20 rounded-3xl"
             x-data="{
                 loading: false,
                 selectedNotification: null,
                 loadingTimeout: null
             }"
             x-init="
                 // Initialize loading states
                 $wire.on('notification-loading', () => {
                     loading = true;
                     loadingTimeout = setTimeout(() => loading = false, 3000);
                 });
                 $wire.on('notification-loaded', () => {
                     loading = false;
                     if(loadingTimeout) clearTimeout(loadingTimeout);
                 });
             ">

            <!-- Loading Overlay -->
            <div x-show="loading"
                 x-transition:enter="transition-opacity ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
                <div class="p-8 text-center border bg-white/10 backdrop-blur-lg border-white/20 rounded-2xl">
                    <div class="w-12 h-12 mx-auto mb-4 border-4 border-purple-500 rounded-full border-t-transparent animate-spin"></div>
                    <p class="font-medium text-white">{{ __('Loading notification...') }}</p>
                </div>
            </div>
            @php
                use App\Repositories\IconRepository;
            @endphp
            <script>
                console.log('Notification Center loaded...');
                
                // TEST IMMEDIATO: verifica che i bottoni esistano
                setTimeout(() => {
                    const testButtons = document.querySelectorAll('.reservation-archive-btn');
                    console.log('🔍 TEST: Bottoni trovati dopo 1 secondo:', testButtons.length);
                    
                    testButtons.forEach((btn, i) => {
                        console.log(`🔍 Bottone ${i+1}:`, {
                            element: btn,
                            notificationId: btn.dataset.notificationId,
                            action: btn.dataset.action,
                            classList: btn.classList.toString()
                        });
                    });
                }, 1000);
            </script>


            <!-- Notification Thumbnails Section -->
                        <!-- Notification Thumbnails Section -->
            <div id="head-notifications-container"
                 class="mb-8"
                 x-data="{ isVisible: false }"
                 x-init="setTimeout(() => isVisible = true, 100)"
                 x-show="isVisible"
                 x-transition:enter="transition-all ease-out duration-500"
                 x-transition:enter-start="opacity-0 transform translate-y-4"
                 x-transition:enter-end="opacity-100 transform translate-y-0">

                <div class="flex items-center justify-between mb-4">
                    <h2 class="flex items-center text-xl font-semibold text-white">
                        <svg class="w-6 h-6 mr-2 text-purple-400 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5-5-5h5v-12h0z"></path>
                        </svg>
                        {{ __('Pending Notifications') }}
                    </h2>
                    @if(count($pendingNotifications) > 0)
                        <span class="px-3 py-1 text-sm font-medium text-white rounded-full bg-gradient-to-r from-purple-600 to-blue-600 animate-bounce">
                            {{ count($pendingNotifications) }} {{ __('pending') }}
                        </span>
                    @endif
                </div>

                <!-- Thumbnails Grid/List with staggered animation -->
                <div x-data="{ showThumbnails: false }"
                     x-init="setTimeout(() => showThumbnails = true, 200)"
                     x-show="showThumbnails"
                     x-transition:enter="transition-all ease-out duration-700"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100">
                    @include('livewire.partials.head-thumbnails-list')
                </div>
            </div>

            <!-- Notification Details Section -->
            <div id="notification-details" class="p-8 mb-6 transition-all duration-300 border bg-white/5 backdrop-blur-sm border-white/10 rounded-2xl hover:bg-white/10 hover:border-white/20">
                @php
                    if (count($pendingNotifications) > 0) {
                        $text = __('notification.select_notification');
                        $icon = '<svg class="w-8 h-8 mx-auto mb-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path></svg>';
                    } else {
                        $text = __('notification.no_notifications');
                        $icon = '<svg class="w-8 h-8 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>';
                    }
                @endphp

                <div class="text-center">
                    {!! $icon !!}
                    <p class="text-lg font-medium text-gray-300">{{ $text }}</p>
                    @if(count($pendingNotifications) > 0)
                        <p class="mt-2 text-sm text-gray-400">{{ __('Click on a notification above to view details') }}</p>
                    @else
                        <p class="mt-2 text-sm text-gray-400">{{ __('You\'re all caught up! No pending notifications.') }}</p>
                    @endif
                </div>
            </div>

            <!-- History Toggle Section -->
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-white">{{ __('Notification History') }}</h3>
                <button wire:click="toggleHistoricalNotifications"
                        class="flex items-center px-6 py-2 space-x-2 font-medium text-white transition-all duration-300 shadow-lg bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 rounded-xl hover:shadow-purple-500/25">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        @if($showHistoricalNotifications)
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 16.121m6.878-6.243L16.121 3M12 9a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        @endif
                    </svg>
                    <span>
                        {{ $showHistoricalNotifications ? __('notification.hide_processed_notifications') : __('notification.show_processed_notifications') }}
                    </span>
                </button>
            </div>            
            @include('livewire.partials.notification-history')
            <livewire:notifications.wallets.decline-proposal-modal />
        </div>
    </div>
</div>

{{-- Scripts per le notifiche con EVENT DELEGATION --}}
@push('scripts')
<script>
console.log('🔧 DEBUG: Script notifiche caricato con event delegation');

document.addEventListener('DOMContentLoaded', function() {
    console.log('📊 DEBUG: DOM caricato, configurando event delegation...');
    
    // Verifica contenitori notifiche esistenti
    const notificationContainers = document.querySelectorAll('#notification-details, .notification-thumbnail, .notification-item');
    console.log(`🔍 Contenitori notifiche trovati: ${notificationContainers.length}`, notificationContainers);

    // ✅ EVENT DELEGATION: Ascolta click su tutto il documento per bottoni dinamici
    document.addEventListener('click', async function(e) {
        // Verifica se il click è su un bottone di archiviazione
        const archiveButton = e.target.closest('.reservation-archive-btn');
        if (!archiveButton) return;
        
        e.preventDefault();
        e.stopPropagation();
        
        console.log("🎯 CLICK RILEVATO su bottone di archiviazione dinamico!", archiveButton);
        console.log('🎯 Dati bottone:', {
            notificationId: archiveButton.dataset.notificationId,
            action: archiveButton.dataset.action || 'archive',
            elemento: archiveButton
        });
        
        const notificationId = archiveButton.getAttribute('data-notification-id');
        console.log("📬 ID Notifica:", notificationId);
        
        if (!notificationId) {
            console.error("❌ ID notifica mancante!");
            alert('Errore: ID notifica mancante');
            return;
        }
        
        try {
            console.log("📡 Invio richiesta di archiviazione...");
            
            // Ottieni il token CSRF
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            console.log("🔐 Token CSRF:", token ? "✅ Trovato" : "❌ Mancante");
            
            // Disabilita il bottone durante la richiesta
            archiveButton.disabled = true;
            archiveButton.textContent = 'Elaborazione...';
            
            const response = await fetch(`/notifications/reservation/response`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    notificationId: notificationId,
                    action: 'archive',
                    payload: 'reservation'
                })
            });
            
            console.log("📡 Risposta ricevuta:", response.status, response.statusText);
            
            if (response.ok) {
                const result = await response.json();
                console.log("✅ Successo:", result);
                
                // Aggiorna il bottone
                archiveButton.textContent = 'Archiviata!';
                archiveButton.style.opacity = '0.5';
                
                // Trova e rimuovi la notifica dal DOM
                const notificationElement = document.querySelector(`[data-notification-id="${notificationId}"]`);
                if (notificationElement) {
                    console.log("🗑️ Rimozione elemento notifica dal DOM");
                    setTimeout(() => {
                        notificationElement.remove();
                    }, 1500);
                }
                
                // Pulisci anche i dettagli se questa notifica era selezionata
                const detailsContainer = document.getElementById('notification-details');
                if (detailsContainer && detailsContainer.innerHTML.includes(notificationId)) {
                    setTimeout(() => {
                        detailsContainer.innerHTML = '<div class="text-center"><svg class="w-8 h-8 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg><p class="text-lg font-medium text-gray-300">Seleziona una notifica per vedere i dettagli</p></div>';
                    }, 1500);
                }
                
                // Mostra messaggio di successo con SweetAlert2 se disponibile
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Perfetto!',
                        text: 'La notifica è stata archiviata con successo.',
                        timer: 2000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                } else {
                    alert('Notifica archiviata con successo!');
                }
                
            } else {
                const errorData = await response.text();
                console.error("❌ Errore dal server:", errorData);
                
                // Ripristina il bottone
                archiveButton.disabled = false;
                archiveButton.textContent = 'OK, Capito!';
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Errore',
                        text: 'Si è verificato un errore durante l\'archiviazione.',
                        toast: true,
                        position: 'top-end'
                    });
                } else {
                    alert('Errore: ' + errorData);
                }
            }
            
        } catch (error) {
            console.error("❌ Errore nella richiesta:", error);
            
            // Ripristina il bottone
            archiveButton.disabled = false;
            archiveButton.textContent = 'OK, Capito!';
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Errore di connessione',
                    text: 'Impossibile connettersi al server. Riprova più tardi.',
                    toast: true,
                    position: 'top-end'
                });
            } else {
                alert('Errore di rete: ' + error.message);
            }
        }
    });
    
    console.log('✅ Event delegation configurato per bottoni dinamici');
});

// Test immediato: controlla se lo script viene eseguito
console.log('🔧 DEBUG: Script con event delegation caricato');
</script>
@endpush
