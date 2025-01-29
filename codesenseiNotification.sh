#!/bin/bash

# ===========================================
# Code Analyzer (PHP + JS)
# ===========================================

# Directory e file di configurazione
base_dir="/home/fabio/EGI"
shared_dir="/var/www/shared/Egi"
log_file="$shared_dir/analyzer.log"

# Funzione per il logging
log_message() {
    local timestamp
    timestamp=$(date '+%Y-%m-%d %H:%M:%S')
    echo "[$timestamp] $1" >> "$log_file"
}

log_message "Avvio analisi codice"

# Directories da analizzare
app_dirs=(
    "Models"
    "Services"
    "Livewire"
    "Enums"
    "Notifications"
    "Services"
    "Models"
)

resources_dirs=(
    "livewire/notifications"
    "livewire/partials"
    "livewire/proposal"
    "notifications"
    "partials"
)

other_dirs=(
    "routes"
)

# Directory per i file JavaScript
js_dirs=(
    "public/js"
)

# Costruisce la lista di directory
directories=()

# Aggiunge le sottocartelle di `app`
for dir in "${app_dirs[@]}"; do
    directories+=("$base_dir/app/$dir")
    log_message "Aggiunta directory: app/$dir"
done

# Aggiunge le sottocartelle di `resources/views`
for dir in "${resources_dirs[@]}"; do
    directories+=("$base_dir/resources/views/$dir")
    log_message "Aggiunta directory: resources/views/$dir"
done

# Aggiunge altre directory
for dir in "${other_dirs[@]}"; do
    directories+=("$base_dir/$dir")
    log_message "Aggiunta directory: $dir"
done

# Aggiunge migrations
directories+=("$base_dir/database/migrations")
log_message "Aggiunta directory: database/migrations"

# Debug delle directory
log_message "Directory da analizzare:"
for dir in "${directories[@]}"; do
    log_message "  - $dir"
done

# File di stato e output
hash_file="$shared_dir/file_hashes.txt"
all_classes_json="$shared_dir/all_classes.json"
modified_classes_json="$shared_dir/modified_classes.json"
file_index="$shared_dir/file_index.txt"
merged_output="$shared_dir/merged_code.txt"

# Funzione per aggiungere separatori nel file di merge
add_separator() {
    local type="$1"
    echo -e "\n\n/* ============================================= */"
    echo "/* INIZIO SEZIONE $type"
    echo "/* ============================================= */\n\n"
}

# Funzioni di analisi (uguali alla versione originale, ma gestiscono anche i file .js)
calculate_complexity() {
    local file="$1"
    # Conta strutture di controllo
    local control_structures
    control_structures=$(grep -oE '\b(if|else|elseif|while|do|for|foreach|switch|case|catch)\b' "$file" 2>/dev/null | wc -l)
    # Conta operatori logici
    local logical_operators
    logical_operators=$(grep -oE '(\&\&|\|\||[^-]>|[^-]<|===|!==|\band\b|\bor\b|\bxor\b)' "$file" 2>/dev/null | wc -l)
    # Somma totale
    echo $((control_structures + logical_operators))
}

count_methods() {
    local file="$1"
    # Conta tutti i metodi pubblici, protetti e privati
    grep -oE '(public|protected|private)\s+function\s+\w+\s*\(' "$file" 2>/dev/null | wc -l
}

calculate_nesting() {
    local file="$1"
    awk '
    BEGIN {
        max_depth = 0
        current_depth = 0
    }
    /\{/ {
        current_depth++
        if (current_depth > max_depth) max_depth = current_depth
    }
    /\}/ {
        current_depth--
    }
    END {
        print max_depth
    }' "$file" 2>/dev/null
}

analyze_dependencies() {
    local file="$1"
    # Conta use statements per PHP
    if [[ "$file" =~ \.php$ ]]; then
        local uses
        uses=$(grep -c '^use ' "$file" 2>/dev/null)
        local inheritance
        inheritance=$(grep -cE '\b(extends|implements)\b' "$file" 2>/dev/null)
        echo $((uses + inheritance))
    # Conta import/require per JavaScript
    elif [[ "$file" =~ \.js$ ]]; then
        local imports
        imports=$(grep -cE '^(import|require)' "$file" 2>/dev/null)
        echo "$imports"
    fi
}

analyze_code() {
    local file="$1"
    local filename
    filename=$(basename "$file")
    local is_js=false

    # Riconosce se è un file JavaScript
    if [[ "$file" =~ \.js$ ]]; then
        is_js=true
    fi

    # Analisi comune per PHP e JS: (if, else, case, for, foreach, while, do, switch, try, catch, &&, ||)
    local control_flow
    control_flow=$(grep -oE '\b(if|else|elseif|case|for|foreach|while|do|switch|try|catch)\b|\&\&|\|\|' "$file" | wc -l)

    if [ "$is_js" = true ]; then
        # Analisi JavaScript
        local functions
        functions=$(grep -c "function" "$file")
        local dependencies
        dependencies=$(grep -cE '^(import|require)' "$file")
    else
        # Analisi PHP
        local public_methods
        public_methods=$(grep -c "public function" "$file")
        local protected_methods
        protected_methods=$(grep -c "protected function" "$file")
        local private_methods
        private_methods=$(grep -c "private function" "$file")
        local total_methods=$((public_methods + protected_methods + private_methods))

        local use_statements
        use_statements=$(grep -c "^use " "$file")
        local extends
        extends=$(grep -c "extends " "$file")
        local implements
        implements=$(grep -c "implements " "$file")
        local total_dependencies=$((use_statements + extends + implements))
    fi

    # Conteggio delle linee
    local total_lines
    total_lines=$(wc -l < "$file")
    local empty_lines
    empty_lines=$(grep -c "^[[:space:]]*$" "$file")
    local comment_lines
    comment_lines=$(grep -c "^[[:space:]]*//" "$file")
    local effective_lines=$((total_lines - empty_lines - comment_lines))

    # Profondità di annidamento
    local nesting
    nesting=$(calculate_nesting "$file")

    # Log dell'analisi
    log_message "=== Analisi dettagliata per $filename ==="
    log_message "Tipo file: $([ "$is_js" = true ] && echo "JavaScript" || echo "PHP")"
    log_message "Complessità ciclomatica: $control_flow"

    if [ "$is_js" = true ]; then
        log_message "Funzioni totali: $functions"
        log_message "Dipendenze (import/require): $dependencies"
    else
        log_message "Metodi totali: $total_methods"
        log_message "Dipendenze totali: $total_dependencies"
    fi

    log_message "Analisi del codice:"
    log_message "  - Linee totali: $total_lines"
    log_message "  - Linee effettive: $effective_lines"
    log_message "  - Max profondità annidamento: $nesting"

    # Creazione JSON d'uscita per ogni file
    if [ "$is_js" = true ]; then
        echo "{
            \"type\": \"javascript\",
            \"complexity\": {
                \"score\": $((control_flow * 5)),
                \"control_structures\": $control_flow
            },
            \"functions\": {
                \"total\": $functions
            },
            \"dependencies\": {
                \"total\": $dependencies
            },
            \"code_lines\": {
                \"total\": $total_lines,
                \"effective\": $effective_lines,
                \"empty\": $empty_lines,
                \"comments\": $comment_lines
            },
            \"nesting_depth\": $nesting
        }"
    else
        echo "{
            \"type\": \"php\",
            \"complexity\": {
                \"score\": $((control_flow * 5)),
                \"control_structures\": $control_flow
            },
            \"methods\": {
                \"total\": $total_methods,
                \"public\": $public_methods,
                \"protected\": $protected_methods,
                \"private\": $private_methods
            },
            \"dependencies\": {
                \"total\": $total_dependencies,
                \"use_statements\": $use_statements,
                \"extends\": $extends,
                \"implements\": $implements
            },
            \"code_lines\": {
                \"total\": $total_lines,
                \"effective\": $effective_lines,
                \"empty\": $empty_lines,
                \"comments\": $comment_lines
            },
            \"nesting_depth\": $nesting
        }"
    fi
}

get_category() {
    local file="$1"
    if [[ "$file" =~ \.js$ ]]; then
        echo "javascript"
    elif [[ "$file" =~ /Controllers/ ]]; then
        echo "controller"
    elif [[ "$file" =~ /Services/ ]]; then
        echo "service"
    elif [[ "$file" =~ /Models/ ]]; then
        echo "model"
    elif [[ "$file" =~ /Helpers/ ]]; then
        echo "helper"
    elif [[ "$file" =~ /Traits/ ]]; then
        echo "trait"
    elif [[ "$file" =~ /Providers/ ]]; then
        echo "provider"
    elif [[ "$file" =~ /routes/ ]]; then
        echo "route"
    elif [[ "$file" =~ \.blade\.php$ ]]; then
        echo "blade"
    else
        echo "unknown"
    fi
}

# Inizializza file di stato se non esiste
if [ ! -f "$hash_file" ]; then
    touch "$hash_file"
    log_message "Creato nuovo file hash"
fi

# Inizializza arrays
declare -a all_classes
declare -a modified_classes

# Inizializza l'indice dei file
> "$file_index"
log_message "Inizializzato file index"

# Raccogli i file PHP e Blade
php_files=""
blade_files=""
for dir in "${directories[@]}"; do
    if [ -d "$dir" ]; then
        # Trova file PHP (escludendo .blade.php)
        if [ -z "$php_files" ]; then
            php_files=$(find "$dir" -name "*.php" ! -name "*.blade.php" 2>/dev/null)
        else
            php_files="$php_files"$'\n'$(find "$dir" -name "*.php" ! -name "*.blade.php" 2>/dev/null)
        fi

        # Trova file Blade
        if [ -z "$blade_files" ]; then
            blade_files=$(find "$dir" -name "*.blade.php" 2>/dev/null)
        else
            blade_files="$blade_files"$'\n'$(find "$dir" -name "*.blade.php" 2>/dev/null)
        fi
    else
        log_message "AVVISO: Directory non trovata: $dir"
    fi
done

# Raccogli i file JavaScript
js_files=""
for dir in "${js_dirs[@]}"; do
    if [ -d "$base_dir/$dir" ]; then
        if [ -z "$js_files" ]; then
            js_files=$(find "$base_dir/$dir" -name "*.js" 2>/dev/null)
        else
            js_files="$js_files"$'\n'$(find "$base_dir/$dir" -name "*.js" 2>/dev/null)
        fi
        log_message "Aggiunta directory JS: $base_dir/$dir"
    else
        log_message "AVVISO: Directory JS non trovata: $base_dir/$dir"
    fi
done

# Log dei file trovati
log_message "File PHP trovati: $(echo "$php_files" | wc -l)"
log_message "File Blade trovati: $(echo "$blade_files" | wc -l)"
log_message "File JavaScript trovati: $(echo "$js_files" | wc -l)"

# Funzione per processare un file (PHP, Blade o JS)
process_file() {
    local file="$1"
    if [ -n "$file" ] && [ -f "$file" ]; then
        log_message "Analisi del file: $file"

        # Analizza il file
        local metrics
        metrics=$(analyze_code "$file")
        local category
        category=$(get_category "$file")

        # Crea JSON temporaneo
        local temp_json
        temp_json=$(mktemp)
        jq -n \
            --arg name "$(basename "$file")" \
            --arg code "$(cat "$file" | sed 's/\\/\\\\/g' | sed 's/"/\\"/g')" \
            --arg category "$category" \
            --argjson metrics "$metrics" \
            '{name: $name, code: $code, category: $category, metrics: $metrics}' > "$temp_json"

        # Aggiungi al JSON totale
        all_classes+=("$(cat "$temp_json")")

        # Verifica modifiche (hash)
        local hash
        hash=$(sha256sum "$file" | awk '{print $1}')
        local previous_hash
        previous_hash=$(grep "$file" "$hash_file" | awk '{print $1}')

        if [ "$hash" != "$previous_hash" ]; then
            modified_classes+=("$(cat "$temp_json")")
            sed -i "\|$file|d" "$hash_file"
            echo "$hash $file" >> "$hash_file"
            log_message "File modificato: $file"
        fi

        rm -f "$temp_json"
        echo "$file" >> "$file_index"
    elif [ -n "$file" ]; then
        log_message "ERRORE: File non trovato: $file"
    fi
}

# Processa tutti i file raccolti
while IFS= read -r file; do
    process_file "$file"
done <<< "$php_files"

while IFS= read -r file; do
    process_file "$file"
done <<< "$blade_files"

while IFS= read -r file; do
    process_file "$file"
done <<< "$js_files"

# Funzione per salvare JSON
save_json() {
    local content="$1"
    local file="$2"
    local temp_file
    temp_file=$(mktemp)

    echo "$content" > "$temp_file"

    if jq empty "$temp_file" 2>/dev/null; then
        mv "$temp_file" "$file"
        log_message "JSON salvato correttamente in: $file"
        return 0
    else
        log_message "ERRORE: JSON non valido per: $file"
        rm -f "$temp_file"
        return 1
    fi
}

# Creazione JSON finali
all_json="[]"
modified_json="[]"

if [ ${#all_classes[@]} -gt 0 ]; then
    all_json="[$(IFS=,; echo "${all_classes[*]}")]"
fi

if [ ${#modified_classes[@]} -gt 0 ]; then
    modified_json="[$(IFS=,; echo "${modified_classes[*]}")]"
fi

# Salva i JSON
save_json "$all_json" "$all_classes_json"
save_json "$modified_json" "$modified_classes_json"

# Crea il file merged con separatori distinti
{
    # Sezione PHP
    add_separator "PHP FILES"
    while IFS= read -r file; do
        if [ -n "$file" ] && [ -f "$file" ]; then
            echo -e "\n/* File: $file */\n"
            cat "$file"
            echo -e "\n/* Fine file: $file */\n"
        fi
    done <<< "$php_files"

    # Sezione BLADE
    add_separator "BLADE TEMPLATES"
    while IFS= read -r file; do
        if [ -n "$file" ] && [ -f "$file" ]; then
            echo -e "\n/* File: $file */\n"
            cat "$file"
            echo -e "\n/* Fine file: $file */\n"
        fi
    done <<< "$blade_files"

    # Sezione JS
    add_separator "JS FILES"
    while IFS= read -r file; do
        if [ -n "$file" ] && [ -f "$file" ]; then
            echo -e "\n/* File: $file */\n"
            cat "$file"
            echo -e "\n/* Fine file: $file */\n"
        fi
    done <<< "$js_files"

} > "$merged_output"

log_message "File di merge generato: $merged_output"

# Output finale
echo "File generati in $shared_dir:"
echo "- Tutte le classi: $all_classes_json"
echo "- Classi modificate: $modified_classes_json"
echo "- Indice dei file: $file_index"
echo "- File unificato con separatori: $merged_output"
echo "- Log: $log_file"
