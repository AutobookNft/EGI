#!/bin/bash

# Nome dei file di output
output_file="progetto_completo.php"
log_file="compilazione_log.txt"
index_file="indice.php"
hash_file="file_hashes.txt"
prev_hash_file="file_hashes_prev.txt"
shared_dir="/var/www/shared"
modified_files_file="$shared_dir/modified_files.txt"
code_metrics_file="$shared_dir/code_metrics.md"

# Directory target (usa la directory corrente come base)
base_dir="home/fabio/ai-tracker"
app_dir="$base_dir/app"

# Ripulisci i file di output
> "$output_file"
> "$log_file"
> "$code_metrics_file"

# Assicurati che la directory condivisa esista
mkdir -p "$shared_dir"

# Backup degli hash precedenti
if [ -f "$hash_file" ]; then
    mv "$hash_file" "$prev_hash_file"
fi

# Funzione per generare l'indice
generate_index() {
    echo -e "<?php\n/*\n### Indice dei File ###\n" > "$index_file"
    grep "#### Inizio File:" "$output_file" | awk '{print NR, $4}' | while read -r line; do
        file_num=$(echo $line | awk '{print $1}')
        file_path=$(echo $line | awk '{$1=""; print $0}')
        echo "$file_num. $file_path" >> "$index_file"
    done
    echo -e "\n### Fine Indice ###\n*/\n?>" >> "$index_file"

    cat "$index_file" "$output_file" > temp_file && mv temp_file "$output_file"
    rm "$index_file"
}

# Funzione per generare hash dei file inclusi
generate_hashes() {
    echo "### Hash dei File Inclusi ###" > "$hash_file"
    find "$app_dir" "$resources_admin_dir" "$resources_livewire_dir" "$resources_notifications_dir" \
        -type f -name "*.php" -print | sort | while read -r file; do
            hash=$(sha256sum "$file" | awk '{print $1}')
            echo "$file: $hash" >> "$hash_file"
        done
}

# Funzione per confrontare gli hash
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

# Funzione per calcolare la complessità ciclomatica
calculate_complexity() {
    local file=$1
    local complexity=$(grep -cE '\bif\b|\belse\b|\bwhile\b|\bfor\b|\bcase\b|&&|\|\||\?:' "$file")
    echo "$complexity"
}

# Funzione per analizzare i principi SOLID
analyze_solid_principles() {
    local file=$1
    local srp_score=0
    local ocp_score=0
    local isp_score=0
    local dip_score=0

    srp_score=$(grep -c "class" "$file")
    srp_score=$((10 - srp_score < 1 ? 1 : 10 - srp_score))

    ocp_score=$(grep -c "extends\|implements" "$file")
    ocp_score=$((ocp_score > 0 ? 10 : 0))

    isp_score=$(grep -c "interface" "$file")
    isp_score=$((isp_score > 0 ? 10 : 0))

    dip_score=$(grep -cE "public function __construct\(.*(Interface|Contract)" "$file")
    dip_score=$((dip_score > 0 ? 10 : 3))

    echo "## SOLID Analysis for $(basename "$file")" >> "$code_metrics_file"
    echo "- Single Responsibility: $srp_score/10" >> "$code_metrics_file"
    echo "- Open/Closed: $ocp_score/10" >> "$code_metrics_file"
    echo "- Interface Segregation: $isp_score/10" >> "$code_metrics_file"
    echo "- Dependency Inversion: $dip_score/10" >> "$code_metrics_file"
}

# Funzione per generare il sommario
generate_summary() {
    echo -e "\n# 📊 SOMMARIO SETTIMANALE" >> "$code_metrics_file"
    echo "Data: $(date +"%Y-%m-%d")" >> "$code_metrics_file"

    # Calcola la settimana corrente
    current_week=$(date +"%V")
    current_year=$(date +"%Y")

    # Prepara il file JSON per le statistiche Hubbard
    hubbard_stats_file="/var/www/productivity-tracker/data/hubbard_stats.json"
    echo "[" > "$hubbard_stats_file"

    # Calcola medie e preparale per il formato Hubbard
    if [ -f "/tmp/srp_scores" ]; then
        local srp_avg=$(awk '{ sum += $1 } END { print sum/NR }' /tmp/srp_scores)
        local ocp_avg=$(awk '{ sum += $1 } END { print sum/NR }' /tmp/ocp_scores)
        local isp_avg=$(awk '{ sum += $1 } END { print sum/NR }' /tmp/isp_scores)
        local dip_avg=$(awk '{ sum += $1 } END { print sum/NR }' /tmp/dip_scores)

        # Scrivi le statistiche nel formato Hubbard
        cat << EOF >> "$hubbard_stats_file"
        {
            "week": "W${current_week}",
            "year": "${current_year}",
            "value": ${srp_avg},
            "category": "solid_principles"
        },
        {
            "week": "W${current_week}",
            "year": "${current_year}",
            "value": ${ocp_avg},
            "category": "code_quality"
        }
EOF
    fi

    # Statistiche generali
    local total_files=$(grep -c "SOLID Analysis for" "$code_metrics_file")
    local total_badges=$(grep -c "🏆\|📚\|⚡" "$code_metrics_file")

    # Aggiungi le statistiche generali al file Hubbard
    cat << EOF >> "$hubbard_stats_file"
    {
        "week": "W${current_week}",
        "year": "${current_year}",
        "value": ${total_files},
        "category": "files_analyzed"
    }
]
EOF

    # Aggiungi il sommario anche al file delle metriche
    echo -e "\n## 📈 Metriche Generali" >> "$code_metrics_file"
    echo "- Files Analizzati: $total_files" >> "$code_metrics_file"
    echo "- Badge Totali: $total_badges" >> "$code_metrics_file"
    echo "- Media SOLID: ${srp_avg}/10" >> "$code_metrics_file"

    # Pulisci i file temporanei
    rm -f /tmp/*_scores
}

# Funzione per assegnare badge
assign_badges() {
    local file=$1
    local badges=""
    if grep -q "\/\*\*" "$file"; then
        badges+="📚 Documentation Hero "
    fi
    if [ $(grep -c "interface\|extends\|implements" "$file") -gt 0 ]; then
        badges+="🏆 SOLID Master "
    fi
    echo "### Badges: $badges" >> "$code_metrics_file"
}

# Log iniziale
echo "Inizio compilazione: $(date)" >> "$log_file"

# Trova e processa i file
find "$app_dir" "$resources_admin_dir" "$resources_livewire_dir" "$resources_notifications_dir" \
    -type f -name "*.php" -print | sort | while read -r file; do
    echo "Elaborazione file: $file" >> "$log_file"
    complexity=$(calculate_complexity "$file")
    echo "Complessità: $complexity" >> "$log_file"

    if grep -q "class" "$file"; then
        analyze_solid_principles "$file"
        assign_badges "$file"
    fi

    echo -e "\n<?php /* #### Inizio File: $file #### */ ?>\n" >> "$output_file"
    cat "$file" >> "$output_file"
    echo "File aggiunto al progetto completo: $file" >> "$log_file"
done

generate_summary

generate_index
generate_hashes
compare_hashes

echo "Compilazione completata!"
echo "File generati:"
echo "- File unificato: $output_file"
echo "- Log: $log_file"
echo "- Metriche: $code_metrics_file"
