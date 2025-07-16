<x-app-layout>
    <x-slot name="title">Crea Biografia</x-slot>

    <div class="max-w-4xl px-4 py-12 mx-auto sm:px-6 lg:px-8">
        <div class="p-8 border border-gray-700 shadow-2xl rounded-xl bg-gray-800/50 backdrop-blur-sm">

            <!-- Header -->
            <div class="mb-8">
                <h1 class="mb-2 text-3xl font-bold text-white">Crea Nuova Biografia</h1>
                <p class="text-gray-300">Racconta la tua storia e condividila con il mondo</p>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="p-4 mb-6 border border-red-500 rounded-lg bg-red-900/50">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="font-medium text-red-300">Errori di validazione</h3>
                    </div>
                    <ul class="mt-2 space-y-1 text-sm text-red-200">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Success Message -->
            @if (session()->has('success'))
                <div class="p-4 mb-6 border border-green-500 rounded-lg bg-green-900/50">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        <span class="font-medium text-green-300">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <!-- Form -->
            <form
                action="{{ isset($biography) ? route('biography.update', $biography->id) : route('biography.store') }}"
                method="POST" enctype="multipart/form-data" id="biography-form">
                @csrf
                @if (isset($biography))
                    @method('PUT')
                @endif

                <!-- Navigation Tabs -->
                <div class="mb-8 border-b border-gray-700">
                    <nav class="flex space-x-8">
                        <button type="button" class="tab-button active" data-tab="basic">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                <span>Informazioni Base</span>
                            </div>
                        </button>
                        <button type="button" class="tab-button" data-tab="media">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                                <span>Media</span>
                            </div>
                        </button>
                        <button type="button" class="tab-button" data-tab="settings">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span>Impostazioni</span>
                            </div>
                        </button>
                    </nav>
                </div>

                <!-- Tab Content -->
                <div id="basic-tab" class="space-y-6">
                    <!-- Title -->
                    <div>
                        <label for="title" class="block mb-2 text-sm font-medium text-gray-300">Titolo *</label>
                        <input type="text" id="title" name="title"
                            value="{{ old('title', $biography->title ?? '') }}"
                            class="w-full rounded-lg border border-gray-600 bg-gray-800 px-4 py-3 text-white placeholder-gray-400 transition-colors focus:border-[#D4A574] focus:ring-1 focus:ring-[#D4A574]"
                            placeholder="Inserisci il titolo della tua biografia" required>
                        @error('title')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <!-- Content -->
                    <div>
                        <label for="content" class="block mb-2 text-sm font-medium text-gray-300">Contenuto *</label>
                        <div class="trix-container">
                            <input id="content-trix" name="content" type="hidden"
                                value="{{ old('content', $biography->content ?? '') }}">
                            <trix-editor input="content-trix"
                                class="trix-editor-biography min-h-[300px] rounded-lg border border-gray-600 bg-gray-800 text-white transition-colors focus:border-[#D4A574] focus:ring-1 focus:ring-[#D4A574]"
                                placeholder="Racconta la tua storia..."></trix-editor>
                        </div>
                        @error('content')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <!-- Excerpt -->
                    <div>
                        <label for="excerpt" class="block mb-2 text-sm font-medium text-gray-300">Estratto</label>
                        <textarea id="excerpt" name="excerpt" rows="3" maxlength="500"
                            class="w-full rounded-lg border border-gray-600 bg-gray-800 px-4 py-3 text-white placeholder-gray-400 transition-colors focus:border-[#D4A574] focus:ring-1 focus:ring-[#D4A574]"
                            placeholder="Breve descrizione della tua biografia...">{{ old('excerpt', $biography->excerpt ?? '') }}</textarea>
                        <p class="mt-1 text-xs text-gray-400">Descrizione breve che apparirà in anteprima (<span
                                id="excerpt-count">{{ strlen(old('excerpt') ?? '') }}</span>/500)</p>
                        @error('excerpt')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <!-- Bottone aggiungi capitolo -->
                    <div class="flex items-center justify-end mt-8">
                        <button type="button" id="add-chapter-btn"
                            class="inline-flex items-center rounded-lg bg-gradient-to-r from-[#D4A574] to-[#E6B885] px-6 py-3 font-semibold text-gray-900 shadow-lg transition-all duration-200 hover:from-[#E6B885] hover:to-[#D4A574]">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4"></path>
                            </svg>
                            Aggiungi Capitolo
                        </button>
                    </div>
                </div>
                <div id="media-tab" class="hidden space-y-6">
                    <div class="space-y-6">
                        <h3 class="text-lg font-medium text-white">Gestione Media</h3>
                        <!-- Multiple Images Upload -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-300">Immagini Biografia</label>
                            <input type="file" id="multiple-images-input" multiple accept="image/*"
                                class="w-full rounded-lg border border-gray-600 bg-gray-800 px-4 py-3 text-white transition-colors file:mr-4 file:rounded-lg file:border-0 file:bg-[#D4A574] file:px-4 file:py-2 file:text-sm file:font-medium file:text-gray-900 hover:file:bg-[#E6B885]">
                            <p class="text-xs text-gray-400">Carica le immagini per la tua biografia. Formati
                                supportati: JPG, PNG, WEBP (Max 2MB ciascuna)</p>
                            <div id="upload-loading"
                                class="mt-2 hidden items-center space-x-2 rounded-lg bg-[#D4A574]/10 p-3 text-[#D4A574]">
                                <div class="h-4 w-4 animate-spin rounded-full border-b-2 border-[#D4A574]"></div>
                                <span class="text-sm font-medium">Caricamento immagini in corso...</span>
                            </div>
                            <div id="upload-error" class="hidden mt-1 text-sm text-red-400"></div>
                            <div id="upload-success" class="hidden mt-1 text-sm text-green-400"></div>
                            <div id="images-gallery" class="mt-6 space-y-4">
                                <h4 class="text-lg font-medium text-white">Immagini Caricate</h4>
                                <div id="images-grid" class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- MEDIA PER CAPITOLI -->
                    @if (isset($biography) && $biography->isChapterBased())
                        <div class="mt-10">
                            <h2 class="mb-6 text-2xl font-bold text-white">Media per Capitoli</h2>
                            @foreach ($biography->chapters as $chapter)
                                <div class="p-6 mb-10 shadow-lg rounded-xl bg-gray-800/60">
                                    <h3 class="mb-2 text-lg font-semibold text-white">{{ $chapter->title }}</h3>
                                    <!-- Upload immagini capitolo -->
                                    <form action="{{ route('biography.chapters.media.upload', $chapter->id) }}"
                                        method="POST" enctype="multipart/form-data"
                                        class="mb-4 chapter-media-upload" data-chapter="{{ $chapter->id }}">
                                        @csrf
                                        <input type="file" name="images[]" multiple accept="image/*"
                                            class="block w-full mb-2 text-white">
                                        <button type="submit"
                                            class="rounded-lg bg-[#D4A574] px-4 py-2 font-semibold text-gray-900 transition hover:bg-[#E6B885]">Carica
                                            Immagini</button>
                                    </form>
                                    <!-- Gallery immagini capitolo -->
                                    <div class="grid grid-cols-2 gap-4 mt-4 md:grid-cols-4"
                                        id="chapter-gallery-{{ $chapter->id }}">
                                        @foreach ($chapter->getMedia('chapter_images') as $media)
                                            <div class="relative group">
                                                <img src="{{ $media->getUrl('thumb') ?? $media->getUrl() }}"
                                                    alt="Immagine capitolo"
                                                    class="object-cover w-full h-32 rounded-lg shadow-md">
                                                <button data-media="{{ $media->id }}"
                                                    data-chapter="{{ $chapter->id }}"
                                                    class="absolute p-1 text-white transition bg-red-600 rounded-full delete-chapter-image right-2 top-2 opacity-80 hover:opacity-100">
                                                    &times;
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div id="settings-tab" class="hidden space-y-6">
                    <div class="space-y-6">
                        <h3 class="text-lg font-medium text-white">Impostazioni</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <label class="text-sm font-medium text-gray-300">Biografia Pubblica</label>
                                    <p class="text-xs text-gray-400">Rendi visibile la tua biografia a tutti gli utenti
                                    </p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_public" id="is_public" class="sr-only peer"
                                        {{ old('is_public', $biography->is_public ?? false) ? 'checked' : '' }}>
                                    <div
                                        class="peer h-6 w-11 rounded-full bg-gray-600 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-[#D4A574] peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#D4A574]/20">
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-6 mt-8 border-t border-gray-700">
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('biography.manage') }}"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-gray-800 border border-gray-600 rounded-lg hover:bg-gray-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Torna Indietro
                        </a>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button type="submit"
                            class="inline-flex items-center rounded-lg bg-gradient-to-r from-[#D4A574] to-[#E6B885] px-6 py-3 font-semibold text-gray-900 transition-all duration-200 hover:from-[#E6B885] hover:to-[#D4A574]">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ isset($biography) ? 'Aggiorna Biografia' : 'Crea Biografia' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modale CRUD Capitolo (markup base, da popolare via JS) -->
    <div id="chapter-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden ml-92 bg-black/60">
        <div class="relative w-full max-w-2xl p-8 bg-gray-900 shadow-2xl rounded-xl">
            <button id="close-chapter-modal"
                class="absolute text-2xl text-gray-400 right-4 top-4 hover:text-white">&times;</button>
            <div id="chapter-modal-content">
                <!-- Il form CRUD capitolo verrà iniettato qui via JS -->
            </div>
        </div>
    </div>
</x-app-layout>

@push('styles')
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2/dist/trix.css">
    <style>
        .tab-button {
            @apply border-b-2 border-transparent px-1 py-3 text-sm font-medium text-gray-400 transition-colors hover:text-white;
        }

        .tab-button.active {
            @apply border-[#D4A574] text-[#D4A574];
        }

        .tab-content {
            @apply hidden;
        }

        .tab-content.active {
            @apply block;
        }

        .trix-editor-biography {
            @apply rounded-lg border border-gray-600 bg-gray-800 text-white;
        }

        .trix-editor-biography:focus {
            @apply border-[#D4A574] ring-1 ring-[#D4A574];
        }

        /* Trix editor customizations */
        trix-editor {
            background-color: #1f2937 !important;
            color: white !important;
        }

        trix-editor strong {
            color: #d1d5db !important;
        }

        trix-editor a {
            color: #D4A574 !important;
        }

        trix-editor blockquote {
            border-left: 4px solid #D4A574;
            padding-left: 1rem;
            margin-left: 0;
            color: #d1d5db !important;
        }

        trix-editor ul,
        trix-editor ol {
            margin: 0.5rem 0 !important;
            padding-left: 1.5rem !important;
        }

        trix-editor li {
            margin: 0.25rem 0 !important;
        }
    </style>
@endpush

@push('scripts')
    <script type="text/javascript" src="https://unpkg.com/trix@2/dist/trix.umd.min.js"></script>
    <script>
        // Disable file uploads in Trix
        addEventListener("trix-file-accept", function(event) {
            event.preventDefault()
        })
    </script>
    <script src="{{ asset('js/biography-edit.js') }}"></script>
@endpush
