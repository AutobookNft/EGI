{{-- 
ESEMPIO DI UTILIZZO DEL COMPONENTE PAYMENT DISTRIBUTION STATS

Puoi includere questo componente in qualsiasi vista Blade così:
--}}

{{-- 1. Inclusione semplice --}}
<x-payment-distribution-stats />

{{-- 2. Esempio in una pagina dashboard --}}
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Dashboard Payment Distributions</h1>
    
    {{-- Statistiche globali --}}
    <div class="mb-8">
        <h2 class="text-lg font-semibold mb-4">Statistiche Globali</h2>
        <x-payment-distribution-stats />
    </div>
    
    {{-- Altri contenuti della dashboard... --}}
</div>

{{-- 3. Esempio in un hero banner --}}
<div class="relative min-h-screen bg-gradient-to-br from-blue-900 to-purple-900">
    <div class="absolute inset-0 bg-black bg-opacity-50"></div>
    
    <div class="relative z-10 flex flex-col items-center justify-center min-h-screen text-white">
        <h1 class="text-5xl font-bold mb-8">FlorenceEGI Platform</h1>
        
        {{-- Statistiche sovrapposte --}}
        <x-payment-distribution-stats />
        
        <div class="mt-8">
            <button class="px-8 py-3 bg-blue-600 hover:bg-blue-700 rounded-lg font-semibold">
                Esplora Collections
            </button>
        </div>
    </div>
</div>

{{--
DATI MOSTRATI DAL COMPONENTE:

1. EGIS: 47 (Numero totale EGI presenti)
2. SELL EGIS: 7 (EGI con prenotazioni attive)  
3. VOLUME: €1,180.00 (Totale distribuito)
4. COLLECTIONS: 1 (Collections con distribuzioni)
5. EPP: €236.00 (Totale distribuito agli EPP - "di cui")

Il componente è responsive e segue lo stesso styling del componente di riferimento.
--}}
