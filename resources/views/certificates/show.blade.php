{{-- resources/views/certificates/show.blade.php --}}
<x-guest-layout
    :title="__('certificate.page_title', ['uuid' => $certificate->certificate_uuid])"
    :metaDescription="__('certificate.meta_description', ['type' => ucfirst($certificate->reservation_type), 'title' => $certificate->egi->title ?? __('certificate.unknown_egi')])">

{{-- Schema.org nel head --}}
<x-slot name="schemaMarkup">
    @php
    $certificateUrl = route('egi-certificates.show', $certificate->certificate_uuid);
    $verificationUrl = route('egi-certificates.verify', $certificate->certificate_uuid);
    $egiImageUrl = $certificate->egi && $certificate->egi->collection_id && $certificate->egi->user_id && $certificate->egi->key_file && $certificate->egi->extension
        ? asset(sprintf('storage/users_files/collections_%d/creator_%d/%d.%s',
            $certificate->egi->collection_id, $certificate->egi->user_id, $certificate->egi->key_file, $certificate->egi->extension))
        : asset('images/default_egi_placeholder.jpg');
    @endphp
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "DigitalDocument",
        "name": "{{ __('certificate.page_title', ['uuid' => $certificate->certificate_uuid]) }}",
        "description": "{{ __('certificate.meta_description', ['type' => ucfirst($certificate->reservation_type), 'title' => $certificate->egi->title ?? __('certificate.unknown_egi')]) }}",
        "url": "{{ $certificateUrl }}",
        "dateCreated": "{{ $certificate->created_at->toIso8601String() }}",
        "provider": {
            "@type": "Organization",
            "name": "FlorenceEGI",
            "url": "{{ url('/') }}"
        },
        "about": {
            "@type": "VisualArtwork",
            "name": "{{ $certificate->egi->title ?? __('certificate.unknown_egi') }}",
            "image": "{{ $egiImageUrl }}"
        },
        "potentialAction": {
            "@type": "ViewAction",
            "target": "{{ $verificationUrl }}"
        }
    }
    </script>
</x-slot>

{{-- Slot personalizzato per disabilitare la hero section --}}
<x-slot name="noHero">true</x-slot>

{{-- Contenuto principale --}}
<x-slot name="slot">
    <div class="relative z-20 py-12">
        <div class="container px-4 mx-auto sm:px-6 lg:px-8">
            {{-- Alert di successo (mostrato se è appena stata creata una prenotazione) --}}
            @if(session('success'))
            <div class="relative px-4 py-3 mb-8 text-green-700 bg-green-100 border border-green-400 rounded" role="alert">
                <strong class="font-bold">{{ __('certificate.success_message') }}</strong>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg class="w-6 h-6 text-green-500 fill-current" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <title>{{ __('Close') }}</title>
                        <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                    </svg>
                </span>
            </div>
            @endif

            <div class="overflow-hidden bg-white rounded-lg shadow-lg">
                {{-- Header --}}
                <div class="p-6 bg-gradient-to-r from-indigo-500 to-purple-600">
                    <div class="flex items-center justify-between">
                        <h1 class="text-2xl font-bold text-white truncate">
                            {{ __('certificate.page_title', ['uuid' => $certificate->certificate_uuid]) }}
                        </h1>
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                            @if($certificate->reservation_type === 'strong') bg-blue-100 text-blue-800 @else bg-orange-100 text-orange-800 @endif">
                            {{ __('reservation.type.' . $certificate->reservation_type) }}
                        </span>
                    </div>
                    <p class="mt-2 text-indigo-100">
                        {{ $certificate->created_at->diffForHumans() }}
                    </p>
                </div>

                {{-- Contenuto principale --}}
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                        {{-- Colonna sinistra: Dettagli del certificato --}}
                        <div>
                            <h2 class="mb-4 text-xl font-semibold text-gray-800">{{ __('certificate.details.title') }}</h2>

                            <dl class="space-y-4">
                                <div class="grid grid-cols-3 gap-4">
                                    <dt class="text-sm font-medium text-gray-500">{{ __('certificate.details.egi_title') }}</dt>
                                    <dd class="col-span-2 text-sm text-gray-900">{{ $certificate->egi->title ?? __('certificate.unknown_egi') }}</dd>
                                </div>

                                <div class="grid grid-cols-3 gap-4">
                                    <dt class="text-sm font-medium text-gray-500">{{ __('certificate.details.collection') }}</dt>
                                    <dd class="col-span-2 text-sm text-gray-900">{{ $certificate->egi->collection->collection_name ?? '-' }}</dd>
                                </div>

                                <div class="grid grid-cols-3 gap-4">
                                    <dt class="text-sm font-medium text-gray-500">{{ __('certificate.details.reservation_type') }}</dt>
                                    <dd class="col-span-2 text-sm text-gray-900">{{ __('reservation.type.' . $certificate->reservation_type) }}</dd>
                                </div>

                                <div class="grid grid-cols-3 gap-4">
                                    <dt class="text-sm font-medium text-gray-500">{{ __('certificate.details.wallet_address') }}</dt>
                                    <dd class="col-span-2 font-mono text-xs text-sm text-gray-900 break-all">{{ $certificate->wallet_address }}</dd>
                                </div>

                                <div class="grid grid-cols-3 gap-4">
                                    <dt class="text-sm font-medium text-gray-500">{{ __('certificate.details.offer_amount_eur') }}</dt>
                                    <dd class="col-span-2 text-sm text-gray-900">€{{ number_format($certificate->offer_amount_eur, 2) }}</dd>
                                </div>

                                <div class="grid grid-cols-3 gap-4">
                                    <dt class="text-sm font-medium text-gray-500">{{ __('certificate.details.offer_amount_algo') }}</dt>
                                    <dd class="col-span-2 text-sm text-gray-900">{{ number_format($certificate->offer_amount_algo, 8) }} ALGO</dd>
                                </div>

                                <div class="grid grid-cols-3 gap-4">
                                    <dt class="text-sm font-medium text-gray-500">{{ __('certificate.details.created_at') }}</dt>
                                    <dd class="col-span-2 text-sm text-gray-900">{{ $certificate->created_at->format('d M Y H:i:s') }}</dd>
                                </div>

                                <div class="grid grid-cols-3 gap-4">
                                    <dt class="text-sm font-medium text-gray-500">{{ __('certificate.details.status') }}</dt>
                                    <dd class="col-span-2 text-sm text-gray-900">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                            @if($certificate->is_superseded) bg-gray-100 text-gray-800 @else bg-green-100 text-green-800 @endif">
                                            @if($certificate->is_superseded)
                                                {{ __('reservation.status.superseded') }}
                                            @else
                                                {{ __('reservation.status.active') }}
                                            @endif
                                        </span>
                                    </dd>
                                </div>

                                <div class="grid grid-cols-3 gap-4">
                                    <dt class="text-sm font-medium text-gray-500">{{ __('certificate.details.priority') }}</dt>
                                    <dd class="col-span-2 text-sm text-gray-900">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                            @if($certificate->is_current_highest) bg-green-100 text-green-800 @else bg-gray-100 text-gray-800 @endif">
                                            @if($certificate->is_current_highest)
                                                {{ __('reservation.priority.highest') }}
                                            @else
                                                {{ __('reservation.priority.superseded') }}
                                            @endif
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        {{-- Colonna destra: QR code e azioni --}}
                        <div class="flex flex-col items-center justify-between">
                            {{-- QR Code per la verifica --}}
                            <div class="p-4 mb-6 bg-white rounded-lg shadow">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode(route('egi-certificates.verify', $certificate->certificate_uuid)) }}"
                                     alt="{{ __('certificate.qr_code_alt') }}"
                                     class="w-48 h-48">
                                <p class="mt-2 text-xs text-center text-gray-500">{{ __('certificate.verification.what_this_means') }}</p>
                            </div>

                            {{-- Azioni --}}
                            <div class="w-full space-y-4">
                                @if($certificate->hasPdf())
                                <a href="{{ route('egi-certificates.download', $certificate->certificate_uuid) }}"
                                   class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg class="w-5 h-5 mr-2 -ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    {{ __('certificate.actions.download_pdf') }}
                                </a>
                                @endif

                                <a href="{{ route('egi-certificates.verify', $certificate->certificate_uuid) }}"
                                   class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg class="w-5 h-5 mr-2 -ml-1 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                    {{ __('certificate.actions.verify') }}
                                </a>

                                <a href="{{ route('egis.show', $certificate->egi_id) }}"
                                   class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg class="w-5 h-5 mr-2 -ml-1 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    {{ __('certificate.actions.view_egi') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Informazioni Aggiuntive --}}
                    <div class="pt-6 mt-8 border-t border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('certificate.verification.what_this_means') }}</h3>
                        <div class="p-4 mt-4 rounded-md bg-gray-50">
                            <p class="text-sm text-gray-700">
                                @if($certificate->is_superseded)
                                    {{ __('certificate.verification.explanation_priority') }}
                                @else
                                    @if($certificate->is_current_highest)
                                        {{ __('certificate.verification.explanation_valid') }}
                                    @endif
                                @endif
                            </p>
                        </div>
                    </div>

                    {{-- Firma e ID--}}
                    <div class="pt-4 mt-8 text-center border-t border-gray-200">
                        <p class="text-xs text-gray-500">{{ __('certificate.details.certificate_uuid') }}: {{ $certificate->certificate_uuid }}</p>
                        <p class="mt-1 font-mono text-xs text-gray-500 break-all">{{ __('certificate.details.signature_hash') }}: {{ $certificate->signature_hash }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-slot>
</x-guest-layout>
