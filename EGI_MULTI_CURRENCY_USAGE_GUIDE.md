# EGI Multi-Currency System - Guida d'Uso

## 📋 Overview

Il sistema Multi-Currency di EGI implementa l'architettura "Think FIAT, Operate ALGO" per gestire transazioni in denaro reale con conversioni accurate real-time.

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
