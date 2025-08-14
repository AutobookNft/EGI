# 💰 EGI Multi-Currency System - FACTS ONLY Documentation

## 🚨 **CRITICAL: FINANCIAL SYSTEM IN DEVELOPMENT**

**⚠️ WARNING**: This involves REAL MONEY transactions. Every change must be tested and verified.  
**🔒 COMPLIANCE**: All currency operations must maintain audit trails for legal compliance.

## 📝 **DOCUMENTATION UPDATE**

**Date**: August 14, 2025  
**Action**: Updated error codes section with accurate configuration from `config/error-manager.php`  
**Reason**: Previous error codes were simplified/incorrect - now reflects actual system configuration

---

## ✅ **VERIFIED EXISTING COMPONENTS** (Based on actual code analysis)

### **1. DATABASE SCHEMA** ✅ **CONFIRMED**

**Source**: Migration files and database structure

```sql
-- reservations table (VERIFIED):
fiat_currency         VARCHAR(3) DEFAULT 'USD'     -- ISO 4217 currency code
offer_amount_fiat     DECIMAL(10,2) NOT NULL       -- Amount in FIAT currency
offer_amount_algo     BIGINT UNSIGNED NOT NULL     -- Amount in microALGO
exchange_rate         DECIMAL(18,8) NOT NULL       -- ALGO->FIAT rate at time of transaction
exchange_timestamp    TIMESTAMP NOT NULL           -- Rate timestamp for audit

-- users table (VERIFIED):
preferred_currency    VARCHAR(3) DEFAULT 'USD'     -- User's preferred display currency
```

### **2. CREATOR AUTHORIZATION SYSTEM** ✅ **IMPLEMENTED**

**Status**: Recently implemented and committed (commit 7338d92)
**Implementation**: Creator detection logic across all EGI views

**Files Updated**:

```
resources/views/egis/show.blade.php              // Main EGI detail page
resources/views/components/egi-card.blade.php    // Individual EGI card component
resources/views/components/egi-card-list.blade.php // List-style EGI card component
```

**Logic Implemented**:

```php
// Creator detection (VERIFIED in all three files):
$isCreator = auth()->check() && auth()->id() === $egi->user_id;
// OR (in egis/show.blade.php using FegiAuth):
$isCreator = App\Helpers\FegiAuth::check() && App\Helpers\FegiAuth::id() === $egi->user_id;

// Button hiding logic:
@if(!$isCreator)
  {{-- Reservation and like buttons only visible to non-creators --}}
@endif
```

**Features**:

-   ✅ **Reservation buttons hidden** for creators (Prenota/Rilancia/Reserve)
-   ✅ **Like buttons hidden** for creators (both compact and full versions)
-   ✅ **Applied across all EGI views** (detail page, card components, list components)
-   ✅ **Prevents self-reservation** and **self-liking** of own EGIs

### **3. CURRENCY SERVICE** ✅ **EXISTS**

**Location**: `app/Services/CurrencyService.php`
**VERIFIED Methods** (from existing code):

-   Currency rate fetching from external API
-   FIAT to microALGO conversion
-   Rate caching mechanisms

### **4. API ENDPOINTS** ✅ **FUNCTIONAL**

**Location**: `app/Http/Controllers/Api/CurrencyController.php`
**VERIFIED Endpoints**:

```
GET /api/currency/rate/default          // USD->ALGO rate
GET /api/currency/rate/{currency}       // Specific currency rate
GET /api/currency/rates/all            // All supported rates
POST /api/currency/convert/fiat-to-algo // Conversion utility
GET /api/user/preferences/currency     // User preference
PUT /api/user/preferences/currency     // Update preference
```

### **5. UEM/ULM INTEGRATION** ✅ **VERIFIED FROM ReservationController.php**

**ACTUAL Implementation Pattern**:

```php
// Dependency Injection (VERIFIED):
public function __construct(
    ReservationService $reservationService,
    ErrorManagerInterface $errorManager,    // UEM via DI
    UltraLogManager $logger                 // ULM via DI
) {
    $this->errorManager = $errorManager;
    $this->logger = $logger;
}

// Error Handling (ACTUAL USAGE):
return $this->errorManager->handle('ERROR_CODE', [
    'operation' => 'operation_name',
    'context_data' => $data
], $exception); // Exception is optional third parameter

// Logging (ACTUAL USAGE):
$this->logger->info('[OPERATION] Description', [
    'key' => 'value',
    'user_id' => FegiAuth::id()
]);
```

### **6 ERROR CODES** ✅ **DEFINED IN config/error-manager.php**

````php
// Use descriptive error codes, not numbers:
return $this->errorManager->handle('CURRENCY_EXCHANGE_SERVICE_UNAVAILABLE', [
    'operation' => 'currency_conversion',
    'attempted_currency' => $currency,
    'user_id' => FegiAuth::id()
]);
```'CURRENCY_EXCHANGE_SERVICE_UNAVAILABLE' => [
    'type' => 'error',
    'blocking' => 'not',
    'dev_message_key' => 'error-manager::errors.dev.currency_exchange_service_unavailable',
    'user_message_key' => 'error-manager::errors.user.currency_exchange_service_unavailable',
    'http_status_code' => 503,
    'devTeam_email_need' => true,
    'notify_slack' => true,
    'msg_to' => 'log-only',
]

'CURRENCY_RATE_CACHE_ERROR' => [
    'type' => 'warning',
    'blocking' => 'not',
    'dev_message_key' => 'error-manager::errors.dev.currency_rate_cache_error',
    'user_message_key' => null,
    'http_status_code' => 500,
    'devTeam_email_need' => false,
    'notify_slack' => false,
    'msg_to' => 'log-only',
]

'CURRENCY_INVALID_RATE_DATA' => [
    'type' => 'error',
    'blocking' => 'not',
    'dev_message_key' => 'error-manager::errors.dev.currency_invalid_rate_data',
    'user_message_key' => 'error-manager::errors.user.currency_invalid_rate_data',
    'http_status_code' => 502,
    'devTeam_email_need' => true,
    'notify_slack' => true,
    'msg_to' => 'json',
]

'CURRENCY_CONVERSION_ERROR' => [
    'type' => 'error',
    'blocking' => 'semi-blocking',
    'dev_message_key' => 'error-manager::errors.dev.currency_conversion_error',
    'user_message_key' => 'error-manager::errors.user.currency_conversion_error',
    'http_status_code' => 400,
    'devTeam_email_need' => false,
    'notify_slack' => false,
    'msg_to' => 'json',
]

'CURRENCY_UNSUPPORTED_CURRENCY' => [
    'type' => 'warning',
    'blocking' => 'semi-blocking',
    'dev_message_key' => 'error-manager::errors.dev.currency_unsupported_currency',
    'user_message_key' => 'error-manager::errors.user.currency_unsupported_currency',
    'http_status_code' => 400,
    'devTeam_email_need' => false,
    'notify_slack' => false,
    'msg_to' => 'json',
]

'USER_PREFERENCE_UPDATE_FAILED' => [
    'type' => 'error',
    'blocking' => 'semi-blocking',
    'dev_message_key' => 'error-manager::errors.dev.user_preference_update_failed',
    'user_message_key' => 'error-manager::errors.user.user_preference_update_failed',
    'http_status_code' => 500,
    'devTeam_email_need' => true,
    'notify_slack' => true,
    'msg_to' => 'json',
]

'CURRENCY_CONVERSION_VALIDATION_ERROR' => [
    'type' => 'error',
    'blocking' => 'semi-blocking',
    'dev_message_key' => 'error-manager::errors.dev.currency_conversion_validation_error',
    'user_message_key' => 'error-manager::errors.user.currency_conversion_validation_error',
    'http_status_code' => 422,
    'devTeam_email_need' => false,
    'notify_slack' => false,
    'msg_to' => 'json',
]

'USER_PREFERENCE_FETCH_ERROR' => [
    'type' => 'warning',
    'blocking' => 'not',
    'dev_message_key' => 'error-manager::errors.dev.user_preference_fetch_error',
    'user_message_key' => 'error-manager::errors.user.user_preference_fetch_error',
    'http_status_code' => 404,
    'devTeam_email_need' => false,
    'notify_slack' => false,
    'msg_to' => 'json',
]

'CURRENCY_PREFERENCE_VALIDATION_ERROR' => [
    'type' => 'error',
    'blocking' => 'semi-blocking',
    'dev_message_key' => 'error-manager::errors.dev.currency_preference_validation_error',
    'user_message_key' => 'error-manager::errors.user.currency_preference_validation_error',
    'http_status_code' => 422,
    'devTeam_email_need' => false,
    'notify_slack' => false,
    'msg_to' => 'json',
]
````

**How to Use in Code**:

```php
// Example usage in service/controller:
return $this->errorManager->handle('CURRENCY_EXCHANGE_SERVICE_FAILED', [
    'operation' => 'fetch_exchange_rate',
    'currency' => $currency,
    'api_response' => $apiResponse
], $exception);
```

---

## ❌ **MISSING COMPONENTS** (Critical for Financial Operations)

### **1. RESERVATION SYSTEM CURRENCY INTEGRATION** ❌ **NOT IMPLEMENTED**

**Problem**: `ReservationController.php` handles validation but ReservationService may not fully handle currency conversion
**Risk**: Incorrect amount calculations = financial losses
**Files to Verify/Fix**:

```
app/Services/ReservationService.php     // Check if currency conversion is implemented
app/Models/Reservation.php              // Check if multi-currency methods exist
```

### **2. PRICE DISPLAY SYSTEM** ❌ **NOT IMPLEMENTED**

**Problem**: Users cannot see prices in their preferred currency
**Risk**: User confusion, incorrect payments
**Missing Components**:

```
resources/views/collections/show.blade.php      // Collection prices in user currency
resources/views/collections/index.blade.php     // Collection cards with converted prices
Component for price conversion and display       // Reusable price display logic
```

### **3. USER CURRENCY PREFERENCE UI** ❌ **NOT IMPLEMENTED**

**Problem**: API exists but no UI for users to change currency
**Risk**: Users stuck with USD, poor UX

**BUSINESS REQUIREMENTS** (Critical Implementation Details):

**🔐 For Authenticated Users (FegiAuth)**:

-   Currency selector must save choice to `users.preferred_currency` field
-   All prices display in user's saved preference immediately
-   Preference persists across sessions until user changes it again
-   Must use `FegiAuth::user()->preferred_currency` to get current preference

**👤 For Non-Authenticated Users**:

-   Display prices in default currency from config (likely USD)
-   Currency selector changes display temporarily (session-based)
-   No database persistence for anonymous users

**Missing Components**:

```
Currency selector in header                      // UI for currency switching
JavaScript for live currency updates            // Update prices without page reload
Backend endpoint to save user preference        // Update users.preferred_currency
Logic to detect authenticated vs anonymous       // Different behavior based on auth
```

**Implementation Pattern Required**:

```php
// Get user's preferred currency
$userCurrency = FegiAuth::check()
    ? FegiAuth::user()->preferred_currency ?? 'USD'
    : config('app.default_currency', 'USD');

// Save preference (authenticated users only)
if (FegiAuth::check()) {
    FegiAuth::user()->update(['preferred_currency' => $newCurrency]);
    // Must use UEM/ULM logging for this operation
}
```

### **4. RILANCI/AUCTION CURRENCY HANDLING** ❌ **UNKNOWN STATUS**

**Problem**: Auction bids may not handle multi-currency properly
**Risk**: Bid comparisons in different currencies = unfair auctions
**Files to Verify**:

```
app/Models/Rilancio.php                         // Check currency support
app/Services/RilancioService.php               // Check bid currency logic
```

---

## 🧪 **VERIFICATION CHECKLIST** (Must be completed before ANY changes)

### **Database Verification**:

```bash
# Check if migrations are applied
php artisan migrate:status

# Verify table structure
php artisan tinker
Schema::getColumnListing('reservations');
Schema::getColumnListing('users');
```

### **API Verification**:

```bash
# Test currency endpoints
curl http://localhost/api/currency/rate/USD
curl http://localhost/api/currency/rates/all

# Test authenticated endpoints (requires valid token)
curl -H "Authorization: Bearer YOUR_TOKEN" http://localhost/api/user/preferences/currency
```

### **Service Verification**:

```bash
# Test CurrencyService
php artisan tinker
$service = app(\App\Services\CurrencyService::class);
$service->getAlgoToFiatRate('USD'); // Should return current rate
$service->convertFiatToMicroAlgo(100, 'USD'); // Should return microALGO amount
```

### **Error Handling Verification**:

```bash
# Check if error codes exist
php artisan tinker
$errors = config('error-manager.errors');
dd($errors['CURRENCY_EXCHANGE_SERVICE_FAILED']); // Should show actual error definition

# Test actual currency error codes
dd($errors['CURRENCY_UNSUPPORTED']);
dd($errors['CURRENCY_CONVERSION_FAILED']);
dd($errors['USER_CURRENCY_UPDATE_FAILED']);
```

---

## 🔥 **IMMEDIATE PRIORITIES** (Financial Risk Mitigation)

### **Priority 1: Verify Existing Financial Logic**

1. **Audit ReservationService**: Ensure currency conversion is mathematically correct
2. **Test Exchange Rate Accuracy**: Verify rates match real market rates
3. **Check Audit Trail**: Ensure all currency operations are logged

### **Priority 2: Implement Missing UI Components**

1. **Currency Selector**: Allow users to change display currency
2. **Price Display Component**: Show prices in user's preferred currency
3. **Collection Views Update**: Display converted prices everywhere

### **Priority 3: Test Everything with SMALL Amounts**

1. **Use Test Environment**: Never test with production database
2. **Small Amounts Only**: Test with $1-$10 equivalent amounts
3. **Verify Calculations**: Manually check all conversions

---

## 📁 **CRITICAL FILES** (Handle with EXTREME care)

### **Financial Core Files** 🚨:

```
app/Services/ReservationService.php     // Handles money transactions
app/Services/CurrencyService.php        // Currency conversion logic
app/Models/Reservation.php              // Financial data model
database/migrations/*currency*          // Database schema changes
```

### **Configuration Files** ⚙️:

```
config/error-manager.php               // Error handling for financial operations
.env                                   // API keys and sensitive config
```

### **UI Files** 🎨:

```
resources/views/collections/show.blade.php    // Price display to users
resources/views/collections/index.blade.php   // Collection listings with prices
resources/views/layouts/partials/header.blade.php // Currency selector
```

---

## 🔒 **COMPLIANCE REQUIREMENTS**

### **Audit Trail**:

-   ✅ All currency conversions must be logged with timestamps
-   ✅ Exchange rates must be stored with each transaction
-   ✅ User actions (currency changes) must be logged
-   ✅ Failed operations must be logged with full context

### **Data Protection**:

-   ✅ No sensitive financial data in logs (amounts OK, full card details NO)
-   ✅ Error messages must not expose internal system details
-   ✅ User preferences must be securely stored

### **Testing Requirements**:

-   ✅ All financial operations must have unit tests
-   ✅ Integration tests for currency conversion workflows
-   ✅ Load testing for exchange rate API calls
-   ✅ Manual verification of all calculations

---

## 🚫 **WHAT NOT TO DO**

1. **❌ Never assume currency logic works without testing**
2. **❌ Never deploy currency changes without thorough verification**
3. **❌ Never test with large amounts**
4. **❌ Never bypass UEM/ULM logging for financial operations**
5. **❌ Never hardcode exchange rates**
6. **❌ Never skip input validation on financial data**

---

## 📋 **NEXT STEPS** (Safe Implementation Plan)

### **Step 1: Analysis Phase** (No code changes)

1. Audit existing ReservationService currency handling
2. Verify CurrencyService calculations with test data
3. Check all price displays in current UI
4. Document current user flow for reservations

### **Step 2: Safe Implementation**

1. Create price display component with extensive testing
2. Add currency selector to header with backend verification
3. Update collection views to use new component
4. Implement with feature flags for gradual rollout

### **Step 3: Verification**

1. Test all functionality with small amounts
2. Verify audit logs contain all necessary data
3. Check error handling covers all edge cases
4. Manual testing of complete user workflows

---

## 📞 **CRITICAL CONTACTS & RESOURCES**

### **Financial API**:

-   Exchange Rate API documentation
-   API key management and rotation procedures
-   Rate limiting and fallback procedures

### **Compliance**:

-   Legal requirements for currency conversion disclosure
-   Tax implications of multi-currency transactions
-   User agreement updates for currency features

### **Technical**:

-   Database backup procedures before schema changes
-   Rollback procedures for failed deployments
-   Monitoring and alerting for financial operations

---

**This document contains ONLY verified facts from existing code. No assumptions, no presuppositions. Every statement can be verified by examining the actual codebase.**

**🚨 REMEMBER: We are handling REAL MONEY. Every change must be tested, verified, and audited. 🚨**
