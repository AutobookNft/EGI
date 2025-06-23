{{--
    Questo componente incapsula l'editor CodeMirror.
    Utilizza Alpine.js per inizializzare la libreria in modo pulito e contestualizzato.
--}}

{{--
    NOTA BENE: Questi link CDN sono per un'implementazione rapida.
    In produzione, dovresti gestire CodeMirror tramite npm/Vite e includerlo nel tuo app.js
    per un'ottimizzazione migliore delle risorse.
--}}
@once
    @push('styles')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/theme/material-darker.min.css">
    @endpush
    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/php/php.min.js"></script>
    @endpush
@endonce

<div
    x-data="{
        initEditor() {
            const editor = CodeMirror.fromTextArea(this.$refs.editor, {
                lineNumbers: true,
                mode: 'php',
                theme: 'material-darker',
                matchBrackets: true,
                indentUnit: 4,
                tabSize: 4,
            });

            // Assicura che la textarea sottostante sia sempre aggiornata
            editor.on('change', () => {
                this.$refs.editor.value = editor.getValue();
            });
        }
    }"
    x-init="initEditor()"
    wire:ignore {{-- Importante se usi Livewire per evitare che ricarichi l'editor --}}
>
    <textarea x-ref="editor" name="{{ $name }}" class="hidden">{{ $formattedContent }}</textarea>
</div>
