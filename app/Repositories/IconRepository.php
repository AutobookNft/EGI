<?php

namespace App\Repositories;

use App\Models\Icon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class IconRepository
{
    /**
     * Recupera un'icona specifica dal database o dalla cache.
     *
     * @param string $name  Nome dell'icona.
     * @param string $style Stile dell'icona.
     * @param string|null $customClass Classe personalizzata (opzionale).
     * @return string|null  Contenuto HTML dell'icona.
     */
    public function getIcon(string $name, string $style, ?string $customClass = null): ?string
    {

        $this->clearCache($name, $style, $customClass);

        // Log::channel('florenceegi')->info( 'Class IconRepository. Method: getIcon. Action: customClass: (' . $customClass .  ') name: (' . $name .')');

        // Costruisce la chiave della cache
        $cacheKey = $this->buildCacheKey($name, $style, $customClass);
        // Log::channel('florenceegi')->info('Cache Key Generated', ['key' => $cacheKey, 'name' => $name, 'style' => $style, 'customClass' => $customClass]);

        // Controlla se l'elemento è già in cache
        $cachedValue = Cache::get($cacheKey);
        if ($cachedValue) {
            // Log::channel('florenceegi')->info('Cache Hit', ['key' => $cacheKey, 'cachedValue' => $cachedValue]);

            // Sostituisce il segnaposto %class% con la classe personalizzata o quella di default
            $finalClass = $customClass ?? 'default-class';
            $processedValue = str_replace('%class%', $finalClass, $cachedValue);
            // Log::channel('florenceegi')->info('Processed Cached Value', ['processedValue' => $processedValue, 'finalClass' => $finalClass]);

            return $processedValue;
        }

        // Cache miss: esegue la closure per calcolare il valore
        // Log::channel('florenceegi')->info('Class IconRepository. Method: getIcon. Action: Icon name', ['name' => $name]);

        return Cache::remember($cacheKey, 3600, function () use ($name, $style, $customClass) {
            // Log::channel('florenceegi')->info('Querying Database', ['name' => $name, 'style' => $style]);

            $query_icon = Icon::where('name', $name)->where('style', $style)->first();

            if (!$query_icon) {
                Log::channel('florenceegi')->warning('Class IconRepository. Method: getIcon. Action: Icon Not Found', ['name' => $name, 'style' => $style]);
                return 'fallback'; // Puoi specificare un'icona di fallback
            }

            if ($customClass) {
                $finalClass = $customClass;
            } else {
                $finalClass = $query_icon->class;
            }

            // Log::channel('florenceegi')->info('Class IconRepository. Method: getIcon. Action: Processed', ['$finalClass' => $finalClass]);
            $processedHtml = str_replace('%class%', $finalClass, $query_icon->html);

            return $processedHtml;
        });
    }

    /**
     * Recupera un'icona con lo stile predefinito.
     *
     * @param string $name Nome dell'icona.
     * @return string      Contenuto HTML dell'icona (o fallback).
     */
    public function getDefaultIcon(string $name): string
    {
        $defaultStyle = config('icons.default');

        // Determina lo stile dell'utente autenticato
        if (Auth::check()) {
            $defaultStyle = Auth::user()->icon_style ?? $defaultStyle;
        }

        return $this->getIcon($name, $defaultStyle) ?? $this->getFallbackIcon();
    }


    /**
     * Recupera un'icona di fallback se quella richiesta non esiste.
     *
     * @return string Contenuto HTML dell'icona di fallback.
     */
    protected function getFallbackIcon(): string
    {
        return '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m2 0a2 2 0 00-2-2H9a2 2 0 00-2 2m8 0a2 2 0 01-2 2H9a2 2 0 01-2-2m6 0H9"></path>
                </svg>';
    }

    /**
     * Rimuove un'icona dalla cache.
     *
     * @param string|null $name  Nome dell'icona (opzionale).
     * @param string|null $style Stile dell'icona (opzionale).
     */
    public function clearCache(?string $name = null, ?string $style = null, ?string $customClass): void
    {
        if ($name && $style) {
            $cacheKey = $this->buildCacheKey($name, $style, $customClass);
            Cache::forget($cacheKey);
        } else {
            // Elimina tutte le icone dalla cache
            Cache::tags(['icons'])->flush();
        }
    }

    /**
     * Precarica tutte le icone in cache.
     */
    public function preloadIcons(): void
    {
        Icon::all()->each(function ($icon) {
            $cacheKey = $this->buildCacheKey($icon->name, $icon->style, $icon->customClass);
            Cache::put($cacheKey, $icon->html, 3600);
        });
    }

    /**
     * Costruisce la chiave della cache per un'icona.
     *
     * @param string $name  Nome dell'icona.
     * @param string $style Stile dell'icona.
     * @return string Chiave della cache.
     */
    protected function buildCacheKey(string $name, string $style, ?string $customClass = null): string
    {
        // Usa un hash per evitare che la chiave sia troppo lunga
        $rawKey = "icon:{$style}:{$name}:{$customClass}";
        return 'icon:' . md5($rawKey);
    }

}
