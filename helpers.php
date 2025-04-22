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
    function getDynamicBucketUrl(): string
    {
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
    function checkUrlAvailability(string $url): bool
    {
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
    function hasPendingWallet(int $proposerId): bool
    {

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

