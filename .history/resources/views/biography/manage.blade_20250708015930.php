<x-app-layout>
    <x-slot name="title">{{ __('biography.manage.title') }}</x-slot>

    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900">
        <!-- Header Section -->
        <div class="relative overflow-hidden bg-gradient-to-r from-[#1B365D] to-[#2D5016]">
            <div class="absolute inset-0 bg-black/20"></div>
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="text-center">
                    <h1 class="text-4xl font-bold text-white mb-4 font-serif">
                        {{ __('biography.manage.title') }}
                    </h1>
                    <p class="text-xl text-[#D4A574] max-w-3xl mx-auto">
                        {{ __('biography.manage.subtitle') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Action Bar -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
                <div>
                    <h2 class="text-2xl font-semibold text-white">
                        {{ __('biography.manage.your_biographies') }}
                    </h2>
                    <p class="text-gray-400 mt-1">
                        {{ __('biography.manage.description') }}
                    </p>
                </div>

                <button id="create-biography-btn"
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-[#D4A574] to-[#E6B885] text-gray-900 font-semibold rounded-lg shadow-lg hover:from-[#E6B885] hover:to-[#D4A574] transition-all duration-200 transform hover:scale-105">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    {{ __('biography.manage.create_new') }}
                </button>
            </div>

            <!-- Biographies Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($biographies as $biography)
                    <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl overflow-hidden hover:border-[#D4A574]/30 transition-all duration-300 group">
                        <!-- Biography Header -->
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <h3 class="text-xl font-semibold text-white group-hover:text-[#D4A574] transition-colors">
                                        {{ $biography->title }}
                                    </h3>
                                    <p class="text-sm text-gray-400 mt-1">
                                        {{ __('biography.type.' . $biography->type) }}
                                    </p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    @if($biography->is_public)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ __('biography.public') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ __('biography.private') }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Biography Preview -->
                            <div class="mb-4">
                                <p class="text-gray-300 text-sm line-clamp-3">
                                    {{ $biography->excerpt ?: Str::limit(strip_tags($biography->content), 120) }}
                                </p>
                            </div>

                            <!-- Stats -->
                            <div class="flex items-center justify-between text-sm text-gray-400 mb-4">
                                <div class="flex items-center space-x-4">
                                    @if($biography->type === 'chapters')
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            {{ $biography->chapters->count() }} {{ __('biography.chapters') }}
                                        </span>
                                    @endif
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
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
                                        class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-[#1B365D] text-white text-sm font-medium rounded-lg hover:bg-[#2D5016] transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    {{ __('biography.edit') }}
                                </button>

                                <button onclick="viewBiography({{ $biography->id }})"
                                        class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-gray-700 text-white text-sm font-medium rounded-lg hover:bg-gray-600 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    {{ __('biography.view') }}
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <!-- Empty State -->
                    <div class="col-span-full">
                        <div class="text-center py-12">
                            <div class="mx-auto w-24 h-24 bg-gray-800 rounded-full flex items-center justify-center mb-6">
                                <svg class="w-12 h-12 text-[#D4A574]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-white mb-2">
                                {{ __('biography.manage.empty_title') }}
                            </h3>
                            <p class="text-gray-400 mb-6 max-w-md mx-auto">
                                {{ __('biography.manage.empty_description') }}
                            </p>
                            <button id="create-first-biography-btn"
                                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-[#D4A574] to-[#E6B885] text-gray-900 font-semibold rounded-lg shadow-lg hover:from-[#E6B885] hover:to-[#D4A574] transition-all duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
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
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="bg-gray-900 border border-gray-700 rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-700">
                <h3 class="text-xl font-semibold text-white" id="modal-title">
                    {{ __('biography.create.title') }}
                </h3>
                <button id="close-modal" class="text-gray-400 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                <form id="biography-form" class="space-y-6">
                    @csrf
                    <input type="hidden" id="biography-id" name="id">

                    <!-- Basic Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-300 mb-2">
                                {{ __('biography.form.title') }} *
                            </label>
                            <input type="text" id="title" name="title" required
                                   class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:border-[#D4A574] focus:ring-1 focus:ring-[#D4A574] transition-colors">
                        </div>

                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-300 mb-2">
                                {{ __('biography.form.type') }} *
                            </label>
                            <select id="type" name="type" required
                                    class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white focus:border-[#D4A574] focus:ring-1 focus:ring-[#D4A574] transition-colors">
                                <option value="single">{{ __('biography.type.single') }}</option>
                                <option value="chapters">{{ __('biography.type.chapters') }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- Excerpt -->
                    <div>
                        <label for="excerpt" class="block text-sm font-medium text-gray-300 mb-2">
                            {{ __('biography.form.excerpt') }}
                        </label>
                        <textarea id="excerpt" name="excerpt" rows="3"
                                  class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:border-[#D4A574] focus:ring-1 focus:ring-[#D4A574] transition-colors"
                                  placeholder="{{ __('biography.form.excerpt_placeholder') }}"></textarea>
                    </div>

                    <!-- Content Editor -->
                    <div>
                        <label for="content" class="block text-sm font-medium text-gray-300 mb-2">
                            {{ __('biography.form.content') }}
                        </label>
                        <div id="editor-container" class="border border-gray-600 rounded-lg overflow-hidden">
                            <div id="editor-toolbar" class="bg-gray-800 border-b border-gray-600 p-2 flex flex-wrap gap-2">
                                <button type="button" data-command="bold" class="p-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M12.6 18c-.4 0-.8-.1-1.1-.4l-6-6c-.6-.6-.6-1.5 0-2.1l6-6c.6-.6 1.5-.6 2.1 0 .6.6.6 1.5 0 2.1L8.7 10l5.9 5.9c.6.6.6 1.5 0 2.1-.3.3-.7.4-1.1.4z"/>
                                    </svg>
                                </button>
                                <button type="button" data-command="italic" class="p-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 4a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 01.894 1.79l-1.233.616 1.738 5.42a1 1 0 01-.285 1.05A6.972 6.972 0 0110 18a6.972 6.972 0 01-5.671-2.83 1 1 0 01-.285-1.05l1.738-5.42-1.233-.616a1 1 0 01.894-1.79l1.599.8L9 6.323V5a1 1 0 011-1z"/>
                                    </svg>
                                </button>
                                <button type="button" data-command="underline" class="p-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                                <div class="w-px h-6 bg-gray-600"></div>
                                <button type="button" data-command="insertUnorderedList" class="p-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                                <button type="button" data-command="insertOrderedList" class="p-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            </div>
                            <div id="editor" contenteditable="true"
                                 class="min-h-[300px] p-4 bg-gray-800 text-white focus:outline-none"
                                 placeholder="{{ __('biography.form.content_placeholder') }}"></div>
                        </div>
                        <input type="hidden" id="content" name="content">
                    </div>

                    <!-- Settings -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex items-center">
                            <input type="checkbox" id="is_public" name="is_public" class="w-4 h-4 text-[#D4A574] bg-gray-800 border-gray-600 rounded focus:ring-[#D4A574] focus:ring-2">
                            <label for="is_public" class="ml-2 text-sm text-gray-300">
                                {{ __('biography.form.is_public') }}
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" id="is_completed" name="is_completed" class="w-4 h-4 text-[#D4A574] bg-gray-800 border-gray-600 rounded focus:ring-[#D4A574] focus:ring-2">
                            <label for="is_completed" class="ml-2 text-sm text-gray-300">
                                {{ __('biography.form.is_completed') }}
                            </label>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end space-x-3 p-6 border-t border-gray-700">
                <button id="cancel-btn" class="px-4 py-2 text-gray-400 hover:text-white transition-colors">
                    {{ __('common.cancel') }}
                </button>
                <button id="save-btn" class="px-6 py-2 bg-gradient-to-r from-[#D4A574] to-[#E6B885] text-gray-900 font-semibold rounded-lg hover:from-[#E6B885] hover:to-[#D4A574] transition-all duration-200">
                    {{ __('common.save') }}
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Biography Management JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('biography-modal');
    const form = document.getElementById('biography-form');
    const editor = document.getElementById('editor');
    const contentInput = document.getElementById('content');

    // Initialize rich text editor
    initEditor();

    // Event listeners
    document.getElementById('create-biography-btn').addEventListener('click', () => openModal());
    document.getElementById('create-first-biography-btn').addEventListener('click', () => openModal());
    document.getElementById('close-modal').addEventListener('click', closeModal);
    document.getElementById('cancel-btn').addEventListener('click', closeModal);
    document.getElementById('save-btn').addEventListener('click', saveBiography);

    // Close modal on outside click
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });

    function openModal(biography = null) {
        if (biography) {
            // Edit mode
            document.getElementById('modal-title').textContent = '{{ __("biography.edit.title") }}';
            document.getElementById('biography-id').value = biography.id;
            document.getElementById('title').value = biography.title;
            document.getElementById('type').value = biography.type;
            document.getElementById('excerpt').value = biography.excerpt || '';
            editor.innerHTML = biography.content || '';
            document.getElementById('is_public').checked = biography.is_public;
            document.getElementById('is_completed').checked = biography.is_completed;
        } else {
            // Create mode
            document.getElementById('modal-title').textContent = '{{ __("biography.create.title") }}';
            form.reset();
            editor.innerHTML = '';
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
        contentInput.value = editor.innerHTML;

        // Submit form
        const formData = new FormData(form);

        fetch('/api/biographies', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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
        document.querySelectorAll('#editor-toolbar button').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const command = button.dataset.command;
                document.execCommand(command, false, null);
                editor.focus();
            });
        });

        // Auto-save content to hidden input
        editor.addEventListener('input', () => {
            contentInput.value = editor.innerHTML;
        });
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
