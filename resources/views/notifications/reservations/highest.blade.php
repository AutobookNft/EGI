<div
  class="mb-4 transition-colors duration-200 border shadow-lg rounded-xl border-emerald-500/40 bg-slate-800 hover:border-emerald-500/60"
  itemscope itemtype="https://schema.org/Message"
  data-notification-id="{{ $notification->id }}"
  data-payload="reservation"
  aria-label="Notifica: Offerta più alta"
>
  <!-- Accent bar -->
  <div class="w-full h-1 rounded-t-xl bg-gradient-to-r from-emerald-500 via-emerald-400 to-teal-400"></div>

  <div class="p-6">
    <!-- Header -->
    <div class="flex items-start justify-between gap-3">
      <div class="flex items-center gap-3">
        <div class="inline-flex items-center justify-center rounded-full h-9 w-9 bg-emerald-600/90 ring-1 ring-emerald-300/40">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z" clip-rule="evenodd"/>
          </svg>
        </div>
        <div>
          <h3 class="text-base font-semibold leading-5 text-white">
            🏆 {{ __('Sei in prima posizione!') }}
          </h3>
          <p class="mt-0.5 text-sm text-slate-300">
            {{ __('Offerta più alta per') }}
            <strong class="text-white">{{ $notification->data['egi_title'] ?? 'questo EGI' }}</strong>
          </p>
        </div>
      </div>

      <!-- Badge stato -->
      <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-medium rounded-full bg-emerald-500/15 text-emerald-300 ring-1 ring-emerald-400/30">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 2l3 7h7l-5.5 4 2.1 7L12 17l-6.6 3 2.1-7L2 9h7z"/>
        </svg>
        {{ __('Offerta migliore') }}
      </span>
    </div>

    <!-- Importo principale -->
    <div class="mt-4 rounded-lg bg-slate-900/60 ring-1 ring-white/5">
      <div class="flex items-center justify-between gap-3 px-4 py-3">
        <div class="flex items-center text-slate-300">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span class="text-sm">{{ __('La tua offerta') }}</span>
        </div>
        <span class="text-2xl font-bold tracking-tight text-white">
          €{{ number_format($notification->data['amount_eur'] ?? 0, 2) }}
        </span>
      </div>

      <!-- Meta info -->
      <div class="grid gap-2 px-4 py-3 border-t border-white/5 sm:grid-cols-2">
        @if(isset($notification->data['total_competitors']) && $notification->data['total_competitors'] > 0)
          <div class="flex items-center text-xs text-slate-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            {{ __('Hai superato') }} {{ $notification->data['total_competitors'] }} {{ __('altri offerenti') }}
          </div>
        @endif

        @if(isset($notification->data['previous_rank']) && $notification->data['previous_rank'] > 1)
          <div class="flex items-center text-xs text-slate-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
            </svg>
            {{ __('Sei salito dalla posizione') }} #{{ $notification->data['previous_rank'] }}
          </div>
        @endif
      </div>
    </div>

    <!-- Suggerimento -->
    <div class="mt-3 rounded-lg bg-emerald-500/10 px-4 py-2.5 text-sm text-emerald-300 ring-1 ring-emerald-400/20">
      💡 {{ __('Mantieni la tua posizione! Altri utenti potrebbero fare offerte più alte.') }}
    </div>

    <!-- CTA -->
    <div class="flex flex-col gap-3 mt-4 sm:flex-row">
      <a
        href="{{ route('egis.show', $notification->data['egi_id'] ?? '#') }}"
        class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg px-4 py-2.5 font-medium text-slate-900 bg-emerald-400 hover:bg-emerald-300 focus:outline-none focus:ring-2 focus:ring-emerald-300/60"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
        </svg>
        {{ __('Vedi EGI') }}
      </a>

      <button
        class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg px-4 py-2.5 font-medium text-slate-200 bg-slate-900/50 ring-1 ring-white/10 hover:bg-slate-900 reservation-archive-btn focus:outline-none focus:ring-2 focus:ring-slate-500/40"
        data-notification-id="{{ $notification->id }}"
        data-action="archive"
        aria-label="Archivia questa notifica"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
        </svg>
        {{ __('OK, Capito!') }}
      </button>
    </div>

    <!-- Footer -->
    <div class="flex items-center mt-4 text-xs text-slate-400">
      <svg xmlns="http://www.w3.org/2000/svg" class="mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <time datetime="{{ $notification->created_at->toIso8601String() }}">{{ $notification->created_at->diffForHumans() }}</time>
    </div>
  </div>
</div>
