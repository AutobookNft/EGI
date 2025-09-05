# GDPR Privacy Levels System

## Panoramica

Il sistema FlorenceEGI utilizza un approccio centralizzato per la gestione dei livelli di privacy tramite l'enum `PrivacyLevel`. Questo garantisce coerenza tra tutti i componenti della piattaforma che gestiscono dati sensibili e conformità GDPR.

## Enum PrivacyLevel

L'enum `App\Enums\Gdpr\PrivacyLevel` definisce quattro livelli di privacy distinti:

### STANDARD

-   **Valore**: `standard`
-   **Ritenzione**: 730 giorni (2 anni)
-   **Audit GDPR**: No
-   **Descrizione**: Attività generali della piattaforma e consensi non critici
-   **Esempi**: Analytics, marketing, personalizzazione

### HIGH

-   **Valore**: `high`
-   **Ritenzione**: 1095 giorni (3 anni)
-   **Audit GDPR**: No
-   **Descrizione**: Sicurezza e dati correlati all'autenticazione
-   **Esempi**: Login, logout, registrazione, servizi essenziali

### CRITICAL

-   **Valore**: `critical`
-   **Ritenzione**: 2555 giorni (7 anni)
-   **Audit GDPR**: Sì
-   **Descrizione**: Operazioni sensibili GDPR e dati personali
-   **Esempi**: Elaborazione dati personali, partecipazione collaborazione, termini di servizio

### IMMUTABLE

-   **Valore**: `immutable`
-   **Ritenzione**: 3650 giorni (10 anni)
-   **Audit GDPR**: Sì
-   **Descrizione**: Audit trail permanente e conformità legale
-   **Esempi**: Record permanenti, log di sicurezza critici

## Integrazione Sistema

### GdprActivityCategory

Ogni categoria di attività ritorna automaticamente il `PrivacyLevel` appropriato:

```php
$category = GdprActivityCategory::GDPR_ACTIONS;
$privacyLevel = $category->privacyLevel(); // PrivacyLevel::CRITICAL
$retentionDays = $category->retentionDays(); // 2555
$requiresAudit = $category->requiresGdprAudit(); // true
```

### ConsentService

Il servizio di consenso utilizza il `PrivacyLevel` per determinare la ritenzione:

```php
$consentService = app(ConsentService::class);
// Internamente usa PrivacyLevel per mappare i tipi di consenso
```

### User Activities

La tabella `user_activities` memorizza i livelli di privacy come stringhe che corrispondono ai valori dell'enum.

## Comandi Artisan

### Migrazione Livelli Privacy

```bash
# Dry run per vedere cosa cambierebbe
php artisan gdpr:migrate-privacy-levels --dry-run

# Esecuzione effettiva della migrazione
php artisan gdpr:migrate-privacy-levels
```

### Test Coerenza Sistema

```bash
# Verifica che tutti i componenti usino correttamente PrivacyLevel
php artisan gdpr:test-privacy-consistency
```

## Vantaggi del Sistema Centralizzato

1. **Coerenza**: Tutti i componenti utilizzano gli stessi valori
2. **Manutenibilità**: Modifiche centrali propagate automaticamente
3. **Type Safety**: Enum previene errori di battitura
4. **Documentazione**: Descrizioni e metadati integrati
5. **Testing**: Facile verifica della coerenza

## Metodi Disponibili

### PrivacyLevel::retentionDays()

Ritorna il numero di giorni di ritenzione per il livello di privacy.

### PrivacyLevel::description()

Ritorna una descrizione human-readable del livello.

### PrivacyLevel::color()

Ritorna un codice colore esadecimale per rappresentazione UI.

### PrivacyLevel::requiresGdprAudit()

Indica se il livello richiede audit GDPR completo.

### PrivacyLevel::options()

Ritorna un array associativo per form/select.

## Conformità GDPR

Il sistema è progettato per supportare la conformità GDPR attraverso:

-   **Ritenzione differenziata**: Periodi più lunghi per dati critici
-   **Audit trail**: Tracciamento completo per livelli critici e immutabili
-   **Eliminazione automatica**: Supporto per retention policies
-   **Documentazione trasparente**: Chiarezza sui periodi di conservazione

## Best Practices

1. **Sempre utilizzare l'enum**: Non hardcodare mai i valori stringa
2. **Testare regolarmente**: Usare `gdpr:test-privacy-consistency`
3. **Migrazioni sicure**: Sempre dry-run prima delle migrazioni
4. **Documentare modifiche**: Aggiornare questa documentazione per nuovi livelli
5. **Review periodiche**: Verificare l'appropriatezza dei livelli assegnati

## Estensioni Future

Per aggiungere nuovi livelli di privacy:

1. Aggiungere il caso all'enum `PrivacyLevel`
2. Implementare la logica nei metodi `retentionDays()` e `requiresGdprAudit()`
3. Aggiornare le mappature in `GdprActivityCategory` e `ConsentService`
4. Eseguire i test di coerenza
5. Aggiornare questa documentazione

---

_Documentazione generata per FlorenceEGI MVP v1.0.0_
_Ultimo aggiornamento: Gennaio 2025_
