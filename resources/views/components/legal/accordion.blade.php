{{-- Riscritto per essere auto-contenuto e senza dipendenze esterne --}}
<div class="space-y-4">
    @foreach($items as $item)
        <div class="border shadow-sm accordion-item bg-white/80 backdrop-blur-lg rounded-2xl border-gray-200/50">
            {{-- Aggiunta la classe 'accordion-button' per essere trovato dallo script --}}
            <button class="flex items-center justify-between w-full p-6 text-left accordion-button">
                <span class="text-xl font-bold text-gray-800">{{ $item['number'] ?? '' }} {{ $item['title'] }}</span>
                <span class="text-2xl text-[#8C6A4A] transition-transform duration-300 accordion-arrow">▼</span>
            </button>
            <div class="accordion-content">
                <div class="px-6 pb-6 prose max-w-none prose-p:text-gray-700 prose-strong:text-black prose-strong:font-semibold prose-headings:text-gray-800">
                    {!! \Illuminate\Support\Str::markdown($item['content'] ?? '') !!}

                    @if(isset($item['subsections']))
                        @foreach($item['subsections'] as $subsection)
                            <div class="mt-4 ml-4">
                                <h4 class="text-lg font-bold">{{ $subsection['number'] }} - {{ $subsection['title'] }}</h4>
                                <p>{!! \Illuminate\Support\Str::markdown($subsection['content'] ?? '') !!}</p>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- ✅ SCRIPT GENERICO PER TUTTE LE FISARMONICHE --}}
{{-- Questo script viene aggiunto una sola volta alla pagina e gestirà tutti gli elementi con la classe 'accordion-button' --}}
@once
    @push('scripts')
        <script>
            // Aggiungiamo questo listener una sola volta al body, usando il delegation pattern.
            // È più performante che aggiungere un listener per ogni bottone.
            document.body.addEventListener('click', function(event) {
                // Controlla se l'elemento cliccato (o un suo genitore) è un pulsante di un accordion.
                const button = event.target.closest('.accordion-button');
                if (button) {
                    const content = button.nextElementSibling;
                    button.classList.toggle('open');

                    if (content && content.classList.contains('accordion-content')) {
                        if (content.style.maxHeight) {
                            content.style.maxHeight = null;
                        } else {
                            content.style.maxHeight = content.scrollHeight + 'px';
                        }
                    }
                }
            });
        </script>
    @endpush
@endonce
