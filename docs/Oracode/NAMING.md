# Oracode Naming Standards

Tutte le classi, metodi e file devono rispettare convenzioni precise.

## Classi
- Manager → suffisso `Manager` (es. `PaymentManager`).
- Exception → suffisso `Exception` (es. `WalletBalanceException`).
- DTO → suffisso `Dto` (es. `UserLoginDto`).
- Enum → suffisso `Enum` (es. `CurrencyEnum`).

## Metodi
- Getter: `getX()`
- Setter: `setX()`
- Finder: `findByX()`
- Azioni: verbo all’infinito inglese (es. `createReservation()`).

## File / Cartelle
- `app/Services/*` → logica applicativa.
- `app/Managers/*` → orchestratori (ULM obbligatorio).
- `app/Exceptions/*` → solo eccezioni.
- `tests/Feature/*` e `tests/Unit/*` → test obbligatori per ogni classe.

## Regole di coerenza
- Vietato creare metodi con nomi già esistenti in altri Manager.
- Vietato introdurre nuovi Service senza interfaccia corrispondente.
