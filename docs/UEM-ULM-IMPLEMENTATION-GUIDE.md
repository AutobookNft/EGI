# Guida Implementazione UEM (Ultra Error Manager) e ULM (Ultra Log Manager)

## Esempio Pratico: GdprController

### Overview
Questo documento illustra l'implementazione corretta di UEM e ULM nel contesto di FlorenceEGI, usando come esempio il `GdprController` che gestisce la compliance GDPR.

## 1. Setup e Dependency Injection

### Import delle Dipendenze

```php
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
```

### Constructor con Dependency Injection

```php
/**
 * Constructor with dependency injection
 *
 * @param UltraLogManager $logger
 * @param ErrorManagerInterface $errorManager
 * @param GdprService $gdprService
 * @param ConsentService $consentService
 * @param DataExportService $exportService
 * @param AuditLogService $auditService
 * @param ProcessingRestrictionService $processingRestrictionService
 */
public function __construct(
    UltraLogManager $logger,
    ErrorManagerInterface $errorManager,
    GdprService $gdprService,
    ConsentService $consentService,
    DataExportService $exportService,
    AuditLogService $auditService,
    ProcessingRestrictionService $processingRestrictionService
) {
    $this->logger = $logger;
    $this->errorManager = $errorManager;
    $this->gdprService = $gdprService;
    $this->consentService = $consentService;
    $this->exportService = $exportService;
    $this->auditService = $auditService;
    $this->processingRestrictionService = $processingRestrictionService;
}
```

## 2. Implementazione ULM (Ultra Log Manager)

### Logging di Warning

```php
// Log warning senza interrompere il flusso
if ($this->logger) {
    $this->logger->warning('[GDPR Profile] Failed to load consent status', [
        'user_id' => $user->id,
        'error' => $e->getMessage()
    ]);
}
```

### Pattern di Logging

- **Prefisso Contestuale**: `[GDPR Profile]` identifica immediatamente il modulo
- **Dati Strutturati**: Array associativo con informazioni rilevanti
- **Controllo Null-Safe**: Verifica esistenza logger prima dell'uso

## 3. Implementazione UEM (Ultra Error Manager)

### Gestione Errori Completa

```php
try {
    // Logica business
    $user = auth()->user();
    
    // Operazioni che potrebbero fallire
    $consentStatus = $this->consentService->getUserConsentStatus($user);
    
    return view('gdpr.profile', [
        'user' => $user,
        'consentStatus' => $consentStatus,
        // ... altri dati
    ]);
    
} catch (\Exception $e) {
    // Gestione errore con UEM
    $this->errorManager->handle('GDPR_PROFILE_PAGE_LOAD_ERROR', [
        'user_id' => auth()->id(),
        'ip_address' => request()->ip(),
        'user_agent' => request()->userAgent(),
        'error' => $e->getMessage()
    ], $e);

    // Return error view on failure
    return view('error.generic', [
        'message' => __('gdpr.errors.general'),
        'return_url' => route('dashboard')
    ]);
}
```

### Componenti della Gestione Errore

1. **Codice Errore**: `GDPR_PROFILE_PAGE_LOAD_ERROR` - univoco e descrittivo
2. **Contesto**: Array con tutti i dati utili per debug
3. **Exception**: Passata come terzo parametro per stack trace completo
4. **Fallback**: Vista di errore user-friendly

## 4. Configurazione Error Manager

### Mappatura Codici Errore in `error-manager.php`

```php
'GDPR_CONSENT_LOAD_ERROR' => [
    'type' => 'error',
    'blocking' => 'not',
    'dev_message_key' => 'error-manager::errors.dev.gdpr_consent_load_error',
    'user_message_key' => 'error-manager::errors.user.gdpr_consent_load_error',
    'http_status_code' => 500,
    'devTeam_email_need' => false,
    'notify_slack' => false,
    'msg_to' => 'sweet-alert',
],
```

### Parametri di Configurazione

| Parametro | Valore | Descrizione |
|-----------|--------|-------------|
| `type` | `error` | Tipo di errore (error, warning, info) |
| `blocking` | `not` | Se l'errore blocca l'operazione |
| `dev_message_key` | `error-manager::errors.dev.*` | Chiave per messaggio sviluppatore |
| `user_message_key` | `error-manager::errors.user.*` | Chiave per messaggio utente |
| `http_status_code` | `500` | Codice HTTP da ritornare |
| `devTeam_email_need` | `false` | Se notificare via email il team |
| `notify_slack` | `false` | Se notificare su Slack |
| `msg_to` | `sweet-alert` | Tipo di notifica UI |

## 5. Pattern di Implementazione

### Try-Catch con Fallback Graceful

```php
try {
    // Operazione principale
    $consentStatus = $this->consentService->getUserConsentStatus($user);
} catch (\Exception $e) {
    // Log ma non fallire completamente
    if ($this->logger) {
        $this->logger->warning('[Module] Operation failed', [
            'context' => $relevantData,
            'error' => $e->getMessage()
        ]);
    }
    
    // Valore di fallback
    $consentStatus = null;
}
```

### Error Handling Critico

```php
try {
    // Operazione critica
    $criticalData = $this->service->getCriticalData();
    
} catch (\Exception $e) {
    // Log e gestione errore completa
    $this->errorManager->handle('CRITICAL_OPERATION_FAILED', [
        'user_id' => auth()->id(),
        'operation' => 'getCriticalData',
        'timestamp' => now()->toIso8601String(),
        'context' => $additionalContext
    ], $e);
    
    // Redirect o vista di errore
    return redirect()->route('error.page')
        ->with('error', __('errors.critical_failure'));
}
```

## 6. Best Practices

### Nomenclatura Codici Errore

- **Formato**: `MODULE_ACTION_ERROR_TYPE`
- **Esempi**:
  - `GDPR_PROFILE_PAGE_LOAD_ERROR`
  - `PAYMENT_DISTRIBUTION_CALCULATION_ERROR`
  - `RESERVATION_RANK_UPDATE_ERROR`

### Livelli di Logging

| Livello | Uso | Esempio |
|---------|-----|---------|
| `debug` | Informazioni di debug | Valori intermedi, stato oggetti |
| `info` | Operazioni normali | Login utente, creazione record |
| `warning` | Problemi non bloccanti | Fallback attivato, retry necessario |
| `error` | Errori gestiti | Validazione fallita, risorsa non trovata |
| `critical` | Errori di sistema | Database down, servizio esterno KO |

### Contesto Sempre Completo

```php
$context = [
    // Identificatori
    'user_id' => auth()->id(),
    'session_id' => session()->getId(),
    
    // Request info
    'ip_address' => request()->ip(),
    'user_agent' => request()->userAgent(),
    'request_id' => request()->header('X-Request-ID'),
    
    // Business context
    'operation' => 'specific_operation_name',
    'entity_type' => 'Reservation',
    'entity_id' => $reservation->id,
    
    // Timing
    'timestamp' => now()->toIso8601String(),
    'execution_time' => $executionTime,
    
    // Error details
    'error_message' => $e->getMessage(),
    'error_code' => $e->getCode()
];
```

## 7. Testing

### Unit Test per Error Handling

```php
public function test_handles_consent_load_error_gracefully()
{
    // Mock del servizio che fallisce
    $this->mock(ConsentService::class)
        ->shouldReceive('getUserConsentStatus')
        ->andThrow(new \Exception('Service unavailable'));
    
    // Mock di UEM
    $this->mock(ErrorManagerInterface::class)
        ->shouldReceive('handle')
        ->once()
        ->withArgs(function($code, $context, $exception) {
            return $code === 'GDPR_CONSENT_LOAD_ERROR'
                && isset($context['user_id'])
                && $exception instanceof \Exception;
        });
    
    // Esegui e verifica che non crashi
    $response = $this->actingAs($this->user)
        ->get(route('gdpr.profile'));
    
    $response->assertStatus(200);
    $response->assertViewHas('consentStatus', null);
}
```

## 8. Monitoraggio e Alert

### Dashboard Metriche

- **Error Rate**: Errori per minuto/ora
- **Error Distribution**: Per codice errore
- **User Impact**: Utenti affetti da errori
- **Recovery Time**: Tempo di recupero da errori

### Alert Configuration

```php
// In AppServiceProvider o dedicated provider
if (app()->environment('production')) {
    // Alert per errori critici
    ErrorManager::alertOn(['CRITICAL_*', 'PAYMENT_*']);
    
    // Daily digest per warning
    ErrorManager::digestOn(['WARNING_*'], 'daily');
}
```

## Conclusione

L'implementazione corretta di UEM e ULM garantisce:
- **Tracciabilità completa** di tutte le operazioni
- **Debug efficiente** con contesto dettagliato
- **User experience** protetta da errori graceful
- **Monitoraggio proattivo** dei problemi
- **Compliance** con requisiti di audit

Seguendo questi pattern, ogni componente di FlorenceEGI mantiene standard elevati di robustezza e manutenibilità.