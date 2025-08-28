# Ultra Standards – ULM & UEM

Gli standard Ultra garantiscono coerenza e leggibilità a lungo termine.
studia bene e a fondo: UEM-ULM-IMPLEMENTATION-GUIDE.md

## ULM (Ultra Log Manager)
- Ogni Manager deve usare ULM per logging centralizzato.
- Vietato usare `Log::info()` o simili direttamente.
- Livelli: DEBUG, INFO, WARNING, ERROR.
- Ogni entry deve avere: timestamp, classe, metodo, messaggio, contesto.

## UEM (Ultra Error Manager)
- Tutti gli errori/exception passano da UEM.
- Obbligo: nessuna `throw new Exception()` diretta.
- Ogni eccezione personalizzata deve estendere da `UltraBaseException`.
- UEM deve registrare: tipo errore, stacktrace, contesto.

## Obblighi aggiuntivi
- Se un nuovo modulo viene introdotto → aggiornare `semantic_index.json`.
- Ogni refactor che cambia API pubblica → aggiungere deprecazione documentata.
