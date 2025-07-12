<div class="space-y-6">
    <!-- Loading Overlay -->
    <div wire:loading.delay.longer class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="flex items-center space-x-3 rounded-lg bg-gray-800 p-6">
            <div class="h-8 w-8 animate-spin rounded-full border-b-2 border-[#D4A574]"></div>
            <span class="font-medium text-white">Salvataggio in corso...</span>
        </div>
    </div>

    <!-- Error Messages -->
    @if ($errors->any())
        <div class="rounded-lg border border-red-500 bg-red-900/50 p-4">
            <div class="flex items-center space-x-2">
                <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        <div class="rounded-lg border border-green-500 bg-green-900/50 p-4">
            <div class="flex items-center space-x-2">
                <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span class="font-medium text-green-300">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Navigation Tabs -->
    <div class="border-b border-gray-700">
        <nav class="flex space-x-8">
            <button wire:click="setActiveTab('basic')"
                class="{{ $activeTab === 'basic' ? 'border-[#D4A574] text-[#D4A574]' : 'border-transparent text-gray-400 hover:text-white' }} border-b-2 px-1 py-3 text-sm font-medium transition-colors">
                <div class="flex items-center space-x-2">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    <span>Informazioni Base</span>
                </div>
            </button>

            @if ($type === 'chapters')
                <button wire:click="setActiveTab('chapters')"
                    class="{{ $activeTab === 'chapters' ? 'border-[#D4A574] text-[#D4A574]' : 'border-transparent text-gray-400 hover:text-white' }} border-b-2 px-1 py-3 text-sm font-medium transition-colors">
                    <div class="flex items-center space-x-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                            </path>
                        </svg>
                        <span>Capitoli</span>
                    </div>
                </button>
            @endif

            <button wire:click="setActiveTab('media')"
                class="{{ $activeTab === 'media' ? 'border-[#D4A574] text-[#D4A574]' : 'border-transparent text-gray-400 hover:text-white' }} border-b-2 px-1 py-3 text-sm font-medium transition-colors">
                <div class="flex items-center space-x-2">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                    <span>Media</span>
                </div>
            </button>

            <button wire:click="setActiveTab('settings')"
                class="{{ $activeTab === 'settings' ? 'border-[#D4A574] text-[#D4A574]' : 'border-transparent text-gray-400 hover:text-white' }} border-b-2 px-1 py-3 text-sm font-medium transition-colors">
                <div class="flex items-center space-x-2">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
    <div class="space-y-6">
        <!-- Basic Information Tab -->
        @if ($activeTab === 'basic')
            <div class="space-y-6">
                <!-- Title -->
                <div>
                    <label for="title" class="mb-2 block text-sm font-medium text-gray-300">
                        {{ __('biography.form.title') }} *
                    </label>
                    <input type="text" id="title" wire:model.live.debounce.500ms="title"
                        class="w-full rounded-lg border border-gray-600 bg-gray-800 px-4 py-3 text-white placeholder-gray-400 transition-colors focus:border-[#D4A574] focus:ring-1 focus:ring-[#D4A574]"
                        placeholder="{{ __('biography.form.title_placeholder') }}">
                    @error('title')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Type -->
                <div>
                    <label for="type" class="mb-2 block text-sm font-medium text-gray-300">
                        {{ __('biography.form.type') }} *
                    </label>
                    <select id="type" wire:model.live="type"
                        class="w-full rounded-lg border border-gray-600 bg-gray-800 px-4 py-3 text-white transition-colors focus:border-[#D4A574] focus:ring-1 focus:ring-[#D4A574]">
                        <option value="single">{{ __('biography.type.single') }}</option>
                        <option value="chapters">{{ __('biography.type.chapters') }}</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Content (only for single type) -->
                @if ($type === 'single')
                    <div>
                        <label for="content" class="mb-2 block text-sm font-medium text-gray-300">
                            {{ __('biography.form.content') }} *
                        </label>
                        <div class="trix-container">
                            <input id="content-trix" name="content" type="hidden" wire:model="content">
                            <trix-editor input="content-trix"
                                class="trix-editor-biography min-h-[300px] rounded-lg border border-gray-600 bg-gray-800 text-white transition-colors focus:border-[#D4A574] focus:ring-1 focus:ring-[#D4A574]"
                                placeholder="{{ __('biography.form.content_placeholder') }}" wire:ignore
                                x-data="trixEditor()" x-init="initTrix()" @trix-change="updateContent($event)">
                            </trix-editor>
                        </div>
                        @error('content')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <!-- Excerpt -->
                <div>
                    <label for="excerpt" class="mb-2 block text-sm font-medium text-gray-300">
                        {{ __('biography.form.excerpt') }}
                    </label>
                    <textarea id="excerpt" wire:model.live.debounce.500ms="excerpt" rows="3" maxlength="500"
                        class="w-full rounded-lg border border-gray-600 bg-gray-800 px-4 py-3 text-white placeholder-gray-400 transition-colors focus:border-[#D4A574] focus:ring-1 focus:ring-[#D4A574]"
                        placeholder="{{ __('biography.form.excerpt_placeholder') }}"></textarea>
                    <p class="mt-1 text-xs text-gray-400">
                        {{ __('biography.form.excerpt_help') }} ({{ strlen($excerpt) }}/500)
                    </p>
                    @error('excerpt')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        @endif

        <!-- Chapters Tab -->
        @if ($activeTab === 'chapters' && $type === 'chapters')
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-white">Gestione Capitoli</h3>
                    <button wire:click="addChapter"
                        class="inline-flex items-center rounded-lg bg-[#D4A574] px-4 py-2 font-medium text-gray-900 transition-colors hover:bg-[#E6B885]">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                            </path>
                        </svg>
                        Aggiungi Capitolo
                    </button>
                </div>

                <!-- Chapters List -->
                <div class="space-y-4">
                    @forelse($chapters as $index => $chapter)
                        <div class="rounded-lg border border-gray-700 bg-gray-800/50 p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h4 class="font-medium text-white">{{ $chapter['title'] }}</h4>
                                    <p class="mt-1 text-sm text-gray-400">
                                        @if ($chapter['date_from'])
                                            {{ $chapter['date_from'] }}
                                            @if ($chapter['date_to'] && !$chapter['is_ongoing'])
                                                - {{ $chapter['date_to'] }}
                                            @elseif($chapter['is_ongoing'])
                                                - In corso
                                            @endif
                                        @endif
                                    </p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button wire:click="editChapter({{ $index }})"
                                        class="p-2 text-gray-400 transition-colors hover:text-[#D4A574]">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    </button>
                                    <button wire:click="deleteChapter({{ $index }})"
                                        class="p-2 text-gray-400 transition-colors hover:text-red-400">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-gray-400">
                            <svg class="mx-auto mb-4 h-12 w-12" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            <p>Nessun capitolo ancora. Inizia aggiungendo il primo capitolo.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @endif

        <!-- Media Tab -->
        @if ($activeTab === 'media')
            <div class="space-y-6">
                <h3 class="text-lg font-medium text-white">Gestione Media</h3>

                <!-- Featured Image -->
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-300">
                        {{ __('biography.form.featured_image') }}
                    </label>
                    <div class="space-y-4">
                        <input type="file" wire:model="featuredImage" accept="image/*"
                            class="w-full rounded-lg border border-gray-600 bg-gray-800 px-4 py-3 text-white transition-colors file:mr-4 file:rounded-lg file:border-0 file:bg-[#D4A574] file:px-4 file:py-2 file:text-sm file:font-medium file:text-gray-900 hover:file:bg-[#E6B885]"
                            wire:loading.attr="disabled" wire:target="featuredImage"
                            onchange="checkFileSize(this, 2048)">
                        <p class="text-xs text-gray-400">
                            {{ __('biography.form.featured_image_hint') }}
                        </p>

                        <!-- Loading indicator -->
                        <div wire:loading wire:target="featuredImage"
                            class="flex items-center space-x-2 rounded-lg bg-[#D4A574]/10 p-3 text-[#D4A574]">
                            <div class="h-4 w-4 animate-spin rounded-full border-b-2 border-[#D4A574]"></div>
                            <span class="text-sm font-medium">🔄 Caricamento immagine in corso...</span>
                        </div>

                        @error('featuredImage')
                            <p class="text-sm text-red-400">{{ $message }}</p>
                        @enderror

                        @if ($featuredImage)
                            <div class="relative mt-4 inline-block">
                                <img src="{{ $featuredImage->temporaryUrl() }}" alt="Preview"
                                    class="h-32 w-48 rounded-lg object-cover">
                                <button wire:click="removeFeaturedImage"
                                    class="absolute -right-2 -top-2 flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-white transition-colors hover:bg-red-600">
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Gallery Images -->
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-300">
                        Immagini Galleria
                    </label>
                    <input type="file" wire:model="galleryImages" multiple accept="image/*"
                        class="w-full rounded-lg border border-gray-600 bg-gray-800 px-4 py-3 text-white transition-colors file:mr-4 file:rounded-lg file:border-0 file:bg-[#D4A574] file:px-4 file:py-2 file:text-sm file:font-medium file:text-gray-900 hover:file:bg-[#E6B885]"
                        wire:loading.attr="disabled" wire:target="galleryImages"
                        onchange="checkMultipleFileSize(this, 2048)">
                    <!-- Loading indicator -->
                    <div wire:loading wire:target="galleryImages"
                        class="mt-2 flex items-center space-x-2 rounded-lg bg-[#D4A574]/10 p-3 text-[#D4A574]">
                        <div class="h-4 w-4 animate-spin rounded-full border-b-2 border-[#D4A574]"></div>
                        <span class="text-sm font-medium">🔄 Caricamento immagini in corso...</span>
                    </div>

                    @error('galleryImages.*')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror

                    @if ($galleryImages)
                        <div class="mt-4 grid grid-cols-2 gap-4 md:grid-cols-3">
                            @foreach ($galleryImages as $index => $image)
                                <div class="relative">
                                    <img src="{{ $image->temporaryUrl() }}" alt="Gallery preview"
                                        class="h-32 w-full rounded-lg object-cover">
                                    <button wire:click="removeGalleryImage({{ $index }})"
                                        class="absolute -right-2 -top-2 flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-white transition-colors hover:bg-red-600">
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Settings Tab -->
        @if ($activeTab === 'settings')
            <div class="space-y-6">
                <h3 class="text-lg font-medium text-white">Impostazioni</h3>

                <!-- Privacy Settings -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <label class="text-sm font-medium text-gray-300">
                                {{ __('biography.form.is_public') }}
                            </label>
                            <p class="text-xs text-gray-400">
                                {{ __('biography.form.is_public_help') }}
                            </p>
                        </div>
                        <label class="relative inline-flex cursor-pointer items-center">
                            <input type="checkbox" wire:model="isPublic" class="peer sr-only">
                            <div
                                class="peer h-6 w-11 rounded-full bg-gray-600 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-[#D4A574] peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#D4A574]/20">
                            </div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <label class="text-sm font-medium text-gray-300">
                                {{ __('biography.form.is_completed') }}
                            </label>
                            <p class="text-xs text-gray-400">
                                {{ __('biography.form.is_completed_help') }}
                            </p>
                        </div>
                        <label class="relative inline-flex cursor-pointer items-center">
                            <input type="checkbox" wire:model="isCompleted" class="peer sr-only">
                            <div
                                class="peer h-6 w-11 rounded-full bg-gray-600 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-[#D4A574] peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#D4A574]/20">
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Display Settings -->
                <div class="space-y-4">
                    <h4 class="font-medium text-white">Opzioni Visualizzazione</h4>

                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <label class="text-sm font-medium text-gray-300">
                                Mostra Timeline
                            </label>
                            <p class="text-xs text-gray-400">
                                Visualizza la timeline nei capitoli
                            </p>
                        </div>
                        <label class="relative inline-flex cursor-pointer items-center">
                            <input type="checkbox" wire:model="settings.show_timeline" class="peer sr-only">
                            <div
                                class="peer h-6 w-11 rounded-full bg-gray-600 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-[#D4A574] peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#D4A574]/20">
                            </div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <label class="text-sm font-medium text-gray-300">
                                Permetti Commenti
                            </label>
                            <p class="text-xs text-gray-400">
                                Abilita i commenti sulla biografia
                            </p>
                        </div>
                        <label class="relative inline-flex cursor-pointer items-center">
                            <input type="checkbox" wire:model="settings.allow_comments" class="peer sr-only">
                            <div
                                class="peer h-6 w-11 rounded-full bg-gray-600 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-[#D4A574] peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#D4A574]/20">
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Action Buttons -->
    <div class="flex items-center justify-between border-t border-gray-700 pt-6">
        <div class="flex items-center space-x-4">
            @if ($isEditing)
                <span class="text-sm text-gray-400">
                    Ultima modifica: {{ now()->format('d/m/Y H:i') }}
                </span>
            @endif
        </div>

        <div class="flex items-center space-x-4">
            <button wire:click="save" wire:loading.attr="disabled"
                class="inline-flex items-center rounded-lg bg-gradient-to-r from-[#D4A574] to-[#E6B885] px-6 py-3 font-semibold text-gray-900 transition-all duration-200 hover:from-[#E6B885] hover:to-[#D4A574] disabled:opacity-50">
                <svg wire:loading.remove.delay class="mr-2 h-5 w-5" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <div wire:loading.delay class="mr-2 h-5 w-5">
                    <div class="h-4 w-4 animate-spin rounded-full border-b-2 border-gray-900"></div>
                </div>
                {{ $isEditing ? __('biography.form.update_biography') : __('biography.form.create_biography') }}
            </button>
        </div>
    </div>

    <!-- Chapter Form Modal -->
    @if ($showChapterForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="m-4 max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-xl bg-gray-800 shadow-2xl">
                <div class="p-6">
                    <div class="mb-6 flex items-center justify-between">
                        <h3 class="text-xl font-bold text-white">
                            {{ $currentChapter['id'] ? 'Modifica Capitolo' : 'Nuovo Capitolo' }}
                        </h3>
                        <button wire:click="cancelChapterEdit" class="text-gray-400 hover:text-white">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-6">
                        <!-- Chapter Title -->
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-300">
                                Titolo Capitolo *
                            </label>
                            <input type="text" wire:model="currentChapter.title"
                                class="w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-3 text-white placeholder-gray-400 transition-colors focus:border-[#D4A574] focus:ring-1 focus:ring-[#D4A574]"
                                placeholder="Inserisci il titolo del capitolo">
                            @error('currentChapter.title')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Chapter Content -->
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-300">
                                Contenuto *
                            </label>
                            <div class="trix-container">
                                <input id="chapter-content-trix" name="chapter_content" type="hidden"
                                    wire:model="currentChapter.content">
                                <trix-editor input="chapter-content-trix"
                                    class="trix-editor-chapter min-h-[200px] rounded-lg border border-gray-600 bg-gray-700 text-white transition-colors focus:border-[#D4A574] focus:ring-1 focus:ring-[#D4A574]"
                                    placeholder="Racconta questa parte della tua storia..." wire:ignore
                                    x-data="trixChapterEditor()" x-init="initChapterTrix()"
                                    @trix-change="updateChapterContent($event)">
                                </trix-editor>
                            </div>
                            @error('currentChapter.content')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Date Range -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-300">
                                    Data Inizio
                                </label>
                                <input type="date" wire:model="currentChapter.date_from"
                                    class="w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-3 text-white transition-colors focus:border-[#D4A574] focus:ring-1 focus:ring-[#D4A574]">
                                @error('currentChapter.date_from')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-300">
                                    Data Fine
                                </label>
                                <input type="date" wire:model="currentChapter.date_to"
                                    :disabled="$wire.currentChapter.is_ongoing"
                                    class="w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-3 text-white transition-colors focus:border-[#D4A574] focus:ring-1 focus:ring-[#D4A574] disabled:opacity-50">
                                @error('currentChapter.date_to')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Options -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <label class="text-sm font-medium text-gray-300">
                                    In Corso
                                </label>
                                <label class="relative inline-flex cursor-pointer items-center">
                                    <input type="checkbox" wire:model="currentChapter.is_ongoing"
                                        class="peer sr-only">
                                    <div
                                        class="peer h-6 w-11 rounded-full bg-gray-600 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-[#D4A574] peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#D4A574]/20">
                                    </div>
                                </label>
                            </div>

                            <div class="flex items-center justify-between">
                                <label class="text-sm font-medium text-gray-300">
                                    Pubblicato
                                </label>
                                <label class="relative inline-flex cursor-pointer items-center">
                                    <input type="checkbox" wire:model="currentChapter.is_published"
                                        class="peer sr-only">
                                    <div
                                        class="peer h-6 w-11 rounded-full bg-gray-600 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-[#D4A574] peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#D4A574]/20">
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Chapter Actions -->
                    <div class="mt-6 flex items-center justify-end space-x-4 border-t border-gray-700 pt-6">
                        <button wire:click="cancelChapterEdit"
                            class="px-4 py-2 text-gray-400 transition-colors hover:text-white">
                            Annulla
                        </button>
                        <button wire:click="saveChapter"
                            class="rounded-lg bg-[#D4A574] px-6 py-2 font-medium text-gray-900 transition-colors hover:bg-[#E6B885]">
                            Salva Capitolo
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('styles')
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2/dist/trix.css">
    <style>
        /* Trix Editor Dark Theme Customization for FlorenceEGI */
        trix-editor {
            background-color: rgb(31 41 55) !important;
            color: white !important;
            border: 1px solid rgb(75 85 99) !important;
            border-radius: 0.5rem !important;
            font-family: ui-sans-serif, system-ui, sans-serif !important;
        }

        .trix-editor-biography {
            min-height: 300px !important;
        }

        .trix-editor-chapter {
            min-height: 200px !important;
        }

        trix-toolbar {
            background-color: rgb(55 65 81) !important;
            border-bottom: 1px solid rgb(75 85 99) !important;
            border-radius: 0.5rem 0.5rem 0 0 !important;
            padding: 0.75rem !important;
        }

        trix-toolbar .trix-button-group {
            background-color: transparent !important;
            border: none !important;
            margin-right: 0.5rem !important;
        }

        trix-toolbar .trix-button {
            background-color: rgb(75 85 99) !important;
            border: 1px solid rgb(107 114 128) !important;
            color: white !important;
            border-radius: 0.375rem !important;
            margin: 0 0.125rem !important;
            padding: 0.375rem 0.75rem !important;
            transition: all 0.2s ease !important;
        }

        trix-toolbar .trix-button:hover {
            background-color: #D4A574 !important;
            border-color: #D4A574 !important;
            color: rgb(17 24 39) !important;
        }

        trix-toolbar .trix-button.trix-active {
            background-color: #D4A574 !important;
            border-color: #D4A574 !important;
            color: rgb(17 24 39) !important;
        }

        trix-toolbar .trix-button:not(:disabled) {
            background-color: rgb(75 85 99) !important;
        }

        trix-toolbar .trix-dialogs {
            background-color: rgb(31 41 55) !important;
            border: 1px solid rgb(75 85 99) !important;
            border-radius: 0.5rem !important;
        }

        trix-toolbar .trix-dialog {
            background-color: rgb(31 41 55) !important;
            color: white !important;
            padding: 1rem !important;
        }

        trix-toolbar .trix-dialog__link-fields input {
            background-color: rgb(55 65 81) !important;
            border: 1px solid rgb(75 85 99) !important;
            color: white !important;
            border-radius: 0.375rem !important;
            padding: 0.5rem !important;
        }

        /* Content Area Styling */
        trix-editor h1 {
            color: #D4A574 !important;
            font-size: 1.875rem !important;
            font-weight: 700 !important;
            margin: 1rem 0 !important;
        }

        trix-editor h2 {
            color: #E6B885 !important;
            font-size: 1.5rem !important;
            font-weight: 600 !important;
            margin: 0.875rem 0 !important;
        }

        trix-editor h3 {
            color: white !important;
            font-size: 1.25rem !important;
            font-weight: 600 !important;
            margin: 0.75rem 0 !important;
        }

        trix-editor p {
            margin: 0.5rem 0 !important;
            line-height: 1.6 !important;
        }

        trix-editor strong {
            color: #D4A574 !important;
        }

        trix-editor em {
            color: #E6B885 !important;
        }

        trix-editor a {
            color: #D4A574 !important;
            text-decoration: underline !important;
        }

        trix-editor blockquote {
            border-left: 4px solid #D4A574 !important;
            padding-left: 1rem !important;
            margin: 1rem 0 !important;
            color: rgb(156 163 175) !important;
            font-style: italic !important;
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

        // Alpine.js components for Trix integration
        function trixEditor() {
            return {
                content: @entangle('content'),

                initTrix() {
                    this.$nextTick(() => {
                        const editor = this.$el;
                        const trixEditor = editor.editor;

                        // Set initial content
                        if (this.content) {
                            trixEditor.loadHTML(this.content);
                        }

                        // Listen for changes and update Livewire
                        editor.addEventListener('trix-change', (e) => {
                            const htmlContent = trixEditor.getDocument().toString();
                            @this.call('updateTrixContent', htmlContent);
                        });
                    });
                },

                updateContent(event) {
                    const htmlContent = event.target.editor.getDocument().toString();
                    @this.call('updateTrixContent', htmlContent);
                }
            }
        }

        function trixChapterEditor() {
            return {
                chapterContent: @entangle('currentChapter.content'),

                initChapterTrix() {
                    this.$nextTick(() => {
                        const editor = this.$el;
                        const trixEditor = editor.editor;

                        // Set initial content
                        if (this.chapterContent) {
                            trixEditor.loadHTML(this.chapterContent);
                        }

                        // Listen for changes and update Livewire
                        editor.addEventListener('trix-change', (e) => {
                            const htmlContent = trixEditor.getDocument().toString();
                            @this.call('updateChapterTrixContent', htmlContent);
                        });

                        // Listen for content updates from Livewire
                        @this.on('chapter-trix-content-updated', (event) => {
                            if (event.content !== undefined) {
                                trixEditor.loadHTML(event.content);
                            }
                        });
                    });
                },

                updateChapterContent(event) {
                    const htmlContent = event.target.editor.getDocument().toString();
                    @this.call('updateChapterTrixContent', htmlContent);
                }
            }
        }

        // File size checker function
        function checkFileSize(input, maxSizeKB) {
            if (input.files && input.files[0]) {
                const fileSize = input.files[0].size / 1024; // Convert to KB
                if (fileSize > maxSizeKB) {
                    alert(
                        `Il file è troppo grande (${Math.round(fileSize)} KB). Dimensione massima consentita: ${maxSizeKB} KB (${Math.round(maxSizeKB/1024)} MB)`);
                    input.value = ''; // Clear the input
                    return false;
                }
            }
            return true;
        }

        // Multiple files size checker function
        function checkMultipleFileSize(input, maxSizeKB) {
            if (input.files && input.files.length > 0) {
                for (let i = 0; i < input.files.length; i++) {
                    const fileSize = input.files[i].size / 1024; // Convert to KB
                    if (fileSize > maxSizeKB) {
                        alert(
                            `Il file "${input.files[i].name}" è troppo grande (${Math.round(fileSize)} KB). Dimensione massima consentita: ${maxSizeKB} KB (${Math.round(maxSizeKB/1024)} MB)`);
                        input.value = ''; // Clear the input
                        return false;
                    }
                }
            }
            return true;
        }

        // Timeout for loading states to prevent infinite loading
        let loadingTimeout;
        document.addEventListener('livewire:load', function() {
            Livewire.hook('request', ({
                uri,
                options,
                payload,
                respond,
                succeed,
                fail
            }) => {
                // Clear any existing timeout
                if (loadingTimeout) {
                    clearTimeout(loadingTimeout);
                }

                // Set a timeout to force loading to stop after 30 seconds
                loadingTimeout = setTimeout(() => {
                    console.warn('Livewire request timed out, forcing page refresh');
                    location.reload();
                }, 30000);
            });

            Livewire.hook('response', ({
                request,
                response
            }) => {
                // Clear timeout on successful response
                if (loadingTimeout) {
                    clearTimeout(loadingTimeout);
                }
            });
        });

        // Alternative approach - listen for Livewire loading events
        document.addEventListener('livewire:load', function() {
            let uploadTimeout;

            window.addEventListener('livewire:upload-start', () => {
                uploadTimeout = setTimeout(() => {
                    console.warn('Upload timed out, refreshing page');
                    location.reload();
                }, 30000);
            });

            window.addEventListener('livewire:upload-finish', () => {
                if (uploadTimeout) {
                    clearTimeout(uploadTimeout);
                }
            });

            window.addEventListener('livewire:upload-error', () => {
                if (uploadTimeout) {
                    clearTimeout(uploadTimeout);
                }
            });
        });
    </script>
@endpush
