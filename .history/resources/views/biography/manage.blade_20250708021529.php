<x-app-layout>
    <x-slot name="title">{{ __('biography.manage.title') }}</x-slot>

    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900">
        <!-- Header Section -->
        <div class="relative overflow-hidden bg-gradient-to-r from-[#1B365D] to-[#2D5016]">
            <div class="absolute inset-0 bg-black/20"></div>
            <div class="relative mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h1 class="mb-4 font-serif text-4xl font-bold text-white">
                        {{ __('biography.manage.title') }}
                    </h1>
                    <p class="mx-auto max-w-3xl text-xl text-[#D4A574]">
                        {{ __('biography.manage.subtitle') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- Action Bar -->
            <div class="mb-8 flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
                <div>
                    <h2 class="text-2xl font-semibold text-white">
                        {{ __('biography.manage.your_biographies') }}
                    </h2>
                    <p class="mt-1 text-gray-400">
                        {{ __('biography.manage.description') }}
                    </p>
                </div>

                <button id="create-biography-btn"
                    class="inline-flex transform items-center rounded-lg bg-gradient-to-r from-[#D4A574] to-[#E6B885] px-6 py-3 font-semibold text-gray-900 shadow-lg transition-all duration-200 hover:scale-105 hover:from-[#E6B885] hover:to-[#D4A574]">
                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    {{ __('biography.manage.create_new') }}
                </button>
            </div>

            <!-- Biographies Grid -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                @forelse($biographies as $biography)
                    <div
                        class="group overflow-hidden rounded-xl border border-gray-700 bg-gray-800/50 backdrop-blur-sm transition-all duration-300 hover:border-[#D4A574]/30">
                        <!-- Biography Header -->
                        <div class="p-6">
                            <div class="mb-4 flex items-start justify-between">
                                <div class="flex-1">
                                    <h3
                                        class="text-xl font-semibold text-white transition-colors group-hover:text-[#D4A574]">
                                        {{ $biography->title }}
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-400">
                                        {{ __('biography.type.' . $biography->type) }}
                                    </p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    @if ($biography->is_public)
                                        <span
                                            class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                                            <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                            {{ __('biography.public') }}
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800">
                                            <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                            {{ __('biography.private') }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Biography Preview -->
                            <div class="mb-4">
                                <p class="line-clamp-3 text-sm text-gray-300">
                                    {{ $biography->excerpt ?: Str::limit(strip_tags($biography->content), 120) }}
                                </p>
                            </div>

                            <!-- Stats -->
                            <div class="mb-4 flex items-center justify-between text-sm text-gray-400">
                                <div class="flex items-center space-x-4">
                                    @if ($biography->type === 'chapters')
                                        <span class="flex items-center">
                                            <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                </path>
                                            </svg>
                                            {{ $biography->chapters->count() }} {{ __('biography.chapters') }}
                                        </span>
                                    @endif
                                    <span class="flex items-center">
                                        <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $biography->getEstimatedReadingTime() }} {{ __('biography.min_read') }}
                                    </span>
                                </div>
                                <span class="text-xs">
                                    {{ $biography->updated_at->diffForHumans() }}
                                </span>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center space-x-2">
                                <button onclick="editBiography({{ $biography->id }})"
                                    class="inline-flex flex-1 items-center justify-center rounded-lg bg-[#1B365D] px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-[#2D5016]">
                                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                        </path>
                                    </svg>
                                    {{ __('biography.edit') }}
                                </button>

                                <button onclick="viewBiography({{ $biography->id }})"
                                    class="inline-flex flex-1 items-center justify-center rounded-lg bg-gray-700 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-gray-600">
                                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                    {{ __('biography.view') }}
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <!-- Empty State -->
                    <div class="col-span-full">
                        <div class="py-12 text-center">
                            <div
                                class="mx-auto mb-6 flex h-24 w-24 items-center justify-center rounded-full bg-gray-800">
                                <svg class="h-12 w-12 text-[#D4A574]" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="mb-2 text-xl font-semibold text-white">
                                {{ __('biography.manage.empty_title') }}
                            </h3>
                            <p class="mx-auto mb-6 max-w-md text-gray-400">
                                {{ __('biography.manage.empty_description') }}
                            </p>
                            <button id="create-first-biography-btn"
                                class="inline-flex items-center rounded-lg bg-gradient-to-r from-[#D4A574] to-[#E6B885] px-6 py-3 font-semibold text-gray-900 shadow-lg transition-all duration-200 hover:from-[#E6B885] hover:to-[#D4A574]">
                                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                {{ __('biography.manage.create_first') }}
                            </button>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>

<!-- Biography Editor Modal -->
<div id="biography-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
    <div class="relative flex min-h-screen items-center justify-center p-4">
        <div
            class="max-h-[90vh] w-full max-w-4xl overflow-hidden rounded-xl border border-gray-700 bg-gray-900 shadow-2xl">
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-gray-700 p-6">
                <h3 class="text-xl font-semibold text-white" id="modal-title">
                    {{ __('biography.create.title') }}
                </h3>
                <button id="close-modal" class="text-gray-400 transition-colors hover:text-white">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="max-h-[calc(90vh-120px)] overflow-y-auto p-6">
                <form id="biography-form" class="space-y-6" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="biography-id" name="id">

                    <!-- Basic Info -->
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label for="title" class="mb-2 block text-sm font-medium text-gray-300">
                                {{ __('biography.form.title') }} *
                            </label>
                            <input type="text" id="title" name="title" required
                                class="w-full rounded-lg border border-gray-600 bg-gray-800 px-4 py-3 text-white placeholder-gray-400 transition-colors focus:border-[#D4A574] focus:ring-1 focus:ring-[#D4A574]">
                        </div>

                        <div>
                            <label for="type" class="mb-2 block text-sm font-medium text-gray-300">
                                {{ __('biography.form.type') }} *
                            </label>
                            <select id="type" name="type" required
                                class="w-full rounded-lg border border-gray-600 bg-gray-800 px-4 py-3 text-white transition-colors focus:border-[#D4A574] focus:ring-1 focus:ring-[#D4A574]">
                                <option value="single">{{ __('biography.type.single') }}</option>
                                <option value="chapters">{{ __('biography.type.chapters') }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- Trix Editor for Content -->
                    <div>
                        <label for="content" class="mb-2 block text-sm font-medium text-gray-300">
                            {{ __('biography.form.content') }} *
                        </label>
                        <input id="content" type="hidden" name="content">
                        <trix-editor input="content"
                            class="trix-content rounded-lg border border-gray-600 bg-gray-800 text-white"></trix-editor>
                    </div>

                    <!-- Image Upload (handled by Trix) -->
                    <!-- Le immagini saranno gestite tramite upload diretto da Trix -->

                    <div class="flex justify-end">
                        <button type="submit"
                            class="inline-flex items-center rounded-lg bg-gradient-to-r from-[#D4A574] to-[#E6B885] px-6 py-3 font-semibold text-gray-900 shadow-lg transition-all duration-200 hover:from-[#E6B885] hover:to-[#D4A574]">
                            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            {{ __('biography.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        // Biography Management JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('biography-modal');
            const form = document.getElementById('biography-form');
            const editor = document.getElementById('editor'); // This will be replaced by Trix
            const contentInput = document.getElementById('content'); // This will be replaced by Trix

            // Initialize rich text editor
            initEditor();

            // Event listeners
            document.getElementById('create-biography-btn').addEventListener('click', () => openModal());
            document.getElementById('create-first-biography-btn').addEventListener('click', () => openModal());
            document.getElementById('close-modal').addEventListener('click', closeModal);
            document.getElementById('cancel-btn').addEventListener('click',
            closeModal); // This button is removed from the new HTML
            document.getElementById('save-btn').addEventListener('click',
            saveBiography); // This button is removed from the new HTML

            // Close modal on outside click
            modal.addEventListener('click', (e) => {
                if (e.target === modal) closeModal();
            });

            function openModal(biography = null) {
                if (biography) {
                    // Edit mode
                    document.getElementById('modal-title').textContent = '{{ __('biography.edit.title') }}';
                    document.getElementById('biography-id').value = biography.id;
                    document.getElementById('title').value = biography.title;
                    document.getElementById('type').value = biography.type;
                    document.getElementById('excerpt').value = biography.excerpt || '';
                    // editor.innerHTML = biography.content || ''; // This line is no longer needed
                    document.getElementById('is_public').checked = biography.is_public;
                    document.getElementById('is_completed').checked = biography.is_completed;
                } else {
                    // Create mode
                    document.getElementById('modal-title').textContent = '{{ __('biography.create.title') }}';
                    form.reset();
                    // editor.innerHTML = ''; // This line is no longer needed
                }

                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function closeModal() {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }

            function saveBiography() {
                // Update hidden content input with editor content
                // contentInput.value = editor.innerHTML; // This line is no longer needed

                // Submit form
                const formData = new FormData(form);

                fetch('/api/biographies', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content'),
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(Object.fromEntries(formData))
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            closeModal();
                            window.location.reload();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while saving the biography.');
                    });
            }

            function initEditor() {
                // Toolbar functionality
                // document.querySelectorAll('#editor-toolbar button').forEach(button => { // This block is no longer needed
                //     button.addEventListener('click', (e) => {
                //         e.preventDefault();
                //         const command = button.dataset.command;
                //         document.execCommand(command, false, null);
                //         editor.focus();
                //     });
                // });

                // Auto-save content to hidden input
                // editor.addEventListener('input', () => { // This line is no longer needed
                //     contentInput.value = editor.innerHTML;
                // });
            }
        });

        // Global functions for biography actions
        function editBiography(id) {
            // Fetch biography data and open modal
            fetch(`/api/biographies/${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        openModal(data.data);
                    }
                });
        }

        function viewBiography(id) {
            window.location.href = `/biography/view?id=${id}`;
        }
    </script>
@endpush

@push('styles')
    <style>
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        #editor:empty:before {
            content: attr(placeholder);
            color: #9CA3AF;
            pointer-events: none;
        }

        #editor:focus {
            outline: none;
        }

        #editor-toolbar button.active {
            background-color: #D4A574;
            color: white;
        }
    </style>
@endpush
