# 📋 Piano di Refactoring del Sistema di Prenotazioni EGI

**Data:** 13 Agosto 2025  
**Versione:** 2.0  
**Status:** READY TO START

---

## 🎯 OVERVIEW DEL PROBLEMA

### 📝 Requisiti Business Attuali

1. **EGI Senza Prezzo**: Non vendibile → prenotazioni impossibili
2. **Prima Prenotazione**: Importo ≥ prezzo base (es. 8000€)
3. **Rilancio**: Importo > prezzo precedente (es. > 8000€)
4. **Validazione Errori**:
    - Se rilancio ≤ prezzo precedente → **BLOCCO + MESSAGGIO CHIARO**
    - Nessun nuovo record creato

### 🏗️ Architettura Attuale

**Files Coinvolti:**

-   `app/Services/ReservationService.php` - Core business logic
-   `app/Http/Controllers/ReservationController.php` - API/Web endpoints
-   `app/Models/Reservation.php` - Model con campi limitati
-   `resources/views/layouts/partials/header.blade.php` - Frontend header

**UEM/ULM Integration:**

-   `vendor/ultra/ultra-error-manager` - Sistema di gestione errori standardizzato
-   `vendor/ultra/ultra-log-manager` - Sistema di logging avanzato

---

## 🚨 PROBLEMI CRITICI IDENTIFICATI

### 1. **Schema Database Incompleto**

**Problema:** Campi mancanti per gestione enterprise delle valute

**Campi Attuali:**

```php
'offer_amount_eur' => 'decimal:10,2'    // ERRORE: Nome generico
'offer_amount_algo' => 'decimal:18,8'   // OK
```

**Campi Necessari:**

```php
'offer_amount_fiat' => 'decimal:10,2'   // Valore in valuta FIAT
'currency' => 'string:3'                // ISO Code (EUR, USD, GBP...)
'exchange_rate' => 'decimal:18,8'       // Tasso di cambio ALGO al momento
'exchange_timestamp' => 'timestamp'     // Quando è stato rilevato il cambio
```

### 2. **Logica Rilancio Incompleta**

**Problema Attuale:** Validazione parziale, gestione superseded_by_id mancante

**Fix Necessario:**

-   Validazione pre-creazione più rigorosa
-   Aggiornamento corretto del campo `superseded_by_id`
-   Gestione della relazione di superseding bidirezionale

### 3. **Gestione Errori Non Standardizzata**

**Problema:** Mix di `throw new \Exception()` e UEM

**Standard da Seguire:** SOLO UEM/ULM per gestione errori e logging

### 4. **Servizio Cambio ALGO Mancante**

**Requisito:** Badge real-time nel header con aggiornamento ogni 30 secondi

---

## 🎯 PIANO DI IMPLEMENTAZIONE

### **FASE 1: Refactoring Database Schema**

#### 1.1 Creazione Migration

```bash
php artisan make:migration add_currency_fields_to_reservations_table
```

**Campi da Aggiungere:**

```php
Schema::table('reservations', function (Blueprint $table) {
    // Rinomina offer_amount_eur → offer_amount_fiat
    $table->renameColumn('offer_amount_eur', 'offer_amount_fiat');

    // Nuovi campi currency
    $table->string('currency', 3)->default('EUR')->after('offer_amount_fiat');
    $table->decimal('exchange_rate', 18, 8)->nullable()->after('currency');
    $table->timestamp('exchange_timestamp')->nullable()->after('exchange_rate');

    // Indici per performance
    $table->index(['currency']);
    $table->index(['exchange_timestamp']);
});
```

#### 1.2 Aggiornamento Model

```php
// app/Models/Reservation.php
protected $fillable = [
    // ... existing fields
    'offer_amount_fiat',  // Rinominato da offer_amount_eur
    'currency',
    'exchange_rate',
    'exchange_timestamp',
];

protected $casts = [
    'offer_amount_fiat' => 'decimal:2',
    'exchange_rate' => 'decimal:8',
    'exchange_timestamp' => 'datetime',
];
```

### **FASE 2: Servizio di Cambio Valute**

#### 2.1 Creazione CurrencyExchangeService

```php
// app/Services/CurrencyExchangeService.php
class CurrencyExchangeService {
    protected UltraLogManager $logger;

    public function getCurrentAlgoRate(string $baseCurrency = 'EUR'): array {
        // API call a servizio di cambio (es. CoinGecko, CoinMarketCap)
        // Ritorna: ['rate' => float, 'timestamp' => Carbon]
    }

    public function convertFiatToAlgo(float $fiatAmount, string $currency = 'EUR'): float {
        // Conversione con rate corrente
    }
}
```

#### 2.2 Badge Real-time Header

**File:** `resources/views/layouts/partials/header.blade.php`

**Aggiungere:**

```html
<div id="algo-rate-badge" class="badge">
    <span>ALGO: €<span id="algo-rate-value">--</span></span>
    <span class="update-indicator" id="rate-indicator">●</span>
</div>
```

**JavaScript (30s polling):**

```javascript
setInterval(() => {
    fetch("/api/currency/algo-rate")
        .then((response) => response.json())
        .then((data) => {
            document.getElementById("algo-rate-value").textContent =
                data.rate.toFixed(4);
            // Visual feedback dell'aggiornamento
        });
}, 30000);
```

### **FASE 3: Refactoring ReservationService**

#### 3.1 Logica Rilancio Migliorata

```php
public function createReservation(array $data, ?User $user = null, ?string $walletAddress = null): ?Reservation {
    return DB::transaction(function () use ($data, $user, $walletAddress) {
        // 1. Validazione EGI
        $egi = $this->validateEgiAvailability($data['egi_id']);

        // 2. Controllo rilancio PRE-CREAZIONE
        $this->validateRelaunchAmount($user, $data['egi_id'], $data['offer_amount_fiat']);

        // 3. Ottenere tasso di cambio corrente
        $exchangeData = $this->currencyService->getCurrentAlgoRate($data['currency'] ?? 'EUR');

        // 4. Creare nuova prenotazione
        $reservation = $this->createNewReservation([
            'user_id' => $user?->id,
            'egi_id' => $egi->id,
            'offer_amount_fiat' => $data['offer_amount_fiat'],
            'currency' => $data['currency'] ?? 'EUR',
            'exchange_rate' => $exchangeData['rate'],
            'exchange_timestamp' => $exchangeData['timestamp'],
            'offer_amount_algo' => $this->convertFiatToAlgo($data['offer_amount_fiat'], $exchangeData['rate']),
        ]);

        // 5. Aggiornare prenotazioni precedenti (superseding)
        $this->updateSupersededReservations($reservation);

        return $reservation;
    });
}

private function validateRelaunchAmount(?User $user, int $egiId, float $newAmount): void {
    if (!$user) return; // Skip per utenti anonimi

    $previousReservation = Reservation::where('user_id', $user->id)
        ->where('egi_id', $egiId)
        ->where('status', 'active')
        ->orderBy('created_at', 'desc')
        ->first();

    if ($previousReservation && $newAmount <= $previousReservation->offer_amount_fiat) {
        // UEM ERROR - Standardized
        $this->logger->error('Relaunch amount insufficient', [
            'user_id' => $user->id,
            'egi_id' => $egiId,
            'previous_amount' => $previousReservation->offer_amount_fiat,
            'new_amount' => $newAmount,
        ]);

        throw UltraError::handle('RESERVATION_RELAUNCH_INSUFFICIENT_AMOUNT', [
            'egi_id' => $egiId,
            'previous_amount' => $previousReservation->offer_amount_fiat,
            'new_amount' => $newAmount,
            'currency' => $previousReservation->currency,
        ]);
    }
}
```

### **FASE 4: Configurazione UEM Error Codes**

#### 4.1 Nuovi Error Codes

**File:** `config/error-manager.php`

```php
'RESERVATION_RELAUNCH_INSUFFICIENT_AMOUNT' => [
    'type' => 'error',
    'blocking' => 'blocking',
    'dev_message_key' => 'error-manager::errors.dev.relaunch_insufficient_amount',
    'user_message_key' => 'error-manager::errors.user.relaunch_insufficient_amount',
    'http_status_code' => 422,
    'devTeam_email_need' => false,
    'notify_slack' => false,
    'msg_to' => 'sweet-alert',
],

'CURRENCY_EXCHANGE_SERVICE_FAILED' => [
    'type' => 'critical',
    'blocking' => 'semi-blocking',
    'dev_message_key' => 'error-manager::errors.dev.currency_exchange_failed',
    'user_message_key' => 'error-manager::errors.user.currency_exchange_failed',
    'http_status_code' => 503,
    'devTeam_email_need' => true,
    'notify_slack' => true,
    'msg_to' => 'sweet-alert',
],

'RESERVATION_CURRENCY_NOT_SUPPORTED' => [
    'type' => 'error',
    'blocking' => 'blocking',
    'dev_message_key' => 'error-manager::errors.dev.currency_not_supported',
    'user_message_key' => 'error-manager::errors.user.currency_not_supported',
    'http_status_code' => 422,
    'devTeam_email_need' => false,
    'notify_slack' => false,
    'msg_to' => 'sweet-alert',
],
```

#### 4.2 Messaggi di Traduzione

**File:** `resources/lang/it/error-manager.php`

```php
'user' => [
    'relaunch_insufficient_amount' => 'Il tuo rilancio deve essere superiore alla tua offerta precedente di :previous_amount :currency. Hai inserito :new_amount :currency.',
    'currency_exchange_failed' => 'Impossibile ottenere il tasso di cambio corrente. Riprova tra qualche minuto.',
    'currency_not_supported' => 'La valuta selezionata non è supportata. Valute supportate: EUR, USD, GBP.',
],
```

### **FASE 5: API Endpoints**

#### 5.1 Currency API

```php
// routes/api.php
Route::get('/currency/algo-rate', [CurrencyController::class, 'getAlgoRate']);
Route::get('/currency/supported', [CurrencyController::class, 'getSupportedCurrencies']);

// app/Http/Controllers/CurrencyController.php
class CurrencyController extends Controller {
    public function getAlgoRate(Request $request) {
        $currency = $request->get('currency', 'EUR');

        try {
            $rateData = $this->currencyService->getCurrentAlgoRate($currency);
            return response()->json([
                'success' => true,
                'data' => $rateData
            ]);
        } catch (\Exception $e) {
            return UltraError::handle('CURRENCY_EXCHANGE_SERVICE_FAILED', [
                'currency' => $currency,
                'requested_at' => now(),
            ], $e);
        }
    }
}
```

---

## 🛠️ SPECIFICHE TECNICHE

### **UEM/ULM Standard da Seguire**

#### Logging con ULM

```php
// ✅ CORRETTO
$this->logger->info('Reservation created successfully', [
    'reservation_id' => $reservation->id,
    'user_id' => $user?->id,
    'egi_id' => $egi->id,
    'offer_amount_fiat' => $reservation->offer_amount_fiat,
    'currency' => $reservation->currency,
]);

// ❌ EVITARE
\Log::info('Some message'); // Non standardizzato
```

#### Error Handling con UEM

```php
// ✅ CORRETTO
throw UltraError::handle('ERROR_CODE', [
    'context_key' => 'context_value',
], $originalException);

// ❌ EVITARE
throw new \Exception('Generic message'); // Non tracciabile
```

### **Database Migration Strategy**

#### Migrazione Senza Downtime

```php
// Step 1: Aggiungere nuovi campi
Schema::table('reservations', function (Blueprint $table) {
    $table->string('currency', 3)->default('EUR');
    $table->decimal('exchange_rate', 18, 8)->nullable();
    $table->timestamp('exchange_timestamp')->nullable();
});

// Step 2: Popolare dati esistenti
DB::statement("UPDATE reservations SET currency = 'EUR' WHERE currency IS NULL");

// Step 3: In migration separata rinominare offer_amount_eur
Schema::table('reservations', function (Blueprint $table) {
    $table->renameColumn('offer_amount_eur', 'offer_amount_fiat');
});
```

### **Testing Strategy**

#### Unit Tests Necessari

```php
// ReservationServiceTest.php
public function test_validates_relaunch_amount_correctly()
public function test_creates_reservation_with_currency_data()
public function test_handles_currency_service_failure()
public function test_updates_superseded_reservations_correctly()

// CurrencyExchangeServiceTest.php
public function test_fetches_current_algo_rate()
public function test_handles_api_service_failure()
public function test_converts_fiat_to_algo_correctly()
```

---

## 📋 CHECKLIST IMPLEMENTAZIONE

### **Database**

-   [ ] Migration per nuovi campi currency
-   [ ] Migration per rinominare offer_amount_eur
-   [ ] Aggiornare Model Reservation
-   [ ] Aggiornare Model EgiReservationCertificate
-   [ ] Test migrazione su database di sviluppo

### **Services**

-   [ ] CurrencyExchangeService con API integration
-   [ ] Refactoring ReservationService
-   [ ] Aggiornare CurrencyService esistente
-   [ ] Integration UEM/ULM completa

### **Controllers**

-   [ ] CurrencyController per API endpoints
-   [ ] Aggiornare ReservationController
-   [ ] Error handling standardizzato

### **Frontend**

-   [ ] Badge ALGO rate in header
-   [ ] JavaScript polling ogni 30s
-   [ ] Form prenotazione con currency selector
-   [ ] Messaggi di errore user-friendly

### **Configuration**

-   [ ] Nuovi error codes in UEM config
-   [ ] Traduzioni messaggi errore
-   [ ] Variabili ENV per API currency service
-   [ ] Rate limiting per API calls

### **Testing**

-   [ ] Unit tests per nuovi services
-   [ ] Integration tests per flow completo
-   [ ] Frontend testing per badge real-time
-   [ ] Load testing per API currency

### **Documentation**

-   [ ] Aggiornare API documentation
-   [ ] Guide per configurazione currency service
-   [ ] Esempi di utilizzo UEM/ULM
-   [ ] Migration guide per produzione

---

## 🚀 PRIORITÀ IMPLEMENTAZIONE

### **HIGH PRIORITY (Settimana 1)**

1. Database schema refactoring
2. ReservationService logic fix
3. UEM error codes configuration

### **MEDIUM PRIORITY (Settimana 2)**

4. CurrencyExchangeService
5. API endpoints per currency
6. Header badge implementation

### **LOW PRIORITY (Settimana 3)**

7. Advanced currency features
8. Extended testing
9. Performance optimization

---

## 🔄 PROCESSO DI MIGRAZIONE PRODUZIONE

### **Strategia Zero-Downtime**

1. **Deploy Database Changes**: Nuovi campi, mantieni vecchi
2. **Deploy Application**: Support per entrambi i campi
3. **Data Migration**: Popola nuovi campi da vecchi
4. **Switch Logic**: Usa nuovi campi
5. **Cleanup**: Rimuovi campi vecchi

### **Rollback Plan**

-   Mantieni campi vecchi fino a conferma funzionamento
-   Scripts di rollback preparati
-   Backup completo prima migrazione
-   Monitoring attivo post-deploy

---

**🎯 OBIETTIVO FINALE:**  
Sistema di prenotazioni robusto, enterprise-ready, con gestione multi-currency, real-time exchange rates, error handling standardizzato UEM/ULM, e user experience ottimale.

---

_Documento preparato per nuova chat session - Contains all context needed for implementation_
