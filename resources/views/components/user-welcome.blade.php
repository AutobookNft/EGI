@php
$user = App\Helpers\FegiAuth::user();
@endphp

@if ($user)
<div class="flex items-center ml-4">
    {{-- Versione Desktop --}}
    <span class="hidden text-sm font-medium text-emerald-400 sm:inline">
        {{ App\Helpers\FegiAuth::getWelcomeMessage() }}
    </span>
    
    {{-- Versione Mobile --}}
    <span class="text-xs font-medium text-emerald-500 sm:hidden">
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
@endif
