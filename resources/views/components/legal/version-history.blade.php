<div class="space-y-4">
    @forelse ($versions as $versionData)
        <div class="p-3 text-sm border-l-4 rounded-r-lg bg-base-200/50 border-primary/50">
            <div class="flex items-center justify-between">
                <p class="font-bold text-base-content">{{ __('legal_editor.history.version') }} {{ $versionData['version'] }}</p>
                <p class="text-xs text-neutral-500">{{ \Carbon\Carbon::parse($versionData['metadata']['release_date'])->format('d/m/Y') }}</p>
            </div>
            <div class="mt-2 text-xs text-neutral-500">
                <p>
                    <span class="font-semibold">{{ __('legal_editor.history.author') }}:</span> {{ $versionData['metadata']['created_by'] }}
                </p>
                <p class="mt-1">
                    <span class="font-semibold">{{ __('legal_editor.history.summary') }}:</span>
                    <em class="italic">"{{ $versionData['metadata']['summary_of_changes'] }}"</em>
                </p>
            </div>
        </div>
    @empty
        <div class="p-4 text-center text-neutral-500">
            <p>{{ __('legal_editor.history.no_versions') }}</p>
        </div>
    @endforelse
</div>
