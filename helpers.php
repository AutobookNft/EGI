<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

if (!function_exists('livewire_tmp_path')) {
    function livewire_tmp_path() {
        $disk = config('livewire.temporary_file_upload.disk', 'local');
        $directory = config('livewire.temporary_file_upload.directory', 'livewire-tmp');

        if (is_null($directory)) {
            $directory = 'livewire-tmp';
        }

        return Storage::disk($disk)->path($directory);
    }
}

if (!function_exists('getDynamicBucketUrl')) {
    /**
     * Determina dinamicamente l'URL del bucket tra Digital Ocean e CDN.
     *
     * @return string
     */
    function getDynamicBucketUrl(): string {
        $doUrl = config('paths.hosting.Digital_Ocean.url');
        $cdnUrl = config('paths.hosting.CDN.url');

        // Controlla la disponibilità di Digital Ocean
        if (checkUrlAvailability($doUrl)) {
            Log::info("Utilizzo di Digital Ocean: {$doUrl}");
            return $doUrl;
        }

        // Controlla la disponibilità della CDN
        if (checkUrlAvailability($cdnUrl)) {
            Log::info("Utilizzo della CDN: {$cdnUrl}");
            return $cdnUrl;
        }

        // Fallback su un valore di default
        $defaultUrl = '/storage/';
        Log::warning("Nessun servizio disponibile, uso il disco locale: {$defaultUrl}");
        return $defaultUrl;
    }

    /**
     * Verifica se un URL è disponibile.
     *
     * @param string $url
     * @return bool
     */
    function checkUrlAvailability(string $url): bool {
        try {
            $response = Http::head($url);
            return $response->successful();
        } catch (\Exception $e) {
            Log::error("Errore nella verifica dell'URL: {$url}", ['error' => $e->getMessage()]);
            return false;
        }
    }
}

if (!function_exists('hasPendingWallet')) {
    /**
     * Verifica se esiste un wallet pending per il Creator.
     *
     * @param int $proposerId
     * @return bool
     */
    function hasPendingWallet(int $proposerId): bool {

        Log::channel('florenceegi')->info('hasPendingWallet: Verifica wallet pending', [
            'proposerId' => $proposerId
        ]);

        $payload = \App\Models\NotificationPayloadWallet::where('proposer_id', $proposerId)
            ->where('status', 'LIKE', '%pending%')
            ->exists();

        Log::channel('florenceegi')->info('hasPendingWallet: Risultato verifica wallet pending', [
            'payload' => $payload
        ]);

        // Supponiamo di usare il modello NotificationPayloadWallet
        // e che la colonna 'status' contenga il valore 'pending' per i wallet in attesa.
        return $payload;
    }
}

if (!function_exists('formatActivatorDisplay')) {
    /**
     * Format activator display based on user permissions
     * 
     * @param \App\Models\User $user
     * @return array ['name' => string, 'avatar' => string|null, 'is_commissioner' => bool]
     */
    function formatActivatorDisplay($user) {
        $isCommissioner = $user && $user->can('display_public_name_on_egi') && $user->can('display_public_avatar_on_egi');

        if ($isCommissioner) {
            // Commissioner: show real name and avatar
            $name = ($user->first_name && $user->last_name)
                ? $user->first_name . ' ' . $user->last_name
                : ($user->name ?? 'N/A');

            // Get avatar using User model's profile_photo_url attribute
            $avatar = null;
            try {
                // Use the User model's profile_photo_url attribute which handles all the logic
                $profileUrl = $user->profile_photo_url;

                // Only use if it's not the default DiceBear avatar
                if ($profileUrl && !str_contains($profileUrl, 'dicebear.com')) {
                    $avatar = $profileUrl;
                }
            } catch (\Exception $e) {
                // Log error but continue without avatar
                \Log::warning('Failed to get user avatar', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
                $avatar = null;
            }

            return [
                'name' => $name,
                'avatar' => $avatar,
                'is_commissioner' => true,
                'wallet_abbreviated' => null
            ];
        } else {
            // Regular collector: show abbreviated wallet address
            $walletAddress = $user->wallet ?? '';
            $abbreviated = strlen($walletAddress) >= 10
                ? substr($walletAddress, 0, 6) . '...' . substr($walletAddress, -4)
                : $walletAddress;

            return [
                'name' => $abbreviated,
                'avatar' => null,
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

if (!function_exists('getActivatorsCount')) {
    /**
     * Get total count of activators (collectors + commissioners)
     * 
     * @return int
     */
    function getActivatorsCount(): int {
        return \App\Models\User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['collector', 'commissioner']);
        })->count();
    }
}

if (!function_exists('getCreatorsCount')) {
    /**
     * Get total count of creators
     * 
     * @return int
     */
    function getCreatorsCount(): int {
        return \App\Models\User::whereHas('roles', function ($query) {
            $query->where('name', 'creator');
        })->orWhere('usertype', 'creator')->count();
    }
}

if (!function_exists('getCollectionsCount')) {
    /**
     * Get total count of published collections
     * 
     * @return int
     */
    function getCollectionsCount(): int {
        return \App\Models\Collection::where('is_published', true)->count();
    }
}
