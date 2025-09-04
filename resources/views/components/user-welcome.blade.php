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
        
        // Tronca solo il nick_name se è troppo lungo (max 7 caratteri + ...)
        if ($user && $user->nick_name && strlen($user->nick_name) > 7) {
            $truncatedNickName = substr($user->nick_name, 0, 7) . '...';
            $mobileMessage = str_replace($user->nick_name, $truncatedNickName, $welcomeMessage);
        } else {
            $mobileMessage = $welcomeMessage;
        }
        @endphp
        {{ $mobileMessage }}
    </span>
</div>
@endif
