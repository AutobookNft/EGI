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
    public function getIcon(string $name, string $style = null, ?string $customClass = null): ?string
    {
        // Cancella la cache prima di recuperare l'icona (se necessario)
        $this->clearCache($name, $style, $customClass);

        // Costruisce la chiave della cache
        $cacheKey = $this->buildCacheKey($name, $style, $customClass);

        // Controlla se l'elemento è già in cache
        return Cache::tags(['icons'])->remember($cacheKey, 3600, function () use ($name, $style, $customClass) {

            $query_icon = Icon::where('name', $name)->where('style', "=",$style)->first();
            Log::channel('florenceegi')->info("IconRepository: query_icon: $query_icon");

            if (!$query_icon) {
                Log::warning("Icona non trovata: $name ($style)");
                return 'fallback';
            }

            $finalClass = $customClass ?? $query_icon->class;
            return str_replace('%class%', $finalClass, $query_icon->html);
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
            // Usa Cache::tags() solo se Redis è attivo, altrimenti pulisce tutta la cache
            if (config('cache.default') === 'redis') {
                Cache::tags(['icons'])->flush();
            } else {
                Cache::flush();
            }
        }
    }

    /**
     * Precarica tutte le icone in cache.
     */
    public function preloadIcons(): void
    {
        Icon::all()->each(function ($icon) {
            $cacheKey = $this->buildCacheKey($icon->name, $icon->style, $icon->customClass);
            Cache::tags(['icons'])->put($cacheKey, $icon->html, 3600);
        });
    }

    /**
     * Costruisce la chiave della cache per un'icona.
     *
     * @param string $name  Nome dell'icona.
     * @param string $style Stile dell'icona.
     * @param string|null $customClass Classe personalizzata (opzionale).
     * @return string Chiave della cache.
     */
    protected function buildCacheKey(string $name, ?string $style = 'elegant', ?string $customClass = null): string
    {
        // Usa un hash per evitare chiavi troppo lunghe
        $rawKey = "icon:{$style}:{$name}:{$customClass}";
        return 'icon:' . md5($rawKey);
    }
}
