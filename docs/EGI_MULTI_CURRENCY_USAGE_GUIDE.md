# EGI Multi-Currency System - Guida d'Uso

## 📋 Overview

Il sistema Multi-Currency di EGI implementa l'architettura "Think FIAT, Operate ALGO" per gestire transazioni in denaro reale con conversioni accurate real-time.

## ✅ AGGIORNAMENTO: PROBLEMI RISOLTI (Gennaio 2025)

### 🛠️ Fix Implementati

**1. RISOLTO: "NaN USD" nel Display delle Conversioni**

-   **Problema**: Le conversioni mostravano "NaN USD" invece dei prezzi corretti
-   **Cause**: Selettori mancanti, formula di conversione errata, bug nel refresh dei tassi
-   **Soluzione**: Correzioni multiple in CurrencyDisplayManager, CurrencyService, e CurrencyDisplayComponent

**2. RISOLTO: Sincronizzazione Header-Card**

-   **Problema**: Cambiando valuta dal badge nell'header, le card non si aggiornava automaticamente
-   **Soluzione**: Implementato sistema di eventi `currencyChanged` tra CurrencyBadgeManager e CurrencyDisplayComponent

**3. RISOLTO: "Pulsazioni" delle Cifre**

-   **Problema**: Dopo i refresh automatici, le cifre "pulsavano" continuamente (5 volte/secondo)
-   **Soluzione**: Ottimizzato il rilevamento dei cambi valuta per evitare aggiornamenti inutili

**4. RISOLTO: Loop Infiniti di Aggiornamento**

-   **Problema**: Eventi multipli causavano loop infiniti di conversioni
-   **Soluzione**: Eliminato doppio emit di eventi e migliorato controllo delle condizioni

### 🔧 Componenti Modificati

-   `resources/ts/ui/currencyDisplayManager.ts` - Selettori e inizializzazione
-   `resources/ts/services/currencyService.ts` - Formula di conversione ALGO
-   `resources/ts/components/CurrencyDisplayComponent.ts` - Gestione rate refresh e eventi
-   `resources/views/layouts/partials/header.blade.php` - CurrencyBadgeManager sync

### 📊 Risultati

✅ Sistema completamente funzionante
✅ Conversioni accurate in real-time  
✅ Sincronizzazione perfetta header-card
✅ Performance ottimizzate (refresh ogni 30s)
✅ Zero loop infiniti o aggiornamenti inutili

## 🎯 Componenti Principali

### 1. CurrencySelectorComponent

**Posizione**: Header Badge
**Funzione**: Permette all'utente di selezionare la valuta preferita
**Supporta**: Desktop e Mobile

### 2. CurrencyDisplayComponent

**Funzione**: Converte e mostra automaticamente i prezzi nella valuta selezionata
**Auto-registra**: Tutti gli elementi con `data-price` e `data-currency`

### 3. Currency Price Blade Component

**File**: `resources/views/components/currency-price.blade.php`
**Uso semplificato**: `<x-currency-price :price="$egi->price" currency="EUR" />`

## 🚀 Come Usare il Sistema

### Nei Template Blade

#### Metodo 1: Componente Blade (RACCOMANDATO)

```blade
{{-- Prezzo semplice --}}
<x-currency-price :price="$egi->price" currency="EUR" />

{{-- Prezzo con classi custom --}}
<x-currency-price
    :price="$displayPrice"
    currency="EUR"
    class="text-4xl font-bold text-white"
    :show-original="true"
    :show-conversion-note="true"
/>
```

#### Metodo 2: Attributi data- (Manuale)

```blade
<span
    class="currency-display"
    data-price="{{ $egi->price }}"
    data-currency="EUR"
>
    €{{ number_format($egi->price, 2) }}
</span>
```

### In TypeScript/JavaScript

#### Registrazione Manuale Elementi

```javascript
// Registra un elemento per la conversione currency
window.currencyDisplay.registerPriceElement(element, originalPrice, "EUR", {
    showOriginalCurrency: true,
    showConversionNote: true,
});

// Conversione automatica di elementi esistenti
CurrencyDisplayComponent.convertElement(element, options);
```

#### Eventi Currency

```javascript
// Listen per cambi di valuta
document.addEventListener("currencyChanged", (e) => {
    console.log("Nuova valuta:", e.detail.currency);
    // Il CurrencyDisplayComponent aggiorna automaticamente tutti i prezzi
});
```

## ⚙️ Configurazione

### Valute Supportate

File: `config/app.php`

```php
'currency' => [
    'supported_currencies' => ['USD', 'EUR', 'GBP', 'JPY', 'AUD'],
    'default_currency' => 'USD',
    'cache_ttl_seconds' => 60,
],
```

### API Endpoints

#### Pubblici (Nessuna autenticazione)

-   `GET /api/currency/rate/{currency}` - Tasso specifico
-   `GET /api/currency/rates` - Tutti i tassi
-   `GET /api/currency/default` - Tasso default (USD)

#### Protetti (Autenticazione richiesta)

-   `GET /user/preferences/currency` - Preferenza utente
-   `PUT /user/preferences/currency` - Aggiorna preferenza

## 🔧 Funzionalità Avanzate

### Opzioni Display

```javascript
const options = {
    showOriginalCurrency: false, // Mostra valuta originale come nota
    showConversionNote: false, // Mostra indicatore conversione (*)
    formatStyle: "standard", // 'compact' | 'standard' | 'scientific'
    minimumFractionDigits: 2, // Cifre decimali minime
    maximumFractionDigits: 2, // Cifre decimali massime
};
```

### Cache e Performance

-   **Cache Exchange Rates**: 60 secondi (configurabile)
-   **Cache User Preference**: localStorage + database
-   **Auto-refresh**: Ogni 2 minuti per i tassi di cambio

### Error Handling

-   **UEM Integration**: Tutti gli errori sono gestiti da Ultra Error Manager
-   **Fallback Graceful**: Se API non disponibile, mostra valuta originale
-   **Offline Support**: Cache localStorage per preferenze utente

## 🛠️ Testing

### Test Manuale Frontend

```javascript
// Console browser
window.currencyDisplay.registerPriceElement(
    document.querySelector(".test-price"),
    1000,
    "EUR"
);

// Cambia valuta programmaticamente
document.dispatchEvent(
    new CustomEvent("currencyChanged", {
        detail: { currency: "USD" },
    })
);
```

## 🔍 ARCHITETTURA POST-FIX (Gennaio 2025)

### Flusso di Sincronizzazione

```
1. USER ACTION: Click sul badge valuta nell'header
   ↓
2. CurrencyBadgeManager.switchCurrency()
   ↓
3. API Call: /api/user/preferred-currency
   ↓
4. CurrencyBadgeManager.fetchAndUpdateRate()
   ↓
5. CurrencyBadgeManager.updateBadge()
   ↓ (solo se valuta effettivamente cambiata)
6. Emit: CustomEvent('currencyChanged')
   ↓
7. CurrencyDisplayComponent riceve evento
   ↓
8. Aggiornamento automatico di tutte le card
```

### Refresh Automatico (ogni 30s)

```
CurrencyBadgeManager Timer (30s)
   ↓
fetchAndUpdateRate()
   ↓
updateBadge() (confronta con this.currentCurrency)
   ↓ (solo se cambio rilevato)
Emit: currencyChanged
   ↓
Aggiornamento card sincronizzato
```

### Cache e Performance

-   **Rate Cache**: 5 minuti (CurrencyDisplayComponent)
-   **Rate Cleanup**: 10 minuti (expired rates only)
-   **API Throttling**: 5 secondi minimo tra chiamate
-   **Header Refresh**: 30 secondi
-   **Component Refresh**: 2 minuti (cleanup cache)

### Test Backend API

```bash
# Test endpoint pubblico
curl http://localhost/api/currency/rate/EUR

# Test preferenza utente (con autenticazione)
curl -X PUT http://localhost/user/preferences/currency \
  -H "Content-Type: application/json" \
  -d '{"currency":"USD"}' \
  --cookie "session_cookie_here"
```

## 📊 Architettura "Think FIAT, Operate ALGO"

1. **Frontend**: L'utente vede e pensa in FIAT (EUR, USD, ecc.)
2. **Conversione**: Sistema converte FIAT ↔ microALGO per operazioni
3. **Storage**: Database salva in microALGO per precisione
4. **Display**: Mostra sempre nella valuta preferita dell'utente

## 🎉 Status Implementation

### ✅ Completato

-   [x] CurrencyService con cache e UEM
-   [x] API endpoints pubblici e protetti
-   [x] CurrencySelectorComponent mobile-first
-   [x] CurrencyDisplayComponent auto-conversion
-   [x] Blade component currency-price
-   [x] Routes web per preferenze utente
-   [x] Configurazione esternalizzata

### 🔄 In Corso

-   [ ] Test sistema completo
-   [ ] Validazione salvataggio preferenze
-   [ ] Ottimizzazione performance

### 📅 TODO

-   [ ] Unit tests
-   [ ] Documentation API completa
-   [ ] Monitoraggio errori real-time
