<?php

if (!function_exists('formatActivatorDisplay')) {
    /**
     * Format activator display based on user permissions
     *
     * @param \App\Models\User $user
     * @return array ['name' => string, 'avatar' => string|null, 'is_commissioner' => bool]
     */
    function formatActivatorDisplay($user) {
        // Usa la nuova logica basata su usertype
        $isCommissioner = $user && $user->usertype === 'commissioner';

        if ($isCommissioner) {
            // Commissioner: show real name and real avatar (if uploaded) or generated
            $name = ($user->first_name && $user->last_name)
                ? $user->first_name . ' ' . $user->last_name
                : ($user->name ?? 'Commissioner');

            // Usa sempre profile_photo_url che ora gestisce automaticamente la privacy
            $avatar = null;
            try {
                $avatar = $user->profile_photo_url; // Ora include anche DiceBear
            } catch (\Exception $e) {
                $avatar = $user->defaultProfilePhotoUrl(); // Fallback
            }

            return [
                'name' => $name,
                'avatar' => $avatar,
                'is_commissioner' => true,
                'wallet_abbreviated' => null
            ];
        } else {
            // Non-commissioner: mostra wallet troncato + avatar generato
            $walletAddress = $user->wallet ?? '';
            $abbreviated = strlen($walletAddress) >= 10
                ? substr($walletAddress, 0, 6) . '...' . substr($walletAddress, -4)
                : ($walletAddress ?: 'Utente Anonimo');

            // Usa sempre profile_photo_url che ora restituisce avatar generato per non-commissioner
            $avatar = null;
            try {
                $avatar = $user->profile_photo_url; // Ora sempre presente
            } catch (\Exception $e) {
                $avatar = $user->defaultProfilePhotoUrl(); // Fallback
            }

            return [
                'name' => $abbreviated,
                'avatar' => $avatar, // Ora include l'avatar generato
                'is_commissioner' => false,
                'wallet_abbreviated' => $abbreviated
            ];
        }
    }
}

if (!function_exists('getGenericActivatorIcon')) {
    /**
     * Get generic activator icon SVG
     *
     * @param string $classes
     * @return string
     */
    function getGenericActivatorIcon($classes = 'w-4 h-4') {
        return '<svg class="' . $classes . ' text-gray-400" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd" />
        </svg>';
    }
}
