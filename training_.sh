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

# 3.1 - Calcolo (rudimentale) della complessità ciclomatica
# Conta if, else, while, for, case, &&, ||, ?:
calculate_complexity() {
  local file="$1"
  grep -cE '\bif\b|\belse\b|\bwhile\b|\bfor\b|\bcase\b|&&|\|\||\?:' "$file"
}

# 3.2 - Analisi basica dei principi SOLID
analyze_solid() {
  local file="$1"
  # SRP: Quante classi/interfacce/trait ci sono
  local srp=$(grep -cE "(class|interface|trait)" "$file")
  # OCP: Quante volte appare extends o implements
  local ocp=$(grep -cE "extends|implements" "$file")
  # ISP: Quante volte appare la parola interface
  local isp=$(grep -c "interface" "$file")
  # DIP: Costruttori che ricevono un'Interface o Contract
  local dip=$(grep -cE "public function __construct\(.*(Interface|Contract)" "$file")

  echo "$srp,$ocp,$isp,$dip"
}

# 3.3 - Elabora un singolo file PHP
#      - Legge TUTTO il contenuto
#      - Calcola metriche
#      - Per ogni classe/interface/trait, crea un oggetto JSON
process_file() {
  local file="$1"

  # Leggi TUTTO il file in una variabile (per fornirlo interamente all'AI)
  local full_code
  full_code="$(cat "$file")"

  # Calcolo delle metriche
  local complexity
  complexity=$(calculate_complexity "$file")

  local solid_metrics
  solid_metrics=$(analyze_solid "$file")
  local srp ocp isp dip
  srp=$(echo "$solid_metrics" | cut -d',' -f1)
  ocp=$(echo "$solid_metrics" | cut -d',' -f2)
  isp=$(echo "$solid_metrics" | cut -d',' -f3)
  dip=$(echo "$solid_metrics" | cut -d',' -f4)

  # Trova tutti i nomi di classi / interfacce / trait
  # NOTA: si usa \K (invece del lookbehind) per evitare l'errore "lookbehind assertion..."
  grep -Po '(?:abstract\s+|final\s+)?(?:class|interface|trait)\s+\K\w+' "$file" | while read -r class_name
  do
    echo "Classe/Interfaccia/Trait trovata: $class_name nel file $file" >> "$LOG_FILE"

    # Creiamo l'oggetto JSON
    jq -n \
      --arg name "$class_name" \
      --arg code "$full_code" \
      --argjson complexity "$complexity" \
      --argjson srp "$srp" \
      --argjson ocp "$ocp" \
      --argjson isp "$isp" \
      --argjson dip "$dip" \
      '{
        name: $name,
        code: $code,
        metrics: {
          complexity: $complexity,
          srp: $srp,
          ocp: $ocp,
          isp: $isp,
          dip: $dip
        }
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
