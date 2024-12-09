<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;

class LanguageSelector extends Component
{
    public $currentLocale;
    public $languages;

    public function mount()
    {
        $this->languages = config('app.languages');

        // Segue la stessa logica del middleware SetLanguage
        if (Auth::check() && Auth::user()->language) {
            // Log::channel('florenceegi')->info('language from db');
            $this->currentLocale = trim(Auth::user()->language);
        } elseif (Cookie::has('language')) {
            // Log::channel('florenceegi')->info('language from cookie');
            $this->currentLocale = trim(Cookie::get('language'));
        } else {
            // Log::channel('florenceegi')->info('language from config');
            $this->currentLocale = trim(config('app.locale'));
        }
        // App::setLocale($this->currentLocale);
        // session(['language' => $this->currentLocale]);

        session(['language' => App::getLocale()]);
        // Log::channel('florenceegi')->info('LINGUA CORRENTE: '.App::currentLocale());
        // Log::channel('florenceegi')->info('LINGUA CORRENTE: '.App::getLocale());

    }

    public function updatedCurrentLocale($value)
    {
        Log::channel('florenceegi')->info('Attempting to update locale to: ' . $value);

        if (array_key_exists($value, $this->languages)) {
            $value = trim($value);
            // Log::channel('florenceegi')->info('Locale updated to: ' . $value);

            // Aggiorna il database se l'utente è autenticato
            if (Auth::check()) {
                /** @var User $user */
                $user = Auth::user();
                $user->language = $value;
                $user->save();
                // Log::channel('florenceegi')->info('User language updated in database: ' . $value);
            } else {
                Log::channel('florenceegi')->info('User is not authenticated, setting cookie for language: ' . $value);
            }

            // Imposta il cookie per gli utenti non autenticati
            Cookie::queue('language', $value, 60 * 24 * 365); // Cookie valido per un anno

            // Aggiorna la sessione e l'applicazione
            session(['language' => $value]);
            App::setLocale($value);

            Log::channel('florenceegi')->info('Language changed to: ' . App::currentLocale());

            // Ricarica la pagina per applicare la nuova lingua
            $this->dispatch('language-changed')->self();
            return redirect(request()->header('Referer'));
        } else {
            Log::channel('florenceegi')->warning('Invalid language value provided: ' . $value);
        }
    }

    public function render()
    {
        return view('livewire.language-selector');
    }
}
