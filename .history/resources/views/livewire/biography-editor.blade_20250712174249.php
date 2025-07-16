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
            <div class="space-y-6" x-data="mediaUploadManager()">
                <h3 class="text-lg font-medium text-white">Gestione Media</h3>

                <!-- Multiple Images Upload with Spatie -->
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-300">
                        Immagini Biography
                    </label>
                    <input type="file" id="multiple-images-input" multiple accept="image/*"
                        class="w-full rounded-lg border border-gray-600 bg-gray-800 px-4 py-3 text-white transition-colors file:mr-4 file:rounded-lg file:border-0 file:bg-[#D4A574] file:px-4 file:py-2 file:text-sm file:font-medium file:text-gray-900 hover:file:bg-[#E6B885]"
                        @change="handleMultipleImagesUpload($event)">
                    <p class="text-xs text-gray-400">
                        Carica tutte le immagini per la tua biografia. Potrai selezionare una come immagine featured e
                        una come avatar.
                    </p>

                    <!-- Loading indicator -->
                    <div x-show="multipleImagesLoading" x-transition
                        class="mt-2 flex items-center space-x-2 rounded-lg bg-[#D4A574]/10 p-3 text-[#D4A574]">
                        <div class="h-4 w-4 animate-spin rounded-full border-b-2 border-[#D4A574]"></div>
                        <span class="text-sm font-medium">🔄 Caricamento immagini in corso...</span>
                    </div>

                    <!-- Error message -->
                    <div x-show="multipleImagesError" x-transition class="mt-1 text-sm text-red-400"
                        x-text="multipleImagesError"></div>

                    <!-- Multiple Images Preview -->
                    <div x-show="allImages.length > 0" x-transition class="mt-6 space-y-4">
                        <h4 class="text-lg font-medium text-white">Immagini Caricate</h4>
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                            <template x-for="(image, index) in allImages" :key="image.id">
                                <div class="space-y-3 rounded-lg border border-gray-600 bg-gray-800/50 p-4">
                                    <div class="relative">
                                        <img :src="image.url" :alt="'Image ' + (index + 1)"
                                            class="h-32 w-full rounded-lg object-cover">
                                        <button @click="removeImage(index)"
                                            class="absolute -right-2 -top-2 flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-white transition-colors hover:bg-red-600">
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Image Options -->
                                    <div class="space-y-3">
                                        <!-- Featured Image Toggle -->
                                        <div class="flex items-center space-x-2">
                                            <input type="radio" :id="'featured-' + image.id" name="featured_image"
                                                :value="image.id" x-model="selectedFeaturedImageId"
                                                class="h-4 w-4 border-gray-600 bg-gray-700 text-[#D4A574] focus:ring-2 focus:ring-[#D4A574]">
                                            <label :for="'featured-' + image.id"
                                                class="cursor-pointer text-sm font-medium text-gray-300">
                                                <span class="flex items-center space-x-2">
                                                    <svg class="h-4 w-4 text-blue-400" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z">
                                                        </path>
                                                    </svg>
                                                    <span>Immagine Featured</span>
                                                </span>
                                            </label>
                                        </div>

                                        <!-- Avatar Toggle -->
                                        <div class="flex items-center space-x-2">
                                            <input type="radio" :id="'avatar-' + image.id" name="avatar_selection"
                                                :value="image.url" x-model="selectedAvatarUrl"
                                                @change="updateAvatar($event.target.value)"
                                                class="h-4 w-4 border-gray-600 bg-gray-700 text-[#D4A574] focus:ring-2 focus:ring-[#D4A574]">
                                            <label :for="'avatar-' + image.id"
                                                class="cursor-pointer text-sm font-medium text-gray-300">
                                                <span class="flex items-center space-x-2">
                                                    <svg class="h-4 w-4 text-[#D4A574]" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                        </path>
                                                    </svg>
                                                    <span>Rendi Avatar</span>
                                                </span>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Image Info -->
                                    <div class="text-xs text-gray-400">
                                        <div x-text="image.file_name"></div>
                                        <div x-text="Math.round(image.size / 1024) + ' KB'"></div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
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

            <!-- Unsaved changes indicator -->
            <div class="unsaved-indicator hidden items-center space-x-2 text-sm text-yellow-400">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z">
                    </path>
                </svg>
                <span>Modifiche non salvate</span>
            </div>
        </div>

        <div class="flex items-center space-x-4">
            <!-- Emergency Reset Button (hidden by default) -->
            <button wire:click="resetUploadState"
                class="emergency-reset inline-flex hidden items-center rounded-lg bg-yellow-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-yellow-700"
                title="Reset stato upload in caso di problemi">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                    </path>
                </svg>
                Reset
            </button>

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

        // Unsaved changes tracking
        let hasUnsavedChanges = false;

        function markUnsavedChanges() {
            hasUnsavedChanges = true;

            // Show unsaved changes indicator
            const indicator = document.querySelector('.unsaved-indicator');
            if (indicator) {
                indicator.classList.remove('hidden');
                indicator.classList.add('flex');
            }

            // Visual indicator - change save button style
            const saveBtn = document.querySelector('[wire\\:click="save"]');
            if (saveBtn) {
                saveBtn.classList.add('ring-2', 'ring-yellow-400', 'ring-offset-2', 'ring-offset-gray-800');
            }
        }

        function markChangesSaved() {
            hasUnsavedChanges = false;

            // Hide unsaved changes indicator
            const indicator = document.querySelector('.unsaved-indicator');
            if (indicator) {
                indicator.classList.add('hidden');
                indicator.classList.remove('flex');
            }

            // Remove visual indicator from save button
            const saveBtn = document.querySelector('[wire\\:click="save"]');
            if (saveBtn) {
                saveBtn.classList.remove('ring-2', 'ring-yellow-400', 'ring-offset-2', 'ring-offset-gray-800');
            }
        }

        // Warn user before leaving page with unsaved changes
        window.addEventListener('beforeunload', function(e) {
            if (hasUnsavedChanges) {
                e.preventDefault();
                e.returnValue = 'Hai modifiche non salvate. Sei sicuro di voler uscire dalla pagina?';
                return e.returnValue;
            }
        });

        // Track form changes on regular inputs too
        document.addEventListener('livewire:load', function() {
            // Track changes on text inputs (excluding file inputs)
            document.addEventListener('input', function(e) {
                if (e.target.matches('[wire\\:model]') && e.target.type !== 'file') {
                    markUnsavedChanges();
                }
            });

            // Track changes on checkboxes and selects (excluding file inputs)
            document.addEventListener('change', function(e) {
                if (e.target.matches('[wire\\:model]') && e.target.type !== 'file') {
                    markUnsavedChanges();
                }
            });

            // Reset unsaved changes flag when save is successful
            @this.on('biographySaved', (event) => {
                markChangesSaved();
                console.log('Changes saved successfully:', event);

                // Show success notification
                const notification = document.createElement('div');
                notification.className =
                    'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50 transition-opacity duration-300';
                notification.textContent = event.message || 'Salvato con successo!';
                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.style.opacity = '0';
                    setTimeout(() => {
                        if (document.body.contains(notification)) {
                            document.body.removeChild(notification);
                        }
                    }, 300);
                }, 3000);
            });

            // Reset changes flag when loading existing content
            @this.on('contentLoaded', () => {
                markChangesSaved();
                console.log('Content loaded, resetting unsaved changes flag');
            });
        });

        // Alpine.js components for Trix integration
        function trixEditor() {
            return {
                content: @entangle('content'),
                hasChanges: false,

                initTrix() {
                    this.$nextTick(() => {
                        const editor = this.$el;
                        const trixEditor = editor.editor;

                        // Set initial content
                        if (this.content) {
                            trixEditor.loadHTML(this.content);
                        }

                        // Listen for changes - NO AUTOMATIC SAVE, just track changes
                        editor.addEventListener('trix-change', (e) => {
                            const htmlContent = trixEditor.getDocument().toString();
                            this.content = htmlContent; // Update local content only
                            this.hasChanges = true;
                            markUnsavedChanges(); // Mark form as dirty
                        });
                    });
                },

                updateContent(event) {
                    // This method is only called manually, not automatically
                    const htmlContent = event.target.editor.getDocument().toString();
                    this.content = htmlContent;
                    this.hasChanges = true;
                    markUnsavedChanges();
                }
            }
        }

        function trixChapterEditor() {
            return {
                chapterContent: undefined,
                hasChanges: false,

                initChapterTrix() {
                    this.$nextTick(() => {
                        const editor = this.$el;
                        const trixEditor = editor.editor;

                        // Set initial content
                        if (this.chapterContent) {
                            trixEditor.loadHTML(this.chapterContent);
                        }

                        // Listen for changes - NO AUTOMATIC SAVE, just track changes
                        editor.addEventListener('trix-change', (e) => {
                            const htmlContent = trixEditor.getDocument().toString();
                            this.chapterContent = htmlContent; // Update local content only
                            this.hasChanges = true;
                            markUnsavedChanges(); // Mark form as dirty
                        });

                        // Listen for content updates from Livewire
                        @this.on('chapter-trix-content-updated', (event) => {
                            if (event.content !== undefined) {
                                trixEditor.loadHTML(event.content);
                                this.hasChanges = false; // Reset changes flag when loading content
                            }
                        });
                    });
                },

                updateChapterContent(event) {
                    // This method is only called manually, not automatically
                    const htmlContent = event.target.editor.getDocument().toString();
                    this.chapterContent = htmlContent;
                    this.hasChanges = true;
                    markUnsavedChanges();
                }
            }
        }

        // File size checker function
        function checkFileSize(input, maxSizeKB) {
            if (input.files && input.files[0]) {
                const fileSize = input.files[0].size / 1024; // Convert to KB
                if (fileSize > maxSizeKB) {
                    alert(
                        `Il file è troppo grande (${Math.round(fileSize)} KB). Dimensione massima consentita: ${maxSizeKB} KB (${Math.round(maxSizeKB/1024)} MB)`
                        );
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
                            `Il file "${input.files[i].name}" è troppo grande (${Math.round(fileSize)} KB). Dimensione massima consentita: ${maxSizeKB} KB (${Math.round(maxSizeKB/1024)} MB)`
                            );
                        input.value = ''; // Clear the input
                        return false;
                    }
                }
            }
            return true;
        }

        // Prevent infinite loading states
        let requestCount = 0;
        let loadingTimeout;

        document.addEventListener('livewire:load', function() {
            // Hook into Livewire requests to prevent infinite loops
            Livewire.hook('request', ({
                uri,
                options,
                payload,
                respond,
                succeed,
                fail
            }) => {
                requestCount++;

                // If too many requests in short time, prevent them
                if (requestCount > 5) {
                    console.warn('Too many Livewire requests, blocking to prevent infinite loop');
                    fail({
                        status: 429,
                        message: 'Too many requests'
                    });
                    return;
                }

                // Clear any existing timeout
                if (loadingTimeout) {
                    clearTimeout(loadingTimeout);
                }

                // Set a timeout to force loading to stop after 15 seconds
                loadingTimeout = setTimeout(() => {
                    console.warn('Livewire request timed out');
                    requestCount = 0; // Reset count
                    if (document.querySelector('[wire\\:loading]')) {
                        // Force hide loading indicators
                        document.querySelectorAll('[wire\\:loading]').forEach(el => {
                            el.style.display = 'none';
                        });
                    }
                }, 15000);
            });

            Livewire.hook('response', ({
                request,
                response
            }) => {
                // Clear timeout and reset counter on successful response
                if (loadingTimeout) {
                    clearTimeout(loadingTimeout);
                }

                // Reset request count after successful response
                setTimeout(() => {
                    requestCount = Math.max(0, requestCount - 1);
                }, 1000);
            });

            Livewire.hook('failed', ({
                request,
                response
            }) => {
                // Clear timeout and reset counter on failed response
                if (loadingTimeout) {
                    clearTimeout(loadingTimeout);
                }
                requestCount = Math.max(0, requestCount - 1);
            });
        });

        // Enhanced upload event handling
        document.addEventListener('livewire:load', function() {
            let uploadTimeout;
            let uploadInProgress = false;

            window.addEventListener('livewire:upload-start', (event) => {
                console.log('Upload started:', event.detail);
                uploadInProgress = true;

                // Show loading state manually
                document.querySelectorAll('[wire\\:loading][wire\\:target*="Image"]').forEach(el => {
                    el.style.display = 'flex';
                });

                uploadTimeout = setTimeout(() => {
                    console.warn('Upload timed out, forcing reset');
                    uploadInProgress = false;

                    // Hide loading indicators
                    document.querySelectorAll('[wire\\:loading]').forEach(el => {
                        el.style.display = 'none';
                    });

                    // Show error message
                    const notification = document.createElement('div');
                    notification.className =
                        'fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
                    notification.textContent =
                        'Upload timeout - riprova con un\'immagine più piccola';
                    document.body.appendChild(notification);

                    setTimeout(() => {
                        if (document.body.contains(notification)) {
                            document.body.removeChild(notification);
                        }
                    }, 5000);

                }, 20000); // 20 seconds timeout
            });

            window.addEventListener('livewire:upload-finish', (event) => {
                console.log('Upload finished:', event.detail);
                uploadInProgress = false;

                if (uploadTimeout) {
                    clearTimeout(uploadTimeout);
                }

                // Ensure loading indicators are hidden
                setTimeout(() => {
                    document.querySelectorAll('[wire\\:loading]').forEach(el => {
                        el.style.display = 'none';
                    });
                }, 500);
            });

            window.addEventListener('livewire:upload-error', (event) => {
                console.error('Upload error:', event.detail);
                uploadInProgress = false;

                if (uploadTimeout) {
                    clearTimeout(uploadTimeout);
                }

                // Hide loading indicators
                document.querySelectorAll('[wire\\:loading]').forEach(el => {
                    el.style.display = 'none';
                });
            });

            // Additional safety: periodically check for stuck uploads
            let uploadStuckCount = 0;
            setInterval(() => {
                if (uploadInProgress) {
                    const visibleLoaders = document.querySelectorAll(
                        '[wire\\:loading]:not([style*="display: none"])');
                    if (visibleLoaders.length > 0) {
                        uploadStuckCount++;
                        console.log('Upload still in progress, checking...', uploadStuckCount);

                        // Show emergency reset button after 10 seconds of stuck upload
                        if (uploadStuckCount >= 2) {
                            const resetBtn = document.querySelector('.emergency-reset');
                            if (resetBtn) {
                                resetBtn.classList.remove('hidden');
                            }
                        }
                    }
                } else {
                    uploadStuckCount = 0;
                    // Hide emergency reset button when upload is not stuck
                    const resetBtn = document.querySelector('.emergency-reset');
                    if (resetBtn) {
                        resetBtn.classList.add('hidden');
                    }
                }
            }, 5000);
        });

        // Media Upload Manager with Spatie Media Library
        function mediaUploadManager() {
            return {
                // Multiple images state
                multipleImagesLoading: false,
                multipleImagesError: '',
                allImages: [],

                // Selection state
                selectedFeaturedImageId: '',
                selectedAvatarUrl: '{{ auth()->user()->avatar_url ?? '' }}',

                // Initialize with existing data
                init() {
                    // Combine all existing images from database
                    this.loadExistingImages();
                },

                // Load existing images from Livewire component data
                loadExistingImages() {
                    this.allImages = [];

                    // Load from mediaData
                    @if ($mediaData && !empty($mediaData))
                        @foreach ($mediaData as $index => $media)
                            this.allImages.push({
                                id: '{{ $media['id'] }}',
                                collection_name: '{{ $media['collection_name'] }}',
                                file_name: '{{ $media['file_name'] }}',
                                url: '{{ $media['url'] }}',
                                size: {{ $media['size'] }},
                                mime_type: '{{ $media['mime_type'] }}',
                                is_existing: true
                            });

                            // Set first featured_image as selected
                            @if ($media['collection_name'] === 'featured_image')
                                if (!this.selectedFeaturedImageId) {
                                    this.selectedFeaturedImageId = '{{ $media['id'] }}';
                                }
                            @endif
                        @endforeach
                    @endif
                },

                // Upload multiple images
                async handleMultipleImagesUpload(event) {
                    const files = Array.from(event.target.files);
                    if (files.length === 0) return;

                    // Reset state
                    this.multipleImagesError = '';
                    this.multipleImagesLoading = true;

                    try {
                        for (const file of files) {
                            // Client-side validation
                            if (!file.type.startsWith('image/')) {
                                this.multipleImagesError = `Il file ${file.name} non è un'immagine`;
                                continue;
                            }

                            if (file.size > 2 * 1024 * 1024) { // 2MB
                                this.multipleImagesError = `L'immagine ${file.name} supera i 2MB`;
                                continue;
                            }

                            // Upload to server usando Spatie collection
                            const formData = new FormData();
                            formData.append('file', file);
                            formData.append('collection', 'main_gallery'); // Use Spatie collection
                            formData.append('biography_id', @this.get('biographyId') || '');

                            const response = await fetch('{{ route('biography.upload-media') }}', {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                        .getAttribute('content'),
                                    'Accept': 'application/json'
                                }
                            });

                            const result = await response.json();

                            if (result.success) {
                                // Add to local array
                                this.allImages.push(result.media);

                                // Update Livewire component with media data
                                @this.call('addMediaData', result.media);
                            } else {
                                this.multipleImagesError = result.message || 'Errore durante l\'upload';
                            }
                        }

                        if (this.multipleImagesError === '') {
                            this.showNotification('success', `${files.length} immagini caricate con successo`);
                        }
                    } catch (error) {
                        console.error('Upload error:', error);
                        this.multipleImagesError = 'Errore di connessione durante l\'upload';
                    } finally {
                        this.multipleImagesLoading = false;
                        event.target.value = ''; // Reset input
                    }
                },

                // Remove image
                removeImage(index) {
                    const image = this.allImages[index];

                    // If this was the selected featured image, clear it
                    if (this.selectedFeaturedImageId === image.id) {
                        this.selectedFeaturedImageId = '';
                    }

                    // If this was the selected avatar, clear it
                    if (this.selectedAvatarUrl === image.url) {
                        this.selectedAvatarUrl = '';
                        this.updateAvatar(''); // Clear avatar on server
                    }

                    // Remove from array
                    this.allImages.splice(index, 1);

                    // Update Livewire component
                    @this.call('removeMediaData', image.id);

                    this.showNotification('success', 'Immagine rimossa');
                },

                // Update user avatar
                async updateAvatar(imageUrl) {
                    if (!imageUrl) return;

                    try {
                        const response = await fetch('{{ route('biography.set-avatar') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                image_url: imageUrl
                            })
                        });

                        const result = await response.json();

                        if (result.success) {
                            this.selectedAvatarUrl = imageUrl;
                            this.showNotification('success', result.message);
                        } else {
                            this.showNotification('error', result.message ||
                                'Errore durante l\'aggiornamento dell\'avatar');
                        }
                    } catch (error) {
                        console.error('Avatar update error:', error);
                        this.showNotification('error', 'Errore di connessione durante l\'aggiornamento dell\'avatar');
                    }
                },

                // Show notification
                showNotification(type, message) {
                    const notification = document.createElement('div');
                    notification.className = `fixed top-4 right-4 px-4 py-2 rounded-lg shadow-lg z-50 transition-opacity duration-300 ${
                        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
                    }`;
                    notification.textContent = message;
                    document.body.appendChild(notification);

                    setTimeout(() => {
                        notification.style.opacity = '0';
                        setTimeout(() => {
                            if (document.body.contains(notification)) {
                                document.body.removeChild(notification);
                            }
                        }, 300);
                    }, 3000);
                }
            }
        }

        // Listen for external events from manage page
        window.addEventListener('biography-reset-form', function() {
            @this.call('resetForm');
        });

        window.addEventListener('biography-load', function(event) {
            @this.call('loadBiography', event.detail.biographyId);
        });

        // Listen for upload completion events from Livewire
        document.addEventListener('livewire:load', function() {
            @this.on('uploadCompleted', (event) => {
                console.log('Upload completed:', event);
            });

            @this.on('uploadError', (event) => {
                console.error('Upload error:', event);
            });
        });
    </script>
@endpush
