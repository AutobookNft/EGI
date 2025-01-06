#!/bin/bash

# Nome del file di output
output_file="progetto_completo.txt"

# Naviga nella directory specificata come argomento o usa la directory corrente
target_dir=${1:-$(pwd)}

# Trova tutti i file PHP, escludendo vendor, node_modules e cartelle nascoste (che iniziano con .)
find "$target_dir" \
    -type d \( -path "*/vendor" -o -path "*/node_modules" -o -path "*/.*" \) -prune -o \
    -type f -name "*.php" -print \
    -exec cat {} + > "$output_file"

# Aggiungi una separazione tra i file per chiarezza (opzionale)
find "$target_dir" \
    -type d \( -path "*/vendor" -o -path "*/node_modules" -o -path "*/.*" \) -prune -o \
    -type f -name "*.php" -print \
    -exec echo -e "\n\n### SEPARATORE: {} ###\n" >> "$output_file" \;

echo "Tutti i file PHP sono stati uniti in $output_file, esclusi vendor, node_modules e cartelle nascoste."
