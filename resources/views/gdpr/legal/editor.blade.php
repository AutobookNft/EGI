<x-legal-layout>

    <x-slot name="header">
        <h1 class="text-3xl font-bold text-base-content">
            {{ __('legal_editor.title') }}
        </h1>
        <p class="mt-1 text-neutral-500">
            {{ __('legal_editor.subtitle', ['userType' => '<span class="font-semibold text-primary">' . $userType . '</span>', 'locale' => '<span class="font-semibold uppercase text-primary">' . $locale . '</span>']) !!}
        </p>
        <p class="text-sm text-neutral-400">
            {{ __('legal_editor.current_version') }} <span class="font-mono text-accent">{{ $currentVersion }}</span>
        </p>
    </x-slot>

    <div class="container p-4 mx-auto md:p-6 lg:p-8">

        <form action="{{ route('legal.save', ['userType' => $userType, 'locale' => $locale]) }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">

                {{-- Colonna Principale: Editor e Sommario Modifiche --}}
                <div class="lg:col-span-2">
                    <div class="p-6 card bg-base-100 ring-1 ring-base-300">
                        <div class="card-body">
                            <h2 class="mb-4 text-xl font-semibold card-title">{{ __('legal_editor.content_title') }}</h2>

                            {{-- ✅ INTEGRAZIONE: Utilizzo del componente CodeMirror fornito --}}
                            @php
                                // Prepariamo il contenuto per il componente, gestendo il fallback per `old()`
                                // e la formattazione dell'array PHP iniziale.
                                $editorContent = old('content', is_array($currentContent) ? var_export($currentContent, true) : $currentContent);
                            @endphp
                            <x-legal.code-editor name="content" :formatted-content="$editorContent" />


                            <div class="mt-6 form-control">
                                <label for="change_summary" class="label">
                                    <span class="font-semibold label-text">{{ __('legal_editor.summary_label') }}</span>
                                </label>
                                <textarea id="change_summary" name="change_summary"
                                          class="h-32 textarea textarea-bordered"
                                          placeholder="{{ __('legal_editor.summary_placeholder') }}"
                                          required>{{ old('change_summary') }}</textarea>
                                @error('change_summary')
                                    <span class="mt-1 text-xs text-error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Colonna Laterale: Azioni e Storico --}}
                <div class="lg:col-span-1">
                    <div class="sticky p-6 top-8 card bg-base-100 ring-1 ring-base-300">
                        <div class="card-body">
                            <h2 class="mb-4 text-xl font-semibold card-title">{{ __('legal_editor.actions_title') }}</h2>

                            <div class="form-control">
                                <label class="cursor-pointer label">
                                    <span class="label-text">{{ __('legal_editor.publish_label') }}</span>
                                    <input type="checkbox" name="auto_publish" value="1" class="toggle toggle-primary" {{ old('auto_publish') ? 'checked' : '' }} />
                                </label>
                                <p class="mt-1 text-xs text-neutral-500">
                                    {{ __('legal_editor.publish_help') }}
                                </p>
                            </div>

                            <div class="mt-4 form-control">
                                <label for="effective_date" class="label">
                                    <span class="font-semibold label-text">{{ __('legal_editor.effective_date_label', 'Data Entrata in Vigore') }}</span>
                                </label>
                                <input type="date" id="effective_date" name="effective_date" value="{{ old('effective_date') }}"
                                       class="w-full input input-bordered"
                                       min="{{ now()->toDateString() }}">
                                <p class="mt-1 text-xs text-neutral-500">
                                    {{ __('legal_editor.effective_date_help', 'Lascia vuoto per usare la data odierna.') }}
                                </p>
                                @error('effective_date')
                                    <span class="mt-1 text-xs text-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mt-6 card-actions">
                                <button type="submit" class="w-full btn btn-primary">
                                    <span class="material-symbols-outlined">save</span>
                                    {{ __('legal_editor.submit_button') }}
                                </button>
                            </div>

                            <div class="my-6 divider">{{ __('legal_editor.history_title') }}</div>

                            {{-- ✅ INTEGRAZIONE: Utilizzo del componente Version History fornito --}}
                            <x-legal.version-history :versions="$versions" />

                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-legal-layout>
