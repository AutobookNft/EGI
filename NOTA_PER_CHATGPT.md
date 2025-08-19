📝 NOTA PER CHATGPT - Gestione Commit e Time Tracking nel Progetto EGI
===========================================================================

Ciao! Sono Fabio, sviluppatore del progetto EGI. Ti scrivo alcune informazioni importanti che devi sapere quando lavoriamo insieme:

## 🏷️ SISTEMA DI COMMIT CON TAG OBBLIGATORI

Dal 19 agosto 2025 abbiamo implementato un sistema di commit strutturato che DEVI sempre rispettare:

### Formato obbligatorio:
```
[TAG] Descrizione breve e chiara

- Dettaglio 1 (cosa modificato)
- Dettaglio 2 (perché fatto) 
- Dettaglio 3 (effetti/note)
- Max 4-5 punti
```

### TAG disponibili:
- `[FEAT]` = nuova feature o funzionalità
- `[FIX]` = bug risolto
- `[REFACTOR]` = refactoring del codice senza cambiare logica
- `[DOC]` = documentazione aggiunta o aggiornata
- `[TEST]` = aggiunta o modifica di test
- `[CHORE]` = attività di manutenzione o setup (config, dipendenze, script, ecc.)

### Esempi corretti:
```bash
[FIX] Risolto errore getUrl() su null nell'header

- Aggiunto controllo $user esistenza prima di chiamare profile_photo_url
- Sostituito getCurrentProfileImage()->getUrl() con accessor automatico
- Rimosso div spurio che causava disallineamento navbar
- Gestione robusta per utenti non autenticati
```

```bash
[FEAT] Aggiunta analisi commit per categorie TAG

- Implementato parsing messaggi commit per riconoscere TAG
- Aggiunto conteggio e percentuali per ogni categoria
- Creata sezione statistiche TAG con tipo più frequente
- Aggiunti suggerimenti intelligenti basati sulla distribuzione TAG
```

## 📊 SISTEMA DI MONITORAGGIO PRODUTTIVITÀ

Abbiamo creato degli script bash per monitorare:

1. **commit-range-stats.sh** - Analizza commit per periodo con statistiche TAG
2. **coding-time-tracker.sh** - Stima tempo di sviluppo basato sui commit

### Comandi utili:
```bash
# Statistiche ultimi 7 giorni
./bash_files/commit-range-stats.sh

# Statistiche periodo specifico  
./bash_files/commit-range-stats.sh 2025-08-15 2025-08-19

# Analisi tempo di coding
./bash_files/coding-time-tracker.sh 2025-08-19
```

## ⏱️ TIME TRACKING E AI ASSISTANCE

IMPORTANTE: Abbiamo installato WakaTime per tracciare il tempo effettivo di coding.

### Problema identificato:
- **WakaTime** traccia solo digitazione umana manuale
- **Le tue modifiche AI** (replace_string_in_file, create_file) vengono sottostimate
- **Gap di tracking**: Il tempo di collaborazione AI-umano non viene registrato correttamente

### Implicazioni per noi:
1. **WakaTime mostrerà meno tempo** di quello realmente dedicato al progetto
2. **Il nostro script custom** stima meglio il tempo totale progetto (incluso AI)
3. **La differenza** tra i due sistemi indica l'efficienza dell'AI assistance

### Cosa devi sapere:
- Quando fai modifiche massive con i tuoi tool, WakaTime non le conta come "tempo di coding"
- Il tempo che passo a revieware/testare le tue modifiche potrebbe non essere tracciato
- È normale che WakaTime mostri meno ore rispetto al tempo reale del progetto

## 📈 OBIETTIVI DI MONITORAGGIO

Voglio tracciare:
- **Produttività giornaliera** (commit count + categorie)  
- **Tempo dedicato al progetto** (script custom vs WakaTime)
- **Efficienza AI collaboration** (differenza tra i due tracking)
- **Bilanciamento tipologie** (FEAT vs FIX vs REFACTOR)

## 🎯 COSA DEVI FARE

1. **SEMPRE usare i TAG** nei commit message
2. **Seguire il formato** [TAG] + descrizione + bullet points
3. **Essere consapevole** che le tue modifiche influenzano i time tracking
4. **Suggerire ottimizzazioni** per bilanciare le categorie di commit

## 📋 FILE DI RIFERIMENTO

- `regole per commit.txt` - Regole complete formato commit
- `bash_files/commit-range-stats.sh` - Script analisi commit  
- `bash_files/coding-time-tracker.sh` - Script analisi tempo

---

Grazie per aver letto! Seguendo queste regole mi aiuterai a tenere traccia accurata della produttività e dell'evoluzione del progetto EGI.

Fabio Cherici  
19 agosto 2025
