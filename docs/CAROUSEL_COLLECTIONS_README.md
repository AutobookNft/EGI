# Carousel Collections Components

## Descrizione

Questi componenti implementano un carousel delle collection simile a quello di OpenSea, ma adattato per il sistema FlorenceEGI con dati specifici di **Payment Distribution**.

## Componenti Creati

### 1. `carousel-coll-list.blade.php`
**Container principale del carousel** che gestisce:
- Layout del carousel con controlli di navigazione
- Logica JavaScript per lo scorrimento automatico e manuale
- Responsive design per desktop, tablet e mobile
- Indicatori di posizione
- Auto-play con pausa su hover

### 2. `coll-card-list-small.blade.php`
**Singola card della collection** che mostra:
- **VOLUME**: Sostituisce ETH di OpenSea - mostra il totale distribuito (`total_distributed`)
- **EPP**: Sostituisce la % di OpenSea - mostra la percentuale EPP (`total_to_epp`)
- Avatar della collection (placeholder con iniziali)
- Statistiche aggiuntive: prenotazioni, distribuzione media, ratio EPP
- Effetti hover e interazioni

## Dati Utilizzati

I componenti utilizzano `PaymentDistribution::getDashboardStats()` che restituisce:

```php
[
    'top_collections' => [
        [
            'collection_id' => 1,
            'collection_name' => 'Nome Collezione',
            'total_distributed' => 3560.00,     // VOLUME (era ETH)
            'total_to_epp' => 712.00,          // Per calcolare % EPP
            'reservations_count' => 6,
            'avg_distribution' => 197.78,
            'total_to_creators' => 2492.00,
            'total_to_collectors' => 356.00
        ]
    ]
]
```

## Corrispondenza OpenSea vs FlorenceEGI

| OpenSea | FlorenceEGI | Significato |
|---------|-------------|-------------|
| ETH | VOLUME | Importo totale distribuito in EUR |
| % | EPP | Percentuale distribuita agli EPP |
| Floor Price | Avg. Distr. | Distribuzione media |
| Items | Reservations | Numero prenotazioni |

## Funzionalità

### Carousel
- ✅ Scorrimento automatico ogni 5 secondi
- ✅ Pausa su hover
- ✅ Controlli prev/next
- ✅ Indicatori di posizione
- ✅ Responsive (desktop: 3 cards, tablet: 2 cards, mobile: 1 card)
- ✅ Smooth transitions

### Card
- ✅ Avatar con iniziali collezione
- ✅ Status "Active" con animazione
- ✅ Volume formattato in EUR
- ✅ Percentuale EPP calcolata
- ✅ Variazione percentuale simulata (da sostituire con dati reali)
- ✅ Statistiche aggiuntive
- ✅ Effetti hover
- ✅ Click handler personalizzabile

## Utilizzo

### Nel template Blade:
```blade
{{-- Carousel completo --}}
<x-carousel-coll-list />

{{-- Card singola (raramente necessario) --}}
<x-coll-card-list-small 
    :collectionId="1"
    :collectionName="Test Collection"
    :totalDistributed="1000"
    :totalToEpp="200"
    :reservationsCount="5"
    :avgDistribution="200"
/>
```

### Route di test:
```
/test-carousel
```

## Traduzioni

Le etichette sono localizzate in `resources/lang/it/statistics.php`:
- `top_collections`
- `active`
- `reservations`
- `avg_distribution`
- `epp_ratio`

## Personalizzazioni

### Per modificare l'aspetto:
- Colori: Modifica le classi Tailwind nei file `.blade.php`
- Animazioni: Personalizza la durata in CSS e JavaScript
- Layout: Modifica le classi responsive

### Per modificare il comportamento:
- Auto-play timing: Cambia `setInterval(nextSlide, 5000)` in `carousel-coll-list.blade.php`
- Cards per slide: Modifica `itemsPerSlide = 3` per desktop
- Click handler: Personalizza `viewCollectionDetails()` in `coll-card-list-small.blade.php`

## TODO/Miglioramenti Futuri

- [ ] Sostituire variazione % simulata con dati reali di trending
- [ ] Aggiungere lazy loading per le immagini delle collection
- [ ] Implementare filtri per categoria/status
- [ ] Aggiungere animazioni più elaborate
- [ ] Ottimizzare query database per grandi volumi
- [ ] Aggiungere drag&drop per mobile
- [ ] Implementare deep linking per le collection

## Dipendenze

- Tailwind CSS (per styling)
- Blade Components (Laravel)
- JavaScript vanilla (no framework esterni)
- PaymentDistribution model e relativi metodi
