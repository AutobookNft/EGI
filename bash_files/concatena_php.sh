#!/bin/bash

# --- INIZIO SCRIPT concatena_php.sh ---

# Definisci la cartella di input/output
TARGET_DIR="/home/fabio/AI-files"
OUTPUT_FILE="${TARGET_DIR}/php_files_concatenati.txt"

# Messaggio di inizio
echo "Avvio script di concatenazione file PHP..."
echo "Cartella target: ${TARGET_DIR}"
echo "File di output: ${OUTPUT_FILE}"
echo ""

# Verifica se la cartella target esiste
if [ ! -d "$TARGET_DIR" ]; then
  echo "ERRORE: La cartella '${TARGET_DIR}' non esiste."
  exit 1
fi

# Inizializza/Svuota il file di output e aggiungi un'intestazione generale
echo "##################################################"  > "${OUTPUT_FILE}"
echo "# File PHP concatenati da: ${TARGET_DIR}"          >> "${OUTPUT_FILE}"
echo "# Data: $(date)"                                   >> "${OUTPUT_FILE}"
echo "##################################################" >> "${OUTPUT_FILE}"
echo ""                                                 >> "${OUTPUT_FILE}"

# Contatore per i file processati
file_counter=0

# Trova tutti i file .php nella cartella specificata (non ricorsivo)
# Usiamo find con -print0 e read -d '' per gestire correttamente nomi di file con spazi o caratteri speciali.
find "$TARGET_DIR" -maxdepth 1 -type f -name "*.php" -print0 | while IFS= read -r -d $'\0' php_file; do
  if [ -f "$php_file" ] && [ -r "$php_file" ]; then # Verifica se è un file regolare e leggibile
    echo "Processando: $php_file"

    # Aggiungi il separatore con il percorso del file
    echo "--- START OF FILE: $php_file ---" >> "$OUTPUT_FILE"
    echo ""                                 >> "$OUTPUT_FILE" # Riga vuota per leggibilità

    # Accoda il contenuto del file
    cat "$php_file" >> "$OUTPUT_FILE"

    # Aggiungi un separatore di fine file e una riga vuota
    echo ""                                 >> "$OUTPUT_FILE" # Riga vuota prima della fine
    echo "--- END OF FILE: $php_file ---"   >> "$OUTPUT_FILE"
    echo ""                                 >> "$OUTPUT_FILE" # Riga vuota per separare dal prossimo file
    echo "==================================================" >> "$OUTPUT_FILE"
    echo ""                                 >> "$OUTPUT_FILE"

    file_counter=$((file_counter + 1))
  else
    echo "ATTENZIONE: Il file '$php_file' non è un file regolare o non è leggibile. Saltato."
  fi
done

# Messaggio di fine
echo ""
if [ "$file_counter" -eq 0 ]; then
  echo "Nessun file .php trovato in '${TARGET_DIR}'."
  echo "Il file di output '${OUTPUT_FILE}' contiene solo l'intestazione."
else
  echo "Processo completato. ${file_counter} file .php sono stati concatenati in:"
  echo "${OUTPUT_FILE}"
fi

# --- FINE SCRIPT concatena_php.sh ---
