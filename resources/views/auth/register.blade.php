{{-- resources/views/auth/register.blade.php --}}
{{-- 📜 Oracode View: User Registration Page (GDPR Compliant) --}}
{{-- Page for new user registration, designed with FlorenceEGI's "Rinascimento" theme. --}}
{{-- Emphasizes clarity, user role selection, and GDPR consent management. --}}

{{-- Questo layout è autonomo, come hai fornito. Non usa x-guest-layout. --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- SEO Oracode 2.0 Compliant -->
    <title>{{ __('register.seo_title') }}</title>
    <meta name="description" content="{{ __('register.seo_description') }}">
    <meta name="keywords" content="{{ __('register.seo_keywords') }}">

    <!-- Open Graph -->
    <meta property="og:title" content="{{ __('register.og_title') }}">
    <meta property="og:description" content="{{ __('register.og_description') }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    {{-- <meta property="og:image" content="{{ asset('images/og_florenceegi_rinascimento.jpg') }}"> --}} {{-- Immagine OG consigliata --}}


    <!-- Schema.org -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebPage",
        "name": "{{ __('register.schema_page_name') }}",
        "description": "{{ __('register.schema_page_description') }}",
        "url": "{{ url()->current() }}",
        "isPartOf": {
            "@type": "WebSite",
            "name": "FlorenceEGI",
            "url": "{{ config('app.url') }}"
        }
    }
    </script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Source+Sans+Pro:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Stili del tema "Rinascimento" (invariati) --}}
    <style>
        :root {
            --oro-fiorentino: #D4A574; /* Oro Fiorentino */
            --verde-rinascita: #2D5016; /* Verde Rinascita Intenso */
            --blu-algoritmo: #1B365D;   /* Blu Algoritmo Profondo */
            --grigio-pietra: #6B6B6B;  /* Grigio Pietra Serena */
            --rosso-urgenza: #C13120;  /* Rosso Urgenza Segnaletica */
        }
        .font-rinascimento { font-family: 'Playfair Display', serif; }
        .font-corpo { font-family: 'Source Sans Pro', sans-serif; }
        .bg-rinascimento-gradient { background: linear-gradient(135deg, rgba(212, 165, 116, 0.1) 0%, rgba(45, 80, 22, 0.05) 50%, rgba(27, 54, 93, 0.1) 100%); }
        .glass-effect { backdrop-filter: blur(10px); background: rgba(255, 255, 255, 0.9); border: 1px solid rgba(212, 165, 116, 0.2); }
        .btn-rinascimento { background: linear-gradient(135deg, var(--oro-fiorentino) 0%, #E6B887 100%); color: white; transition: all 0.3s ease; }
        .btn-rinascimento:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(212, 165, 116, 0.3); }
        .input-rinascimento { border: 2px solid rgba(212, 165, 116, 0.3); border-radius: 8px; transition: all 0.3s ease; }
        .input-rinascimento:focus { border-color: var(--oro-fiorentino); box-shadow: 0 0 0 3px rgba(212, 165, 116, 0.1); outline: none; }
        .consent-card { border: 1px solid rgba(212, 165, 116, 0.2); border-radius: 12px; transition: all 0.3s ease; }
        .consent-card:hover { border-color: var(--oro-fiorentino); box-shadow: 0 4px 15px rgba(212, 165, 116, 0.1); }
        .consent-card.selected { ring: 2px; ring-color: var(--oro-fiorentino); background-color: rgba(212, 165, 116, 0.05); } /* Stile per card selezionata */
        .consent-card.error { ring: 2px; ring-color: var(--rosso-urgenza); } /* Stile per card con errore consenso */
    </style>
</head>

<body class="min-h-screen bg-rinascimento-gradient font-corpo text-grigio-pietra">
    <a href="#main-content" class="px-4 py-2 text-white rounded sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-oro-fiorentino">
        {{ __('register.skip_to_main') }}
    </a>

    <div class="flex items-center justify-center min-h-screen px-4 py-12 sm:px-6 lg:px-8">
        <div class="w-full max-w-2xl space-y-8">

            <header class="text-center" role="banner">
                <div class="flex items-center justify-center w-16 h-16 mx-auto mb-6 rounded-full bg-oro-fiorentino">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                </div>
                <h1 class="mb-4 text-4xl font-bold font-rinascimento sm:text-5xl text-blu-algoritmo">
                    {!! __('register.main_title_html') !!}
                </h1>
                <p class="max-w-lg mx-auto text-xl leading-relaxed text-grigio-pietra">
                    {{ __('register.subtitle') }}
                </p>
                <p class="mt-4 font-semibold text-verde-rinascita">
                    {{ __('register.platform_grows_benefit') }}
                </p>
            </header>

            <main id="main-content" role="main" aria-labelledby="registration-title">
                <div class="p-8 shadow-xl glass-effect rounded-2xl sm:p-10">
                    <div class="mb-8">
                        <h2 id="registration-title" class="text-2xl font-semibold text-center font-rinascimento text-blu-algoritmo">
                            {{ __('register.form_title') }}
                        </h2>
                        <p class="mt-2 text-center text-grigio-pietra">
                            {{ __('register.form_subtitle') }}
                        </p>
                    </div>

                    {{-- Error Messages --}}
                    @if ($errors->any() || session('error'))
                        <div class="p-4 mb-6 border rounded-lg bg-red-50 border-rosso-urgenza" role="alert" aria-live="polite">
                            <div class="flex">
                                <svg class="w-5 h-5 text-rosso-urgenza" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-rosso-urgenza">{{ __('register.error_title') }}</h3>
                                    <div class="mt-2 text-sm text-rosso-urgenza">
                                        @if(session('error')) <p>{{ session('error') }}</p> @endif
                                        @if ($errors->any())
                                            <ul class="list-disc list-inside">
                                                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}" class="space-y-6" novalidate>
                        @csrf

                        {{-- User Type Selection --}}
                        <fieldset class="space-y-4">
                            <legend class="mb-4 text-lg font-semibold text-blu-algoritmo">{{ __('register.user_type_legend') }}</legend>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2"> {{-- Ridotto gap per compattezza --}}
                               @php
                                    $userTypes = [
                                        'creator' => ['icon_svg_path' => 'M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z', 'color' => 'oro-fiorentino'],
                                        'mecenate' => ['icon_svg_path' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1', 'color' => 'verde-rinascita'],
                                        'acquirente' => ['icon_svg_path' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z', 'color' => 'blu-algoritmo'],
                                        'azienda' => ['icon_svg_path' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'color' => 'grigio-pietra'],
                                        'epp_entity' => ['icon_svg_path' => 'M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c1.483 0 2.795-.298 3.996-.786M12 21c-1.483 0-2.795-.298-3.996-.786M3.786 15.004A9.004 9.004 0 0112 3c4.032 0 7.406 2.226 8.716 5.253M3.786 15.004A9.004 9.004 0 0012 21m-2.284-5.253A2.998 2.998 0 0012 15a2.998 2.998 0 002.284-1.253M12 12a2.998 2.998 0 01-2.284-1.253A2.998 2.998 0 0112 9a2.998 2.998 0 012.284 1.253A2.998 2.998 0 0112 12Z', 'color' => 'teal-500'], // Icona Mondo/Foglia - colore esempio
                                    ];
                                    // Assumiamo 'creator' come default se old('user_type') è nullo
                                    $selectedUserType = old('user_type', 'creator');
                                @endphp
                                @foreach ($userTypes as $type => $details)
                                <label class="p-4 cursor-pointer consent-card group {{ $selectedUserType === $type ? 'selected ring-2 ring-oro-fiorentino bg-oro-fiorentino/5' : '' }}" for="user_type_{{ $type }}">
                                    <input type="radio" id="user_type_{{ $type }}" name="user_type" value="{{ $type }}"
                                           class="sr-only" {{ $selectedUserType === $type ? 'checked' : '' }}
                                           aria-describedby="{{ $type }}-description" required>
                                    <div class="text-center">
                                        {{-- Ho aggiunto la classe bg-{{ $details['color'] }} per dinamizzare il colore --}}
                                        <div class="flex items-center justify-center w-12 h-12 mx-auto mb-3 transition-transform rounded-full bg-{{ $details['color'] }} group-hover:scale-110">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $details['icon_svg_path'] }}" />
                                            </svg>
                                        </div>
                                        <h3 class="font-semibold text-blu-algoritmo">{{ __('register.user_type_' . $type) }}</h3>
                                        <p id="{{ $type }}-description" class="mt-1 text-sm text-grigio-pietra">
                                            {{ __('register.user_type_' . $type . '_desc') }}
                                        </p>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                            @error('user_type')
                                <p class="mt-1 text-sm text-rosso-urgenza" role="alert">{{ $message }}</p>
                            @enderror
                        </fieldset>
                        {{-- Personal Information (spostato prima di password) --}}
                        <div class="grid grid-cols-1 gap-6 pt-6 border-t border-oro-fiorentino/20 sm:grid-cols-2">
                            <div>
                                <label for="name" class="block mb-2 text-sm font-medium text-blu-algoritmo">{{ __('register.label_name') }} *</label>
                                <input id="name" name="name" type="text" autocomplete="name" required
                                       class="block w-full px-4 py-3 input-rinascimento font-corpo"
                                       value="{{ old('name') }}" aria-describedby="name-error">
                                @error('name') <p id="name-error" class="mt-1 text-sm text-rosso-urgenza" role="alert">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="email" class="block mb-2 text-sm font-medium text-blu-algoritmo">{{ __('register.label_email') }} *</label>
                                <input id="email" name="email" type="email" autocomplete="email" required
                                       class="block w-full px-4 py-3 input-rinascimento font-corpo"
                                       value="{{ old('email') }}" aria-describedby="email-error">
                                @error('email') <p id="email-error" class="mt-1 text-sm text-rosso-urgenza" role="alert">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Password Fields (spostati dopo info personali) --}}
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="password" class="block mb-2 text-sm font-medium text-blu-algoritmo">{{ __('register.label_password') }} *</label>
                                <input id="password" name="password" type="password" autocomplete="new-password" required
                                       class="block w-full px-4 py-3 input-rinascimento font-corpo"
                                       aria-describedby="password-error password-help">
                                <p id="password-help" class="mt-1 text-xs text-grigio-pietra">{{ __('register.password_help') }}</p>
                                @error('password') <p id="password-error" class="mt-1 text-sm text-rosso-urgenza" role="alert">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="password_confirmation" class="block mb-2 text-sm font-medium text-blu-algoritmo">{{ __('register.label_password_confirmation') }} *</label>
                                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                                       class="block w-full px-4 py-3 input-rinascimento font-corpo"
                                       aria-describedby="password-confirmation-error">
                                @error('password_confirmation') <p id="password-confirmation-error" class="mt-1 text-sm text-rosso-urgenza" role="alert">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- GDPR Consent Section --}}
                        <fieldset class="pt-6 space-y-4 border-t border-oro-fiorentino/20">
                            <legend class="mb-4 text-lg font-semibold text-blu-algoritmo">
                                {{ __('register.privacy_legend') }}
                                <span class="block mt-1 text-sm font-normal text-grigio-pietra">{{ __('register.privacy_subtitle') }}</span>
                            </legend>
                            {{-- Required Legal Consents --}}
                            <div class="space-y-4">
                                @php
                                    $requiredConsents = [
                                        'privacy_policy_accepted' => ['route' => 'gdpr.privacy-policy', 'link_text_key' => 'register.privacy_policy_link_text'],
                                        'terms_accepted' => ['route' => 'gdpr.terms', 'link_text_key' => 'register.terms_link_text'],
                                        'age_confirmation' => [],
                                    ];
                                @endphp
                                @foreach ($requiredConsents as $consentKey => $details)
                                <div class="p-4 consent-card bg-green-50/50 {{ $errors->has($consentKey) ? 'error ring-rosso-urgenza' : '' }}">
                                    <div class="flex items-start">
                                        <div class="flex items-center h-6">
                                            <input id="{{ $consentKey }}" name="{{ $consentKey }}" type="checkbox" required value="1"
                                                   class="w-4 h-4 rounded text-oro-fiorentino border-oro-fiorentino focus:ring-oro-fiorentino"
                                                   {{ old($consentKey) ? 'checked' : '' }}
                                                   aria-describedby="{{ $consentKey }}-description">
                                        </div>
                                        <div class="ml-3">
                                            <label for="{{ $consentKey }}" class="text-sm font-medium cursor-pointer text-blu-algoritmo">{{ __('register.consent_label_' . $consentKey) }} *</label>
                                            <p id="{{ $consentKey }}-description" class="mt-1 text-xs text-grigio-pietra">
                                                {{ __('register.consent_desc_' . $consentKey) }}
                                                @if(!empty($details))
                                                <a href="{{ route($details['route']) }}" class="text-oro-fiorentino hover:underline" target="_blank" rel="noopener">
                                                    {{ __($details['link_text_key']) }} <span class="sr-only">{{ __('register.opens_new_window') }}</span>
                                                </a>
                                                @endif
                                            </p>
                                            @error($consentKey) <p class="mt-1 text-xs text-rosso-urgenza" role="alert">{{ $message }}</p> @enderror
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            {{-- Optional Consents --}}
                            <div class="mt-6 space-y-3">
                                <h4 class="text-sm font-medium text-blu-algoritmo">
                                    {{ __('register.optional_consents_title') }}
                                    <span class="block mt-1 text-xs font-normal text-grigio-pietra">{{ __('register.optional_consents_subtitle') }}</span>
                                </h4>
                                @php
                                    $optionalConsents = ['analytics', 'marketing', 'profiling'];
                                @endphp
                                @foreach ($optionalConsents as $consentType)
                                <div class="p-4 consent-card">
                                    <div class="flex items-start">
                                        <div class="flex items-center h-6">
                                            <input id="consents_{{ $consentType }}" name="consents[{{ $consentType }}]" type="checkbox" value="1"
                                                   class="w-4 h-4 rounded text-verde-rinascita border-verde-rinascita focus:ring-verde-rinascita"
                                                   {{ old('consents.' . $consentType) ? 'checked' : '' }}
                                                   aria-describedby="{{ $consentType }}-description">
                                        </div>
                                        <div class="ml-3">
                                            <label for="consents_{{ $consentType }}" class="text-sm font-medium cursor-pointer text-blu-algoritmo">{{ __('register.consent_label_optional_' . $consentType) }}</label>
                                            <p id="{{ $consentType }}-description" class="mt-1 text-xs text-grigio-pietra">{{ __('register.consent_desc_optional_' . $consentType) }}</p>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </fieldset>

                        <div class="pt-6">
                            <button type="submit" class="w-full px-6 py-4 text-lg font-semibold btn-rinascimento rounded-xl focus:outline-none focus:ring-4 focus:ring-oro-fiorentino focus:ring-opacity-50 disabled:opacity-50 disabled:cursor-not-allowed" aria-describedby="submit-help">
                                {{ __('register.submit_button') }}
                            </button>
                            <p id="submit-help" class="mt-3 text-xs text-center text-grigio-pietra">{{ __('register.submit_help') }}</p>
                        </div>

                        <div class="pt-4 text-center border-t border-oro-fiorentino/20">
                            <p class="text-grigio-pietra">
                                {{ __('register.already_registered_prompt') }}
                                <a href="{{ route('login') }}" class="font-medium transition-colors text-oro-fiorentino hover:text-verde-rinascita">{{ __('register.login_link') }}</a>
                            </p>
                        </div>
                    </form>
                </div>
            </main>

            <footer class="space-y-4 text-center" role="contentinfo">
                <div class="flex items-center justify-center space-x-6 text-sm text-grigio-pietra">
                    <div class="flex items-center"><svg class="w-4 h-4 mr-2 text-verde-rinascita" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" /></svg> {{ __('register.footer_gdpr') }}</div>
                    <div class="flex items-center"><svg class="w-4 h-4 mr-2 text-verde-rinascita" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg> {{ __('register.footer_data_protected') }}</div>
                    <div class="flex items-center"><svg class="w-4 h-4 mr-2 text-verde-rinascita" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" /></svg> {{ __('register.footer_real_impact') }}</div>
                </div>
                <p class="max-w-md mx-auto text-xs text-grigio-pietra">{{ __('register.footer_compliance_note') }}</p>
            </footer>
        </div>
    </div>

    {{-- JavaScript (invariato) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // User type selection enhancement
            const userTypeInputs = document.querySelectorAll('input[name="user_type"]');
            const userTypeCards = document.querySelectorAll('label.consent-card[for^="user_type_"]'); // Seleziona le label

            userTypeInputs.forEach(input => {
                input.addEventListener('change', function() {
                    userTypeCards.forEach(card => { // Usa la variabile corretta
                        card.classList.remove('selected', 'ring-2', 'ring-oro-fiorentino', 'bg-oro-fiorentino/5');
                    });
                    if (this.checked) {
                        this.closest('label.consent-card').classList.add('selected', 'ring-2', 'ring-oro-fiorentino', 'bg-oro-fiorentino/5');
                    }
                });
                 // Check on page load (assicurati che questo non causi problemi con old())
                 if (input.checked) {
                    input.closest('label.consent-card').classList.add('selected', 'ring-2', 'ring-oro-fiorentino', 'bg-oro-fiorentino/5');
                }
            });

            // Password strength indicator
            const passwordInput = document.getElementById('password');
            const passwordHelp = document.getElementById('password-help');
            if (passwordInput && passwordHelp) { /* ... codice calcolo strength ... */ }
            function calculatePasswordStrength(password) { /* ... */ }
            function updatePasswordHelp(element, strength) { /* ... */ }

            // Form validation enhancement
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const requiredConsents = document.querySelectorAll('input[type="checkbox"][required]');
                    let hasErrors = false;
                    requiredConsents.forEach(input => {
                        const card = input.closest('.consent-card');
                        if (!input.checked) {
                            hasErrors = true;
                            if(card) card.classList.add('error', 'ring-rosso-urgenza');
                        } else {
                            if(card) card.classList.remove('error', 'ring-rosso-urgenza');
                        }
                    });
                    if (hasErrors) {
                        e.preventDefault();
                        const firstErrorCard = document.querySelector('.consent-card.error');
                        if(firstErrorCard) firstErrorCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                });
            }
        });
    </script>
</body>
</html>
