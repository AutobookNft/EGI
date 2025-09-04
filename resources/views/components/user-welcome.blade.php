@php
$user = App\Helpers\FegiAuth::user();
@endphp

@if ($user)
<div class="flex items-center ml-4" id="user-welcome-component">
    {{-- Versione Desktop --}}
    <span class="hidden text-sm font-medium text-emerald-400 sm:inline" id="welcome-message-desktop">
        {{ App\Helpers\FegiAuth::getWelcomeMessage() }}
    </span>
    
    {{-- Versione Mobile --}}
    <span class="text-xs font-medium text-emerald-500 sm:hidden" id="welcome-message-mobile">
        @php
        $welcomeMessage = App\Helpers\FegiAuth::getWelcomeMessage();
        $user = App\Helpers\FegiAuth::user();
        $userName = App\Helpers\FegiAuth::getUserName();
        
        // Tronca solo il userName (nick_name) se è troppo lungo (max 7 caratteri + ...)
        if ($userName && strlen($userName) > 7) {
            $truncatedUserName = substr($userName, 0, 7) . '...';
            // Sostituisci solo l'username nel messaggio, preservando il resto
            $mobileMessage = str_replace($userName, $truncatedUserName, $welcomeMessage);
        } else {
            $mobileMessage = $welcomeMessage;
        }
        @endphp
        {{ $mobileMessage }}
    </span>
</div>

{{-- JavaScript per aggiornamento dinamico con Laravel Echo --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Verifica se Laravel Echo è disponibile
    if (typeof window.Echo !== 'undefined') {
        const userId = {{ auth()->id() ?? 'null' }};
        
        if (userId) {
            console.log('🎧 Setting up Laravel Echo listener for user:', userId);
            
            // Ascolta l'evento broadcast per l'aggiornamento del messaggio di benvenuto
            window.Echo.private(`user-welcome.${userId}`)
                .listen('.welcome.updated', (data) => {
                    console.log('🔄 Received broadcast update:', data);
                    
                    // Aggiorna versione desktop
                    const desktopElement = document.getElementById('welcome-message-desktop');
                    if (desktopElement && data.welcome_message) {
                        desktopElement.textContent = data.welcome_message;
                        console.log('✅ Desktop welcome updated via broadcast:', data.welcome_message);
                    }
                    
                    // Aggiorna versione mobile con troncamento del nick_name
                    const mobileElement = document.getElementById('welcome-message-mobile');
                    if (mobileElement && data.welcome_message && data.user_name) {
                        let mobileMessage = data.welcome_message;
                        
                        // Applica la logica di troncamento solo al nick_name
                        if (data.user_name.length > 7) {
                            const truncatedUserName = data.user_name.substring(0, 7) + '...';
                            mobileMessage = mobileMessage.replace(data.user_name, truncatedUserName);
                        }
                        
                        mobileElement.textContent = mobileMessage;
                        console.log('✅ Mobile welcome updated via broadcast:', mobileMessage);
                    }
                    
                    // Animazione di feedback per l'aggiornamento
                    [desktopElement, mobileElement].forEach(el => {
                        if (el) {
                            el.style.transition = 'all 0.3s ease';
                            el.style.transform = 'scale(1.05)';
                            el.style.color = '#10b981';
                            
                            setTimeout(() => {
                                el.style.transform = 'scale(1)';
                                el.style.color = '';
                            }, 300);
                        }
                    });
                    
                    console.log('✅ User welcome updated successfully via Laravel Echo broadcast');
                });
        }
    } else {
        console.log('⚠️ Laravel Echo not available, falling back to manual event listener');
        
        // Fallback: Ascolta l'evento personalizzato locale (per compatibilità)
        document.addEventListener('userWelcomeUpdate', async function(event) {
            console.log('🔄 Fallback: User welcome update triggered');
            
            // Attendi che il database sia aggiornato prima di chiamare l'API
            await new Promise(resolve => setTimeout(resolve, 1000));
            
            try {
                console.log('📡 Fetching updated welcome message from API...');
                
                const response = await fetch('/api/user/welcome-message', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    console.log('📨 API Response:', data);
                    
                    if (data.success) {
                        // Aggiorna versione desktop
                        const desktopElement = document.getElementById('welcome-message-desktop');
                        if (desktopElement) {
                            desktopElement.textContent = data.welcome_message;
                            console.log('✅ Desktop welcome updated:', data.welcome_message);
                        }
                        
                        // Aggiorna versione mobile con troncamento del nick_name
                        const mobileElement = document.getElementById('welcome-message-mobile');
                        if (mobileElement) {
                            let mobileMessage = data.welcome_message;
                            const userName = data.user_name;
                            
                            if (userName && userName.length > 7) {
                                const truncatedUserName = userName.substring(0, 7) + '...';
                                mobileMessage = mobileMessage.replace(userName, truncatedUserName);
                            }
                            
                            mobileElement.textContent = mobileMessage;
                            console.log('✅ Mobile welcome updated:', mobileMessage);
                        }
                        
                        console.log('✅ User welcome updated successfully with backend logic');
                    }
                }
            } catch (error) {
                console.error('❌ Error updating user welcome:', error);
            }
        });
    }
});
</script>
@endif
