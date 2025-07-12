# Biography Module API-First Refactor

## Overview

Il modulo Biography è stato completamente rifattorizzato secondo la specifica API-First, centralizzando la logica di business nel `BiographyService` e fornendo endpoint RESTful consistenti.

## Architettura

### 1. Service Layer (`BiographyService`)

-   **File**: `app/Services/BiographyService.php`
-   **Responsabilità**: Logica di business centralizzata
-   **Funzionalità**:
    -   CRUD biografie e capitoli
    -   Gestione media con Spatie Media Library
    -   Validazioni business
    -   Logging GDPR e audit trail

### 2. Request Classes

-   **`BiographyRequest`**: Validazione dati biografie
-   **`ChapterRequest`**: Validazione dati capitoli
-   **`ReorderRequest`**: Validazione riordinamento capitoli

### 3. Resource Classes

-   **`BiographyResource`**: Formattazione risposte API biografie
-   **`BiographyChapterResource`**: Formattazione risposte API capitoli

### 4. Controllers API

-   **`BiographyController`**: Endpoint CRUD biografie
-   **`BiographyChapterController`**: Endpoint CRUD capitoli

## API Endpoints

### Biografie

| Metodo | Endpoint                | Descrizione                |
| ------ | ----------------------- | -------------------------- |
| POST   | `/api/biographies`      | Crea/aggiorna biografia    |
| GET    | `/api/biographies/{id}` | Recupera biografia singola |
| DELETE | `/api/biographies/{id}` | Elimina biografia          |
| GET    | `/api/biographies`      | Lista biografie utente     |

### Capitoli

| Metodo | Endpoint                                 | Descrizione       |
| ------ | ---------------------------------------- | ----------------- |
| POST   | `/api/biographies/{id}/chapters`         | Crea capitolo     |
| PUT    | `/api/biographies/{id}/chapters/{cid}`   | Aggiorna capitolo |
| DELETE | `/api/biographies/{id}/chapters/{cid}`   | Elimina capitolo  |
| PUT    | `/api/biographies/{id}/chapters/reorder` | Riordina capitoli |

## Autenticazione

Tutti gli endpoint richiedono autenticazione Sanctum:

```php
Route::middleware(['auth:sanctum'])->group(function () {
    // Biography routes...
});
```

## Struttura Dati

### Biography

```json
{
    "id": 1,
    "user_id": 1,
    "type": "chapters",
    "title": "La mia vita",
    "content": "Contenuto HTML/JSON",
    "excerpt": "Breve descrizione",
    "is_public": false,
    "is_completed": false,
    "settings": {
        "theme": "modern",
        "show_timeline": true
    },
    "media": {
        "featured_image": "url",
        "gallery": []
    },
    "chapters": [],
    "created_at": "2025-01-07T10:00:00Z"
}
```

### Chapter

```json
{
    "id": 1,
    "biography_id": 1,
    "title": "Infanzia",
    "content": "Contenuto capitolo",
    "date_from": "1990-01-01",
    "date_to": "2000-12-31",
    "is_ongoing": false,
    "sort_order": 1,
    "is_published": true,
    "chapter_type": "standard",
    "formatting_data": {
        "text_align": "left",
        "highlight_color": "#ff0000"
    },
    "media": {
        "featured_image": "url",
        "images": []
    }
}
```

## Validazioni

### BiographyRequest

-   `type`: required, in:single,chapters
-   `title`: required, max:255
-   `content`: required if type=single
-   `excerpt`: nullable, max:500
-   `is_public`: boolean
-   `is_completed`: boolean
-   `settings`: nullable, array

### ChapterRequest

-   `title`: required, max:255
-   `content`: required
-   `date_from`: nullable, date
-   `date_to`: nullable, date, after_or_equal:date_from
-   `is_ongoing`: boolean
-   `sort_order`: nullable, integer, min:0
-   `is_published`: boolean
-   `chapter_type`: nullable, in:standard,milestone,achievement

## Media Management

### Spatie Media Library Integration

-   **Biografie**: `featured_image`, `main_gallery`
-   **Capitoli**: `chapter_featured`, `chapter_images`

### Conversioni Automatiche

-   `thumb`: 150x150
-   `card`: 300x200
-   `full`: 1200x800

## Error Handling

Tutti gli endpoint utilizzano l'ecosistema Ultra per la gestione degli errori:

-   Logging strutturato con `UltraLogManager`
-   Gestione errori con `ErrorManagerInterface`
-   Audit trail GDPR completo

## Esempi di Utilizzo

### Creare una biografia

```bash
curl -X POST /api/biographies \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "type": "chapters",
    "title": "La mia storia",
    "excerpt": "Una biografia personale",
    "is_public": false
  }'
```

### Aggiungere un capitolo

```bash
curl -X POST /api/biographies/1/chapters \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Infanzia",
    "content": "I miei primi anni...",
    "date_from": "1990-01-01",
    "date_to": "2000-12-31",
    "is_published": true
  }'
```

## Compatibilità

### Modelli Esistenti

-   I modelli `Biography` e `BiographyChapter` sono rimasti invariati
-   Tutte le relazioni e accessor esistenti funzionano
-   Spatie Media Library integration preservata

### Database

-   Nessuna migrazione richiesta
-   Schema esistente completamente compatibile
-   Indici e vincoli preservati

## Prossimi Passi

1. **Frontend SPA**: Implementare client TypeScript per consumare l'API
2. **Editor Integration**: Integrare editor di terze parti per contenuti HTML/JSON
3. **Testing**: Aggiungere test unitari e di integrazione
4. **Documentation**: Generare documentazione OpenAPI/Swagger

## Note Tecniche

-   **Versioning**: API v2.0.0 (API-First)
-   **Authentication**: Sanctum tokens
-   **Validation**: Form Request classes con messaggi localizzati
-   **Response Format**: JSON consistente con meta information
-   **Error Codes**: Codici HTTP standard con messaggi descrittivi
