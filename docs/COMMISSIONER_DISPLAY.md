# Commissioner Display System - AGGIORNATO

## Panoramica
Sistema di visualizzazione differenziata per gli attivatori EGI basato sui ruoli utente con avatar Spatie Media.

## Ruoli Utente

### Collector (Normale)
- **Visualizzazione**: Icona generica + indirizzo wallet abbreviato
- **Formato indirizzo**: `ABCDEF...1234` (primi 6 + ultimi 4 caratteri)
- **Avatar**: Icona generica grigia
- **Permessi**: NON ha permessi di visibilità pubblica

### Commissioner (Committente)
- **Visualizzazione**: Avatar Spatie Media + nome e cognome reali
- **Formato nome**: `Nome Cognome` (o fallback su `name`)
- **Avatar**: Immagine da Spatie Media (`avatar` o `profile_photo` collection)
- **Permessi**: HA permessi di visibilità pubblica
  - `display_public_name_on_egi`
  - `display_public_avatar_on_egi`

## Correzioni Implementate ✅

### 1. Rimozione Etichetta "(Committente)"
- ❌ PRIMA: Mostrava `Nome Cognome (Committente)`
- ✅ DOPO: Mostra solo `Nome Cognome`

### 2. Gestione Avatar Spatie Media
- **Collections supportate**: `avatar` e `profile_photo`
- **Fallback sicuro**: Se avatar non disponibile, mostra icona colorata
- **Styling**: Bordi e dimensioni ottimizzate per ogni contesto

### 3. Traduzioni Multilingua
- **Chiave**: `guest_layout.fegi_user_type.commissioner`
- **Lingue supportate**: 
  - 🇮🇹 IT: "Committente"
  - 🇬🇧 EN: "Commissioner" 
  - 🇫🇷 FR: "Commanditaire"
  - 🇪🇸 ES: "Comisionado"
  - 🇩🇪 DE: "Auftraggeber"
  - 🇵🇹 PT: "Comissionário"

## Implementazione Tecnica

### Helper Functions
```php
// helpers.php
formatActivatorDisplay($user) // Restituisce array con dati di visualizzazione
getGenericActivatorIcon($classes) // Restituisce SVG icona generica
```

### Database - Ruoli e Permessi
- **Seeder**: `RolesAndPermissionsSeeder.php`
- **Ruolo**: `commissioner` (eredita tutti i permessi di `collector` + i permessi di visibilità)

### Template Modificati
1. `resources/views/components/egi-card-list.blade.php`
2. `resources/views/components/egi-card.blade.php` 
3. `resources/views/egis/show.blade.php`

### Traduzioni
- `resources/lang/it/common.php`: Aggiunta chiave `commissioner`

## Test

### Comando di Test
```bash
php artisan test:commissioner-display
```

Questo comando:
1. Crea utenti di test (collector + commissioner)
2. Verifica le funzioni di visualizzazione
3. Controlla i permessi Spatie

### Output Atteso
- **Collector**: Mostra `ABCDEF...1234` - Permessi: NO/NO
- **Commissioner**: Mostra `Nome Cognome` - Permessi: YES/YES

## Installazione

1. **Eseguire il seeder**:
```bash
php artisan db:seed --class=RolesAndPermissionsSeeder
```

2. **Ricompilare autoloader**:
```bash
composer dump-autoload
```

3. **Build frontend** (opzionale):
```bash
npm run build
```

## Utilizzo

### Assegnare Ruolo Commissioner
```php
$user->assignRole('commissioner');
```

### Verifica Permessi
```php
$user->can('display_public_name_on_egi')
$user->can('display_public_avatar_on_egi')
```

## Privacy e GDPR

- **Collector**: Privacy protetta (solo wallet abbreviato)
- **Commissioner**: Visibilità pubblica su consenso esplicito tramite permessi Spatie

## File Modificati

### Core
- `helpers.php` - Funzioni helper
- `database/seeders/RolesAndPermissionsSeeder.php` - Ruoli e permessi
- `resources/lang/it/common.php` - Traduzioni

### Templates
- `resources/views/components/egi-card-list.blade.php`
- `resources/views/components/egi-card.blade.php`
- `resources/views/egis/show.blade.php`

### Testing
- `app/Console/Commands/TestCommissionerDisplay.php` - Comando di test

## Note di Sicurezza

1. I permessi sono gestiti tramite **Spatie Laravel Permission**
2. La visibilità è **opt-in** (solo commissioner con permessi espliciti)
3. Fallback sicuri in caso di dati mancanti
4. Abbreviazione wallet per privacy collector
