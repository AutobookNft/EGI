    {{-- resources/views/home.blade.php --}}
    {{--
        Vista specifica per la Home Page.
        Utilizza il componente di layout guest <x-guest-layout>
        e fornisce il contenuto unico per questa pagina tramite le slot.
    --}}

    {{-- Utilizzo del componente di layout guest --}}
    {{-- Passo i dati per le slot "nominate" come attributi HTML --}}
    <x-guest-layout>

        {{-- Definizione della slot 'title' (iniettata nel <title> tag) --}}
        <x-slot name="title">
            {{ __('Home - Discover Unique Art, Support Rainforest') }} | FlorenceEGI
        </x-slot>

        {{-- Definizione della slot 'metaDescription' (iniettata nel <meta name="description"> tag) --}}
        <x-slot name="metaDescription">
            {{ __('Explore unique Ecological Goods Invent (EGI) collections on FlorenceEGI. Buy digital art that concretely funds rainforest protection and other environmental projects.') }}
        </x-slot>

         {{-- (Opzionale) Definizione della slot 'robotsMeta' se la pagina non deve essere indicizzata
         <x-slot name="robotsMeta">
             <meta name="robots" content="noindex, follow">
         </x-slot>
         --}}

        {{-- Definizione della slot 'heroContent' (iniettata nel div '.hero-content' della Hero Section) --}}
        <x-slot name="heroContent">
            {{-- H1 principale della pagina - Essenziale per SEO --}}
            <h1>
                <span class="block text-5xl font-extrabold">{{ __('Discover Unique Art') }},</span>
                <span class="block text-4xl font-extrabold mt-2">{{ __('Support Rainforest') }}</span>
            </h1>
            <div class="space-x-4 mt-6">
                <a href="{{ url('/collections') }}" class="inline-block px-6 py-3 bg-green-500 rounded-lg hover:bg-green-600 transition btn-hover">{{ __('Explore Collections') }}</a>
                <a href="{{ url('/upload/egi') }}" class="inline-block px-6 py-3 bg-white text-green-600 rounded-lg hover:bg-gray-100 transition btn-hover">{{ __('Create Your Work') }}</a>
            </div>
        </x-slot>

        {{--
            Il contenuto qui sotto sarà iniettato nella slot principale ($slot)
            che si trova all'interno del tag <main> nel layout.
            Corrisponde alle sezioni specifiche della home page.
        --}}

        <!-- Collezioni in Evidenza -->
        <section class="py-16"> {{-- Considerare un ID o un ARIA label per questa sezione se diventa un landmark importante --}}
            <div class="max-w-7xl mx-auto px-6">
            {{-- H2 per il sottotitolo della sezione - Buona pratica semantica --}}
            <h2 class="text-2xl font-bold mb-6">{{ __('Featured Collections') }}</h2>
            <div class="flex space-x-4 overflow-x-auto pb-4">
                <!-- Esempio di card -->
                <div class="min-w-[250px] bg-white rounded-2xl shadow-lg p-4 transition hover:shadow-xl">
                <!-- Placeholder per l'immagine - Aggiungere attributo alt! -->
                <div class="w-full h-40 bg-gradient-to-r from-green-500 to-green-600 rounded-lg mb-4 flex items-center justify-center text-white font-bold" aria-label="{{ __('Image placeholder for') }} 'Luce d\'Oriente'">{{ __('Nature') }}</div>
                <h3 class="font-semibold">{{ __('Green Collection') }}</h3>
                <p class="text-sm text-gray-500">{{ __('10 works') }} • 20% EPP</p>
                </div>

                <!-- Altra card -->
                <div class="min-w-[250px] bg-white rounded-2xl shadow-lg p-4 transition hover:shadow-xl">
                <div class="w-full h-40 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg mb-4 flex items-center justify-center text-white font-bold" aria-label="{{ __('Image placeholder for') }} 'Foresta Primordiale'">{{ __('Oceans') }}</div>
                <h3 class="font-semibold">{{ __('Blue Collection') }}</h3>
                <p class="text-sm text-gray-500">{{ __('8 works') }} • 25% EPP</p>
                </div>

                <!-- Altra card -->
                <div class="min-w-[250px] bg-white rounded-2xl shadow-lg p-4 transition hover:shadow-xl">
                <div class="w-full h-40 bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-lg mb-4 flex items-center justify-center text-white font-bold" aria-label="{{ __('Image placeholder for') }} 'Oceano Profondo'">{{ __('Savannah') }}</div>
                <h3 class="font-semibold">{{ __('Gold Collection') }}</h3>
                <p class="text-sm text-gray-500">{{ __('12 works') }} • 15% EPP</p>
                </div>
            </div>
            </div>
        </section>

        <!-- Trending EGI -->
        <section class="py-16 bg-gray-100"> {{-- Considerare un ID o un ARIA label per questa sezione se diventa un landmark importante --}}
            <div class="max-w-7xl mx-auto px-6">
            {{-- H2 per il sottotitolo della sezione - Buona pratica semantica --}}
            <h2 class="text-2xl font-bold mb-6">{{ __('Trending EGI') }}</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Card singola -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden transition hover:shadow-xl">
                <!-- Placeholder per l'immagine - Aggiungere attributo alt! -->
                <div class="w-full h-48 bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white font-bold" aria-label="{{ __('Image placeholder for') }} 'Luce d\'Oriente'">{{ __('Luce d\'Oriente') }}</div>
                <div class="p-4">
                    <div class="flex justify-between items-center mb-2">
                    <span class="font-semibold">"{{ __('Luce d\'Oriente') }}"</span>
                    <span class="text-xs bg-green-500 text-white px-2 py-1 rounded-full">{{ __('1 of 1') }}</span>
                    </div>
                    <p class="text-sm text-gray-600 mb-3">{{ __('oil on canvas') }} • {{ __('creative recycling') }}</p>
                    <div class="flex justify-between items-center">
                    <span class="text-sm text-green-600">🌱 {{ __('€20 donated') }}</span>
                    <a href="https://egiflorence.com/egi/1" class="text-green-600 hover:underline">{{ __('Discover') }}</a>
                    </div>
                </div>
                </div>

                <!-- Altra card -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden transition hover:shadow-xl">
                <div class="w-full h-48 bg-gradient-to-br from-green-500 to-teal-500 flex items-center justify-center text-white font-bold" aria-label="{{ __('Image placeholder for') }} 'Foresta Primordiale'">{{ __('Foresta Primordiale') }}</div>
                <div class="p-4">
                    <div class="flex justify-between items-center mb-2">
                    <span class="font-semibold">"{{ __('Foresta Primordiale') }}"</span>
                    <span class="text-xs bg-green-500 text-white px-2 py-1 rounded-full">{{ __('2 of 5') }}</span>
                    </div>
                    <p class="text-sm text-gray-600 mb-3">{{ __('digital') }} • {{ __('zero impact') }}</p>
                    <div class="flex justify-between items-center">
                    <span class="text-sm text-green-600">🌱 {{ __('€30 donated') }}</span>
                    <a href="https://egiflorence.com/egi/2" class="text-green-600 hover:underline">{{ __('Discover') }}</a>
                    </div>
                </div>
                </div>

                <!-- Altra card -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden transition hover:shadow-xl">
                <div class="w-full h-48 bg-gradient-to-br from-blue-500 to-indigo-500 flex items-center justify-center text-white font-bold" aria-label="{{ __('Image placeholder for') }} 'Oceano Profondo'">{{ __('Oceano Profondo') }}</div>
                <div class="p-4">
                    <div class="flex justify-between items-center mb-2">
                    <span class="font-semibold">"{{ __('Oceano Profondo') }}"</span>
                    <span class="text-xs bg-green-500 text-white px-2 py-1 rounded-full">{{ __('3 of 10') }}</span>
                    </div>
                    <p class="text-sm text-gray-600 mb-3">{{ __('watercolor') }} • {{ __('marine protection') }}</p>
                    <div class="flex justify-between items-center">
                    <span class="text-sm text-green-600">🌱 {{ __('€15 donated') }}</span>
                    <a href="https://egiflorence.com/egi/3" class="text-green-600 hover:underline">{{ __('Discover') }}</a>
                    </div>
                </div>
                </div>
            </div>
            </div>
        </section>

        <!-- Il Tuo Impatto -->
        <section class="py-16"> {{-- Considerare un ID o un ARIA label per questa sezione se diventa un landmark importante --}}
            <div class="max-w-7xl mx-auto px-6">
            {{-- H2 per il sottotitolo della sezione - Buona pratica semantica --}}
            <h2 class="text-2xl font-bold mb-6">{{ __('Your Impact') }}</h2>
            <div class="bg-white rounded-2xl shadow-lg p-6 flex flex-col sm:flex-row items-center justify-between">
                <div class="text-center sm:text-left space-y-4">
                <p class="text-4xl font-bold">0 kg CO₂</p> {{-- Placeholder valore --}}
                <p class="text-gray-600">{{ __('Compensated so far') }} ({{ __('anonymous') }})</p>
                </div>
                <div class="text-center sm:text-left space-y-4">
                <p class="text-4xl font-bold">€0</p> {{-- Placeholder valore --}}
                <p class="text-gray-600">{{ __('Donated to EPPs') }}</p>
                </div>
                <a href="{{ url('/register') }}" class="mt-4 sm:mt-0 px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition btn-hover">{{ __('Register to track your impact') }}</a>
            </div>
            </div>
        </section>

        {{--
            Schema.org Markup per la Home Page (specifico)
            Questo markup descrive la home page. Il tipo più adatto potrebbe essere 'CollectionPage'
            dato che mostra collezioni e EGI, o semplicemente 'WebPage'.
            Utilizziamo 'WebPage' per semplicità, dato che include diverse tipologie di contenuto.
            Potremmo anche aggiungere markup specifico per le sezioni 'Featured Collections' e 'Trending EGI'
            se i dati sottostanti sono strutturati (es. usando ListItem, ImageObject, Product, Offer, ecc.).
            Questo esempio aggiunge il markup WebPage generale per la home.
        --}}
        <script type="application/ld+json">
        {
          "@context": "https://schema.org",
          "@type": "WebPage",
          "headline": "{{ __('Home - Discover Unique Art, Support Rainforest') }} | FlorenceEGI", {{-- Testo localizzato --}}
          "description": "{{ __('Explore unique Ecological Goods Invent (EGI) collections on FlorenceEGI. Buy digital art that concretely funds rainforest protection and other environmental projects.') }}", {{-- Testo localizzato --}}
          "url": "{{ url('/') }}",
          "datePublished": "{{ now()->format('Y-m-d') }}",
          "publisher": {
              "@type": "Organization",
              "name": "{{ __('Frangette Cultural Promotion Association') }}", {{-- Testo localizzato --}}
              "url": "https://frangette.com/",
              "logo": {
                "@type": "ImageObject",
                "url": "{{ asset('images/frangette-logo.png') }}"
              }
          },
          "mentions": [
            {
                "@type": "Organization",
                "name": "FlorenceEGI",
                "url": "https://florenceegi.com/"
            },
             {
                "@type": "Project",
                "name": "{{ __('Environment Protection Programs (EPPs)') }}" {{-- Testo localizzato --}}
            },
             {
                "@type": "Person",
                "name": "Natan Frangette"
            }
          ]
        }
        </script>

    </x-guest-layout>
