#!/bin/bash

# Nome dei file di output
output_file="progetto_completo.txt"
log_file="compilazione_log.txt"
timestamp=$(date +"%Y%m%d%H%M%S")
zip_file="progetto_completo_$timestamp.zip"
hash_file="file_hashes.txt"
prev_hash_file="file_hashes_prev.txt"
shared_dir="/var/www/shared"
modified_files_file="$shared_dir/modified_files.txt"

# Directory target (usa quella corrente se non specificata)
target_dir=${1:-$(pwd)}

# Assicurati che la directory condivisa esista
mkdir -p "$shared_dir"

# Backup degli hash precedenti
if [ -f "$hash_file" ]; then
    mv "$hash_file" "$prev_hash_file"
fi

# Funzione per generare hash dei file inclusi (opzionale, per il tuo controllo)
generate_hashes() {
    echo "### Hash dei File Inclusi ###" > "$hash_file"
    find "$target_dir" \
        -type d \( -path "*/vendor" -o -path "*/node_modules" -o -path "*/.*" -o -path "*/storage/framework" \) -prune -o \
        -type f \( -name "*.php" -o -name "*.ts" -o -name "*.js" -o -name "*.env" -o -name "*.config.js" \) -print | sort | while read -r file; do
            hash=$(sha256sum "$file" | awk '{print $1}')
            echo "$file: $hash" >> "$hash_file"
        done
}

# Funzione per confrontare gli hash (opzionale, per il tuo controllo)
compare_hashes() {
    if [ -f "$prev_hash_file" ]; then
        echo "### File Modificati ###" > "$modified_files_file"
        while IFS= read -r line; do
            current_file=$(echo "$line" | awk -F": " '{print $1}')
            current_hash=$(echo "$line" | awk -F": " '{print $2}')
            prev_hash=$(grep -F "$current_file" "$prev_hash_file" | awk -F": " '{print $2}')
            if [ "$current_hash" != "$prev_hash" ]; then
                echo "$current_file" >> "$modified_files_file"
            fi
        done < "$hash_file"
    else
        echo "Nessun file hash precedente trovato." > "$modified_files_file"
    fi
}

# Inizia il log
echo "Inizio compilazione: $(date)" > "$log_file"
file_count=0
excluded_count=0
excluded_files=()

# Trova e unifica tutti i file rilevanti
{
    echo -e "######## Progetto Completo - Generato il $(date) ########\n" > "$output_file"
    find "$target_dir" \
        -type d \( -path "*/vendor" -o -path "*/node_modules" -o -path "*/.*" -o -path "*/storage/framework" \) -prune -o \
        -type f \( -name "*.php" -o -name "*.ts" -o -name "*.js" -o -name "*.env" -o -name "*.config.js" \) -print | sort | while read -r file; do
            if [[ $file == *"vendor"* || $file == *"node_modules"* || $file == *"/.*"* || $file == *"storage/framework"* ]]; then
                excluded_files+=("$file")
                ((excluded_count++))
                continue
            fi
            # Aggiungi un separatore chiaro con il percorso completo del file
            echo -e "\n######## File: $file ########\n" >> "$output_file"
            # Aggiungi il contenuto del file
            cat "$file" >> "$output_file"
            echo -e "\n" >> "$output_file" # Linea vuota per separazione
            echo "File aggiunto: $file" >> "$log_file"
            ((file_count++))
        done

    # Genera hash dei file (opzionale)
    generate_hashes
    # Confronta gli hash con la versione precedente (opzionale)
    compare_hashes
} || {
    echo "Errore durante la compilazione." >> "$log_file"
}

# Aggiungi i file esclusi al log
echo -e "\nFile esclusi:" >> "$log_file"
for file in "${excluded_files[@]}"; do
    echo "$file" >> "$log_file"
done

# Riepilogo nel log
echo -e "\nTotale file processati: $file_count" >> "$log_file"
echo -e "Totale file esclusi: $excluded_count" >> "$log_file"
echo "Fine compilazione: $(date)" >> "$log_file"

# Comprimi il file unificato in un archivio .zip
zip -q "$zip_file" "$output_file" "$log_file" "$hash_file" "$modified_files_file"
echo "File compressi in: $zip_file" >> "$log_file"

echo "Compilazione completata! File unificato: $output_file | Log: $log_file | Archivio: $zip_file | Hashes: $hash_file | Modifiche: $modified_files_file"