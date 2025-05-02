{{-- resources/views/home.blade.php --}}
<x-guest-layout
    title="FlorenceEGI | Home"
    metaDescription="Scopri collezioni d’arte ecologica e sostieni progetti ambientali su FlorenceEGI."
>
    {{-- Hero --}}
    <x-slot name="heroContent">
        <h1 class="text-4xl font-bold">Benvenuto su EcoGoods</h1>
        <p class="mt-2 text-lg">Scopri collezioni d’arte ecologica e sostieni progetti ambientali.</p>
        <a href="#"
           class="mt-4 inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
           Esplora Gallerie
        </a>
    </x-slot>

    {{-- In evidenza --}}
    {{-- <section class="py-12">
      <div class="container mx-auto px-4">
        <h2 class="text-2xl font-semibold mb-6">In evidenza</h2>
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          @foreach($published as $col)
            <x-collection-card :collection="$col"/>
          @endforeach
        </div>
      </div>
    </section> --}}

    {{-- Nuove Gallerie --}}
    <section class="bg-gray-50 py-12">
      <div class="container mx-auto px-4">
        <h2 class="text-2xl font-semibold mb-6">Nuove Gallerie</h2>
        <div class="grid gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
            @foreach($collections as $index => $collection)
            <div class="w-full sm:max-w-[350px] px-2 flex-shrink-0">
                <x-collection-card :id="$collection->id" :editable="false" imageType="card" />

                </div>
            @endforeach
        </div>
      </div>
    </section>

    {{-- CTA creator --}}
    <section class="py-12 text-center">
      <p class="text-lg mb-4">Sei un creator? Metti in mostra il tuo progetto ambientale.</p>
      <a href="#"
         class="px-6 py-3 border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-100">
         Crea la tua Galleria
      </a>
    </section>
</x-guest-layout>
