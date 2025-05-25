<script>

    window.allowedExtensions = @json(config('AllowedFileType.collection.allowed_extensions', []));
    window.allowedMimeTypes = @json(config('AllowedFileType.collection.allowed_mime_types', []));
    window.maxSize = {{ config('AllowedFileType.collection.max_size', 10 * 1024 * 1024) }};

    // Caricamento configurazione
    fetch('{{ route("global.config") }}', {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, {{-- Corretto a 'csrf-token' --}}
        }
    })
    .then(response => response.json())
    .then(config => {
        Object.assign(window, config);
        document.dispatchEvent(new Event('configLoaded'));
    })
    .catch(error => console.error('Error loading configuration:', error))

    // --- Configurazione Animazione ---
    
</script>

<!-- <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script> -->


