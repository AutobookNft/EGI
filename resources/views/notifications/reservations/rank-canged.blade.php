@php
  $up = ($notification->data['direction'] ?? null) === 'up';

  // Palette dinamica
  $c = $up
    ? [
        'accent_from' => 'from-emerald-500', 'accent_via' => 'via-emerald-400', 'accent_to' => 'to-teal-400',
        'ring' => 'ring-emerald-400/30', 'pill_bg' => 'bg-emerald-500/15', 'pill_txt' => 'text-emerald-300',
        'hint_bg' => 'bg-emerald-500/10', 'hint_ring' => 'ring-emerald-400/20', 'cta_bg' => 'bg-emerald-400', 'cta_hover' => 'hover:bg-emerald-300',
        'text_soft' => 'text-slate-300', 'text_muted' => 'text-slate-400', 'meta_border' => 'border-white/5'
      ]
    : [
        'accent_from' => 'from-rose-500', 'accent_via' => 'via-rose-400', 'accent_to' => 'to-amber-400',
        'ring' => 'ring-rose-400/30', 'pill_bg' => 'bg-rose-500/15', 'pill_txt' => 'text-rose-300',
        'hint_bg' => 'bg-rose-500/10', 'hint_ring' => 'ring-rose-400/20', 'cta_bg' => 'bg-rose-400', 'cta_hover' => 'hover:bg-rose-300',
        'text_soft' => 'text-slate-300', 'text_muted' => 'text-slate-400', 'meta_border' => 'border-white/5'
      ];
@endphp

<div
  class="mb-4 transition-colors duration-200 border shadow-lg rounded-xl border-white/10 bg-slate-800 hover:border-white/20"
  itemscope itemtype="https://schema.org/Message"
  data-notification-id="{{ $notification->id }}"
  data-payload="reservation"
  aria-label="Notifica: Cambio posizione"
>
  <!-- Accent bar -->
  <div class="h-1 w-full rounded-t-xl bg-gradient-to-r {{ $c['accent_from'] }} {{ $c['accent_via'] }} {{ $c['accent_to'] }}"></div>

  <div class="p-6">
    <!-- Header -->
    <div class="flex items-start justify-between gap-3">
      <div class="flex items-center gap-3">
        <div class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-slate-900/60 {{ $c['ring'] }} ring-1">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11 4a1 1 0 10-2 0v4a1 1 0 102 0V7zm-3 1a1 1 0 10-2 0v3a1 1 0 102 0V8zM8 9a1 1 0 00-2 0v2a1 1 0 102 0V9z" clip-rule="evenodd" />
          </svg>
        </div>
        <div>
          <h3 class="text-base font-semibold leading-5 text-white">
            @if($up)
              📈 {{ __('Sei salito in classifica!') }}
            @else
              📉 {{ __('Sei sceso in classifica') }}
            @endif
          </h3>
          <p class="mt-0.5 text-sm {{ $c['text_soft'] }}">
            {{ __('Posizione per') }} <strong class="text-white">{{ $notification->data['egi_title'] ?? 'questo EGI' }}</strong>
          </p>
        </div>
      </div>

      <!-- Badge stato -->
      <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-medium {{ $c['pill_bg'] }} {{ $c['pill_txt'] }} {{ $c['ring'] }} ring-1">
        @if($up)
          {{ __('Salito') }}
        @else
          {{ __('Scivolato') }}
        @endif
      </span>
    </div>

    <!-- Blocco rank -->
    <div class="mt-4 rounded-lg bg-slate-900/60 ring-1 ring-white/5">
      <div class="px-4 py-4">
        <div class="flex items-center justify-center gap-6">
          <div class="text-center">
            <div class="text-3xl font-bold text-white">#{{ $notification->data['old_rank'] ?? '?' }}</div>
            <div class="text-xs uppercase {{ $c['text_muted'] }}">{{ __('Prima') }}</div>
          </div>

          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 {{ $c['pill_txt'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            @if($up)
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
            @else
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
            @endif
          </svg>

          <div class="text-center">
            <div class="text-3xl font-bold text-white">#{{ $notification->data['new_rank'] ?? '?' }}</div>
            <div class="text-xs uppercase {{ $c['text_muted'] }}">{{ __('Ora') }}</div>
          </div>
        </div>

        @if(isset($notification->data['positions_changed']))
          <div class="mt-3 text-center text-sm {{ $c['pill_txt'] }}">
            @if($up)
              +{{ $notification->data['positions_changed'] }} {{ __('posizioni guadagnate') }}
            @else
              -{{ $notification->data['positions_changed'] }} {{ __('posizioni perse') }}
            @endif
          </div>
        @endif

        <div class="mt-4 flex items-center justify-between border-t {{ $c['meta_border'] }} pt-3 {{ $c['text_soft'] }}">
          <span class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm">{{ __('La tua offerta') }}</span>
          </span>
          <span class="text-lg font-bold text-white">€{{ number_format($notification->data['amount_eur'] ?? 0, 2) }}</span>
        </div>
      </div>
    </div>

    <!-- Suggerimento -->
    <div class="mt-3 rounded-lg px-4 py-2.5 text-sm {{ $c['hint_bg'] }} {{ $c['hint_ring'] }} {{ $c['pill_txt'] }} ring-1">
      @if($up)
        💡 {{ __('Ottimo! Mantieni l’attenzione: potrebbero superarti presto.') }}
      @else
        💡 {{ __('Se vuoi risalire, valuta di aumentare l’offerta.') }}
      @endif
    </div>

    <!-- CTA -->
    <div class="flex flex-col gap-3 mt-4 sm:flex-row">
      <a
        href="{{ route('egi.show', $notification->data['egi_id'] ?? '#') }}"
        class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg px-4 py-2.5 font-medium text-slate-900 {{ $c['cta_bg'] }} {{ $c['cta_hover'] }} focus:outline-none focus:ring-2 focus:ring-white/20"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
        </svg>
        {{ __('Vedi Classifica') }}
      </a>

      <button
        class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg px-4 py-2.5 font-medium text-slate-200 bg-slate-900/50 ring-1 ring-white/10 hover:bg-slate-900 reservation-archive-btn focus:outline-none focus:ring-2 focus:ring-white/20"
        data-notification-id="{{ $notification->id }}"
        data-action="archive"
        aria-label="Archivia questa notifica"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        {{ __('OK') }}
      </button>
    </div>

    <!-- Footer -->
    <div class="mt-4 flex items-center text-xs {{ $c['text_muted'] }}">
      <svg xmlns="http://www.w3.org/2000/svg" class="mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <time datetime="{{ $notification->created_at->toIso8601String() }}">{{ $notification->created_at->diffForHumans() }}</time>
    </div>
  </div>
</div>
