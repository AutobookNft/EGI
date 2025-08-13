# 🔧 EGI Multi-Currency System - TECHNICAL IMPLEMENTATION PLAN

## 🚀 **CURRENT WORK SESSION**

-   **Working On**: Florence EGI Multi-Currency "Think FIAT, Operate ALGO" System
-   **Main Goal**: Complete user-facing multi-currency experience
-   **Session Focus**: Implement reservation system + price display + currency switching
-   **Current Task**: Ready to start Step 1 - Reservation System Integration
-   **Expected Time**: 11-15 hours total development
-   **Priority**: HIGH - Core business logic missing

## 🎯 **REAL STATUS**

-   **Database**: ✅ Schema ready (migrations applied)
-   **API**: ✅ Currency endpoints working
-   **UI Badge**: ✅ Mobile currency display working
-   **CORE LOGIC**: ❌ **MISSING** - Reservations, Price Display, User Preferences
-   **Current Commit**: `5e11402` (main branch)
-   **Ready for Production**: ❌ **NO** - Core features missinglti-Currency System - TECHNICAL IMPLEMENTATION PLAN

## 🎯 **REAL STATUS**

-   **Database**: ✅ Schema ready (migrations applied)
-   **API**: ✅ Currency endpoints working
-   **UI Badge**: ✅ Mobile currency display working
-   **CORE LOGIC**: ❌ **MISSING** - Reservations, Price Display, User Preferences
-   **Current Commit**: `5e11402` (main branch)
-   **Ready for Production**: ❌ **NO** - Core features missing

---

## ❌ **WHAT'S ACTUALLY MISSING** (Critical Implementation Gaps)

### **1. RESERVATION SYSTEM INTEGRATION**

❌ **Status**: NOT IMPLEMENTED
❌ **Problem**: Reservations still use old `offer_amount_eur` logic
❌ **Files to Fix**:

```
app/Models/Reservation.php               // Add multi-currency methods
app/Services/ReservationService.php     // Currency conversion logic
app/Http/Controllers/ReservationController.php  // Handle currency in forms
resources/views/reservations/create.blade.php   // Currency selector
resources/views/reservations/show.blade.php     // Display with user currency
```

### **2. PRICE DISPLAY IN ALL VIEWS**

❌ **Status**: NOT IMPLEMENTED  
❌ **Problem**: All prices still show in EUR, no user preference
❌ **Files to Fix**:

```
resources/views/collections/show.blade.php      // Collection detail page
resources/views/collections/index.blade.php     // Collection cards
resources/views/collections/card.blade.php      // Price display component
resources/views/dashboard/creator.blade.php     // Creator earnings
resources/views/partials/price-display.blade.php // Price component (create)
```

### **3. USER CURRENCY PREFERENCES**

❌ **Status**: NOT IMPLEMENTED
❌ **Problem**: No UI to change currency, no persistence
❌ **Files to Fix**:

```
resources/views/layouts/partials/header.blade.php    // Add currency dropdown
resources/js/currency-selector.js                    // Currency change logic (create)
app/Http/Controllers/UserPreferenceController.php    // Already exists but unused
routes/web.php                                       // Add preference routes
```

### **4. RILANCI/AUCTION SYSTEM**

❌ **Status**: NOT IMPLEMENTED
❌ **Problem**: Auction bids don't handle currency conversion
❌ **Files to Fix**:

```
app/Models/Rilancio.php                  // Add currency fields
app/Services/RilancioService.php         // Bid with currency conversion
app/Http/Controllers/RilancioController.php  // Handle currency in bids
resources/views/rilanci/                 // Bid forms and displays
```

---

## 🔧 **IMMEDIATE ACTION PLAN**

### **STEP 1: Make Reservations Work with Multi-Currency** (2-3 hours)

```php
// File: app/Models/Reservation.php
// Add methods:
public function getPriceInUserCurrency($userId)
public function convertPriceToFiat($targetCurrency)
public function getFormattedPrice($currency = null)

// File: app/Services/ReservationService.php
// Add methods:
public function createWithCurrency($collectionId, $userId, $fiatAmount, $currency)
public function calculateAlgoAmount($fiatAmount, $currency)
```

### **STEP 2: Add Currency Selector to UI** (2-3 hours)

```blade
{{-- File: resources/views/layouts/partials/header.blade.php --}}
{{-- Add dropdown AFTER currency badge --}}
<select id="currency-selector" class="...">
    <option value="USD" {{ auth()->user()?->preferred_currency === 'USD' ? 'selected' : '' }}>USD</option>
    <option value="EUR" {{ auth()->user()?->preferred_currency === 'EUR' ? 'selected' : '' }}>EUR</option>
    <!-- etc -->
</select>
```

### **STEP 3: Create Price Display Component** (1-2 hours)

```blade
{{-- File: resources/views/components/price-display.blade.php (create) --}}
@props(['amount', 'originalCurrency' => 'EUR', 'targetCurrency' => null])
<?php
$targetCurrency = $targetCurrency ?? (auth()->user()?->preferred_currency ?? 'USD');
$convertedAmount = app(\App\Services\CurrencyService::class)->convertFiatToFiat($amount, $originalCurrency, $targetCurrency);
?>
<span class="price-display">
    {{ number_format($convertedAmount, 2) }} {{ $targetCurrency }}
</span>
```

### **STEP 4: Update Collection Views** (2-3 hours)

```blade
{{-- File: resources/views/collections/show.blade.php --}}
{{-- Replace old price display with: --}}
<x-price-display :amount="$collection->price" originalCurrency="EUR" />

{{-- File: resources/views/collections/card.blade.php --}}
{{-- Same replacement in cards --}}
```

### **STEP 5: Add AJAX Currency Switching** (2-3 hours)

```javascript
// File: resources/js/currency-selector.js (create)
document
    .getElementById("currency-selector")
    .addEventListener("change", function () {
        const newCurrency = this.value;

        // Save preference
        fetch("/api/user/preferences/currency", {
            method: "PUT",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ currency: newCurrency }),
        });

        // Update all prices on page
        document.querySelectorAll(".price-display").forEach((el) => {
            // Re-fetch price in new currency
        });
    });
```

---

## 📂 **KEY FILES TO CREATE/MODIFY**

### **Files to CREATE**:

```
resources/views/components/price-display.blade.php    // Reusable price component
resources/views/components/currency-selector.blade.php // Currency dropdown
resources/js/currency-selector.js                     // Currency switching logic
app/View/Components/PriceDisplay.php                  // Price component class
```

### **Files to MODIFY** (in order of priority):

```
1. app/Models/Reservation.php                    // Add currency methods
2. app/Services/ReservationService.php          // Core reservation logic
3. resources/views/collections/show.blade.php   // Collection detail prices
4. resources/views/collections/card.blade.php   // Collection card prices
5. resources/views/layouts/partials/header.blade.php // Add currency selector
6. app/Http/Controllers/ReservationController.php // Handle currency in forms
7. routes/web.php                               // Add preference routes
```

---

## 🧪 **HOW TO TEST EACH STEP**

### **Test Reservation Currency**:

```bash
php artisan tinker
$reservation = \App\Models\Reservation::first();
$reservation->getPriceInUserCurrency(1); // Should return price in user's preferred currency
```

### **Test Price Display Component**:

```blade
{{-- Add to any view temporarily: --}}
<x-price-display :amount="100" originalCurrency="EUR" targetCurrency="USD" />
{{-- Should display: $110.50 USD (or current rate) --}}
```

### **Test Currency Switching**:

```javascript
// Open browser console, change currency selector
// Check network tab for API call to /api/user/preferences/currency
// Check that prices update on page
```

---

## 🎯 **REALISTIC TIMELINE**

-   **Day 1**: Steps 1-2 (Reservations + UI selector) = 4-6 hours
-   **Day 2**: Steps 3-4 (Price component + Collection views) = 4-5 hours
-   **Day 3**: Step 5 + Testing (AJAX + debugging) = 3-4 hours
-   **Total**: 11-15 hours of focused development

---

## � **UEM & ULM INTEGRATION GUIDE** (Ultra Error Manager & Ultra Log Manager)

### **🚨 Ultra Error Manager (UEM) - How It Works**

**Purpose**: Centralized error handling system for Florence EGI
**Location**: `app/Services/UltraErrorManager.php`

#### **How to Use UEM**:

```php
// In any service/controller:
use App\Services\UltraErrorManager;

public function someMethod() {
    try {
        // Your logic here
        $result = $this->currencyService->convertFiatToMicroAlgo($amount, $currency);
    } catch (Exception $e) {
        // UEM handles the error with context
        return UltraErrorManager::handleException($e, [
            'context' => 'currency_conversion',
            'amount' => $amount,
            'currency' => $currency,
            'user_id' => auth()->id()
        ]);
    }
}
```

#### **UEM Error Codes for Multi-Currency**:

```php
// Already defined in config/error-manager.php:
'CURRENCY_001' => 'Valuta non supportata'
'CURRENCY_002' => 'Tasso di cambio non disponibile'
'CURRENCY_003' => 'Conversione fallita'
'CURRENCY_004' => 'Importo non valido'
'CURRENCY_005' => 'API di cambio non disponibile'
'CURRENCY_006' => 'Preferenza valuta non salvata'
```

#### **UEM Usage Pattern**:

```php
// Method 1: Direct error with code
UltraErrorManager::logError('CURRENCY_001', [
    'attempted_currency' => $currency,
    'supported_currencies' => $this->getSupportedCurrencies()
]);

// Method 2: Exception handling with context
UltraErrorManager::handleException($exception, [
    'operation' => 'currency_conversion',
    'input_data' => $requestData
]);
```

### **📊 Ultra Log Manager (ULM) - How It Works**

**Purpose**: Enhanced logging with context enrichment for debugging
**Location**: `app/Services/UltraLogManager.php`

#### **How to Use ULM**:

```php
// In any service where you need detailed logs:
use App\Services\UltraLogManager;

public function createReservation($data) {
    // Log start of operation
    UltraLogManager::logOperation('reservation_creation_start', [
        'user_id' => auth()->id(),
        'collection_id' => $data['collection_id'],
        'fiat_amount' => $data['amount'],
        'fiat_currency' => $data['currency']
    ]);

    try {
        // Your business logic
        $microAlgoAmount = $this->currencyService->convertFiatToMicroAlgo(
            $data['amount'],
            $data['currency']
        );

        // Log successful conversion
        UltraLogManager::logSuccess('currency_conversion', [
            'fiat_amount' => $data['amount'],
            'fiat_currency' => $data['currency'],
            'micro_algo_amount' => $microAlgoAmount,
            'exchange_rate' => $this->currencyService->getCurrentRate($data['currency'])
        ]);

        $reservation = Reservation::create([
            'user_id' => auth()->id(),
            'collection_id' => $data['collection_id'],
            'offer_amount_fiat' => $data['amount'],
            'fiat_currency' => $data['currency'],
            'offer_amount_algo' => $microAlgoAmount,
            'exchange_rate' => $this->currencyService->getCurrentRate($data['currency']),
            'exchange_timestamp' => now()
        ]);

        // Log final success
        UltraLogManager::logOperation('reservation_created', [
            'reservation_id' => $reservation->id,
            'final_amounts' => [
                'fiat' => $data['amount'] . ' ' . $data['currency'],
                'algo' => $microAlgoAmount . ' microALGO'
            ]
        ]);

        return $reservation;

    } catch (Exception $e) {
        // Log error with ULM context
        UltraLogManager::logError('reservation_creation_failed', [
            'error_message' => $e->getMessage(),
            'input_data' => $data,
            'stack_trace' => $e->getTraceAsString()
        ]);

        // Then handle with UEM
        throw UltraErrorManager::handleException($e, [
            'operation' => 'reservation_creation',
            'user_context' => auth()->user()->toArray()
        ]);
    }
}
```

### **🔄 UEM + ULM Integration Pattern**:

```php
// Best practice: Use both together
public function handleCurrencyOperation($operation, $data) {
    $operationId = UltraLogManager::startOperation($operation, $data);

    try {
        // Your logic here
        $result = $this->performOperation($data);

        UltraLogManager::completeOperation($operationId, ['result' => $result]);
        return $result;

    } catch (Exception $e) {
        UltraLogManager::failOperation($operationId, ['error' => $e->getMessage()]);
        return UltraErrorManager::handleException($e, ['operation_id' => $operationId]);
    }
}
```

### **📁 Key UEM/ULM Files**:

```
app/Services/UltraErrorManager.php     // Error handling service
app/Services/UltraLogManager.php       // Enhanced logging service
config/error-manager.php               // Error codes and configuration
storage/logs/error_manager.log         // UEM error logs
storage/logs/laravel.log               // ULM enhanced logs
```

### **🧪 Testing UEM/ULM**:

```bash
# Check error logs
tail -f /home/fabio/EGI/error_manager.log

# Check enhanced logs
tail -f /home/fabio/EGI/storage/logs/laravel.log

# Test UEM error code
php artisan tinker
UltraErrorManager::logError('CURRENCY_001', ['test' => true]);

# Test ULM operation logging
UltraLogManager::logOperation('test_currency_op', ['amount' => 100, 'currency' => 'USD']);
```

---

## �🚨 **CRITICAL DEPENDENCIES**

### **Environment Variables Needed**:

```env
CURRENCY_API_KEY=your_exchangerate_api_key
CURRENCY_API_URL=https://api.exchangerate-api.com/v4/latest/
CURRENCY_CACHE_MINUTES=60
```

### **Database Check**:

```sql
-- Verify these tables exist:
SELECT * FROM reservations LIMIT 1;  -- Should have fiat_currency, offer_amount_fiat columns
SELECT * FROM users LIMIT 1;        -- Should have preferred_currency column
```

---

## 📝 **DEVELOPER HANDOFF CHECKLIST**

When you pick this up:

1. ✅ **UEM/ULM Setup**: Both systems are configured and ready to use
2. ✅ Verify database has multi-currency fields (`offer_amount_fiat`, `fiat_currency`, etc.)
3. ✅ Test API endpoints work: `/api/currency/rate/USD`, `/api/currency/rates/all`
4. ❌ **START HERE**: Implement `Reservation::getPriceInUserCurrency()` method **WITH UEM/ULM INTEGRATION**
5. ❌ Add currency selector to header.blade.php
6. ❌ Create price-display component
7. ❌ Update collection views to use new component
8. ❌ Add AJAX currency switching
9. ❌ Test everything works end-to-end **AND CHECK ERROR LOGS**

### **Integration Requirements**:

-   ✅ **ALWAYS use UEM** for error handling in currency operations
-   ✅ **ALWAYS use ULM** for detailed operation logging
-   ✅ **ALWAYS check logs** after testing: `tail -f error_manager.log` and `tail -f storage/logs/laravel.log`

**This is the REAL status with PROPER error management context.**
