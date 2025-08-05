{{-- resources/views/gdpr/export-data.blade.php --}}
<x-gdpr-layout 
    :page-title="__('gdpr.export.title')" 
    :page-description="__('gdpr.export.description')">

@push('styles')
<style>
    .export-form-card {
        background: #fff;
        border-radius: 12px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
    }

    .export-option-group {
        margin-bottom: 2rem;
    }

    .export-option-label {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 1rem;
        display: block;
    }

    .export-options {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1rem;
    }

    .export-option {
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        padding: 1.5rem;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
    }

    .export-option:hover {
        border-color: #3b82f6;
        background: #f8fafc;
    }

    .export-option.selected {
        border-color: #3b82f6;
        background: #eff6ff;
    }

    .export-option input[type="radio"],
    .export-option input[type="checkbox"] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }

    .export-option-title {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .export-option-description {
        color: #6b7280;
        font-size: 0.9rem;
        line-height: 1.5;
    }

    .export-format-icon {
        width: 24px;
        height: 24px;
        padding: 4px;
        border-radius: 4px;
        background: #3b82f6;
        color: white;
        font-size: 12px;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .export-history-section {
        background: #f9fafb;
        border-radius: 12px;
        padding: 2rem;
        margin-top: 2rem;
    }

    .export-history-title {
        font-size: 1.3rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 1.5rem;
    }

    .export-item {
        background: white;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .export-item-info {
        flex: 1;
        min-width: 200px;
    }

    .export-item-title {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.25rem;
    }

    .export-item-details {
        color: #6b7280;
        font-size: 0.9rem;
    }

    .export-item-status {
        padding: 0.25rem 0.75rem;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .export-item-status.completed {
        background: #d1fae5;
        color: #065f46;
    }

    .export-item-status.processing {
        background: #fef3c7;
        color: #92400e;
    }

    .export-item-status.failed {
        background: #fee2e2;
        color: #991b1b;
    }

    .export-item-status.expired {
        background: #f3f4f6;
        color: #6b7280;
    }

    .export-btn {
        padding: 0.5rem 1rem;
        border-radius: 6px;
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 500;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .export-btn-primary {
        background: #3b82f6;
        color: white;
    }

    .export-btn-primary:hover {
        background: #2563eb;
    }

    .export-btn-success {
        background: #10b981;
        color: white;
    }

    .export-btn-success:hover {
        background: #059669;
    }

    .export-btn-secondary {
        background: #6b7280;
        color: white;
    }

    .export-btn-secondary:hover {
        background: #4b5563;
    }

    .export-restrictions {
        background: #fef3c7;
        border: 1px solid #f59e0b;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 2rem;
    }

    .export-restrictions-title {
        font-weight: 600;
        color: #92400e;
        margin-bottom: 0.5rem;
    }

    .export-restrictions-text {
        color: #92400e;
        font-size: 0.9rem;
        line-height: 1.5;
    }
</style>
@endpush

{{-- Page Header --}}
<div class="gdpr-page-header">
    <h1 class="gdpr-page-title">{{ __('gdpr.export.title') }}</h1>
    <p class="gdpr-page-subtitle">{{ __('gdpr.export.subtitle') }}</p>
</div>

{{-- Export Restrictions Notice --}}
@if(!$canRequestExport)
    <div class="export-restrictions">
        <div class="export-restrictions-title">{{ __('gdpr.export.rate_limit_title') }}</div>
        <div class="export-restrictions-text">
            {{ __('gdpr.export.rate_limit_message', ['days' => 30]) }}
            @if(isset($lastExport))
                {{ __('gdpr.export.last_export_date', ['date' => $lastExport->created_at->format('d/m/Y H:i')]) }}
            @endif
        </div>
    </div>
@endif

{{-- New Export Request Form --}}
@if($canRequestExport)
    <form method="POST" action="{{ route('gdpr.export-data.generate') }}" class="export-form-card">
        @csrf

        {{-- Data Categories Selection --}}
        <div class="export-option-group">
            <label class="export-option-label">{{ __('gdpr.export.select_data_categories') }}</label>
            <div class="export-options">
                @foreach($availableCategories as $category => $info)
                    <label class="export-option" onclick="toggleExportOption(this)">
                        <input type="checkbox" name="categories[]" value="{{ $category }}" checked>
                        <div class="export-option-title">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            {{ __($info['name'] ?? 'gdpr.export.categories.' . $category) }}
                        </div>
                        <div class="export-option-description">
                            {{ __('gdpr.export.category_descriptions.' . $category) }}
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Export Format Selection --}}
        <div class="export-option-group">
            <label class="export-option-label">{{ __('gdpr.export.select_format') }}</label>
            <div class="export-options">
                <label class="export-option selected" onclick="selectExportFormat(this)">
                    <input type="radio" name="format" value="json" checked>
                    <div class="export-option-title">
                        <div class="export-format-icon">JSON</div>
                        {{ __('gdpr.export.formats.json') }}
                    </div>
                    <div class="export-option-description">
                        {{ __('gdpr.export.format_descriptions.json') }}
                    </div>
                </label>

                <label class="export-option" onclick="selectExportFormat(this)">
                    <input type="radio" name="format" value="csv">
                    <div class="export-option-title">
                        <div class="export-format-icon">CSV</div>
                        {{ __('gdpr.export.formats.csv') }}
                    </div>
                    <div class="export-option-description">
                        {{ __('gdpr.export.format_descriptions.csv') }}
                    </div>
                </label>

                <label class="export-option" onclick="selectExportFormat(this)">
                    <input type="radio" name="format" value="pdf">
                    <div class="export-option-title">
                        <div class="export-format-icon">PDF</div>
                        {{ __('gdpr.export.formats.pdf') }}
                    </div>
                    <div class="export-option-description">
                        {{ __('gdpr.export.format_descriptions.pdf') }}
                    </div>
                </label>
            </div>
        </div>

        {{-- Additional Options --}}
        <div class="export-option-group">
            <label class="export-option-label">{{ __('gdpr.export.additional_options') }}</label>
            <div class="export-options">
                <label class="export-option" onclick="toggleExportOption(this)">
                    <input type="checkbox" name="include_metadata" value="1">
                    <div class="export-option-title">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        {{ __('gdpr.export.include_metadata') }}
                    </div>
                    <div class="export-option-description">
                        {{ __('gdpr.export.metadata_description') }}
                    </div>
                </label>

                <label class="export-option" onclick="toggleExportOption(this)">
                    <input type="checkbox" name="include_audit_trail" value="1">
                    <div class="export-option-title">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        {{ __('gdpr.export.include_audit_trail') }}
                    </div>
                    <div class="export-option-description">
                        {{ __('gdpr.export.audit_trail_description') }}
                    </div>
                </label>
            </div>
        </div>

        {{-- Submit Button --}}
        <div style="text-align: center; margin-top: 2rem;">
            <button type="submit" class="export-btn export-btn-primary" style="font-size: 1rem; padding: 0.75rem 2rem;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                {{ __('gdpr.export.request_export') }}
            </button>
        </div>
    </form>
@endif

{{-- Export History --}}
<div class="export-history-section">
    <h3 class="export-history-title">{{ __('gdpr.export.history_title') }}</h3>

    @if($exportHistory && $exportHistory->count() > 0)
        @foreach($exportHistory as $export)
            <div class="export-item">
                <div class="export-item-info">
                    <div class="export-item-title">
                        {{ __('gdpr.export.export_format', ['format' => strtoupper($export['format'])]) }}
                    </div>
                    <div class="export-item-details">
                        {{ __('gdpr.export.requested_on') }}: {{ \Carbon\Carbon::parse($export['created_at'])->format('d/m/Y H:i') }}
                        @if(isset($export['completed_at']) && $export['completed_at'])
                            | {{ __('gdpr.export.completed_on') }}: {{ \Carbon\Carbon::parse($export['completed_at'])->format('d/m/Y H:i') }}
                        @endif
                        @if(isset($export['file_size']) && $export['file_size'])
                            | {{ __('gdpr.export.file_size') }}: {{ number_format($export['file_size'] / 1024, 2) }} KB
                        @endif
                        @if(isset($export['expires_at']) && $export['expires_at'])
                            | {{ __('gdpr.export.expires_on') }}: {{ \Carbon\Carbon::parse($export['expires_at'])->format('d/m/Y H:i') }}
                        @endif
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 1rem;">
                    <span class="export-item-status {{ $export['status'] }}">
                        {{ __('gdpr.export.status.' . $export['status']) }}
                    </span>

                    @if($export['status'] === 'completed' && !$export['is_expired'])
                        <a href="{{ route('gdpr.export-data.download', $export['token']) }}"
                           class="export-btn export-btn-success">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            {{ __('gdpr.export.download') }}
                        </a>
                    @elseif($export['status'] === 'processing')
                        <button class="export-btn export-btn-secondary" disabled>
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ __('gdpr.export.processing') }}
                        </button>
                    @elseif($export['status'] === 'failed')
                        <form method="POST" action="{{ route('gdpr.export-data.generate') }}" style="display: inline;">
                            @csrf
                            <input type="hidden" name="format" value="{{ $export['format'] }}">
                            @foreach($export['categories'] as $category)
                                <input type="hidden" name="categories[]" value="{{ $category }}">
                            @endforeach
                            <button type="submit" class="export-btn export-btn-primary">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                {{ __('gdpr.export.retry') }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    @else
        <div style="text-align: center; padding: 3rem; color: #6b7280;">
            <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 style="margin-bottom: 1rem;">{{ __('gdpr.export.no_exports') }}</h3>
            <p>{{ __('gdpr.export.no_exports_description') }}</p>
        </div>
    @endif
</div>

@push('scripts')
<script>
    /**
     * Toggle export option selection (checkboxes)
     */
    function toggleExportOption(element) {
        const checkbox = element.querySelector('input[type="checkbox"]');
        if (checkbox) {
            checkbox.checked = !checkbox.checked;
            element.classList.toggle('selected', checkbox.checked);
        }
    }

    /**
     * Select export format (radio buttons)
     */
    function selectExportFormat(element) {
        // Remove selected class from all format options
        document.querySelectorAll('.export-option input[type="radio"]').forEach(radio => {
            radio.closest('.export-option').classList.remove('selected');
        });

        // Add selected class to clicked option
        element.classList.add('selected');
        const radio = element.querySelector('input[type="radio"]');
        if (radio) {
            radio.checked = true;
        }
    }

    /**
     * Auto-refresh export status
     */
    function refreshExportStatus() {
        const processingExports = document.querySelectorAll('.export-item-status.processing');
        if (processingExports.length > 0) {
            // Reload page to check for status updates
            setTimeout(() => {
                window.location.reload();
            }, 30000); // Refresh every 30 seconds
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        console.log('[GDPR Export] Page initialized');

        // Initialize export option selections
        document.querySelectorAll('.export-option input[type="checkbox"]:checked').forEach(checkbox => {
            checkbox.closest('.export-option').classList.add('selected');
        });

        // Start auto-refresh for processing exports
        refreshExportStatus();

        // Form validation
        const form = document.querySelector('form[action*="export-data.generate"]');
        if (form) {
            form.addEventListener('submit', function(e) {
                const selectedCategories = form.querySelectorAll('input[name="categories[]"]:checked');
                if (selectedCategories.length === 0) {
                    e.preventDefault();
                    alert('{{ __('gdpr.export.select_at_least_one_category') }}');
                    return false;
                }
            });
        }
    });
</script>
@endpush

</x-gdpr-layout>