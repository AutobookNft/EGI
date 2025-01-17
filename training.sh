#!/usr/bin/env bash

# -----------------------------------------------------
# 1. CONFIGURAZIONE
# -----------------------------------------------------
INPUT_DIR="/home/fabio/EGI/app"   # <-- Modifica con la tua directory
OUTPUT_JSON="dataset_ai.json"
LOG_FILE="elaborazione_log.txt"

# -----------------------------------------------------
# 2. PULIZIA / INIZIALIZZAZIONE FILE DI OUTPUT
# -----------------------------------------------------
> "$OUTPUT_JSON"   # Svuota il JSON
> "$LOG_FILE"       # Svuota il log

echo "[" >> "$OUTPUT_JSON"       # Apre l'array JSON
echo "Inizio elaborazione: $(date)" >> "$LOG_FILE"

# -----------------------------------------------------
# 3. FUNZIONI
# -----------------------------------------------------

# 3.1 - Elabora un singolo file PHP
#      - Legge TUTTO il contenuto
#      - Per ogni classe/interface/trait, crea un oggetto JSON senza metriche
process_file() {
  local file="$1"

  # Leggi TUTTO il file in una variabile (per fornirlo interamente all'AI)
  local full_code
  full_code="$(cat "$file")"

  # Trova tutti i nomi di classi / interfacce / trait
  grep -Po '(?:abstract\s+|final\s+)?(?:class|interface|trait)\s+\K\w+' "$file" | while read -r class_name
  do
    echo "Classe/Interfaccia/Trait trovata: $class_name nel file $file" >> "$LOG_FILE"

    # Creiamo l'oggetto JSON senza metriche
    jq -n \
      --arg name "$class_name" \
      --arg code "$full_code" \
      '{
        name: $name,
        code: $code
      }' >> "$OUTPUT_JSON"

    # Aggiunge una virgola come separatore tra oggetti JSON
    echo "," >> "$OUTPUT_JSON"
  done
}

# -----------------------------------------------------
# 4. MAIN SCRIPT
# -----------------------------------------------------

# Trova i file PHP e li processa in ordine alfabetico
find "$INPUT_DIR" -type f -name "*.php" | sort | while read -r php_file
do
  echo "Elaborazione file: $php_file" >> "$LOG_FILE"
  process_file "$php_file"
done

# Rimuove l'ultima virgola in più e chiude l'array JSON
sed -i '$ s/,$//' "$OUTPUT_JSON"
echo "]" >> "$OUTPUT_JSON"

# Log di fine elaborazione
echo "Elaborazione terminata: $(date)" >> "$LOG_FILE"
echo "File JSON creato: $OUTPUT_JSON" >> "$LOG_FILE"
echo "Log salvato in: $LOG_FILE"
