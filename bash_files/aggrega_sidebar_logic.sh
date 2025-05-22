#!/bin/bash

# --- INIZIO SCRIPT aggrega_sidebar_logic.sh ---

# Definisci la directory base del progetto Laravel (aggiustala se necessario)
# Assumiamo che lo script venga eseguito dalla root del progetto Laravel.
# Se lo esegui da un'altra posizione, imposta PROJECT_DIR al percorso assoluto della root del progetto.
PROJECT_DIR="."
# Esempio se lo script è altrove: PROJECT_DIR="/percorso/completo/del/tuo/progetto/florenceegi"

# Definisci il file di output
OUTPUT_DIR="${PROJECT_DIR}/documentation_ai_context" # Cartella per i file generati per l'AI
OUTPUT_FILE="${OUTPUT_DIR}/sidebar_system_complete_logic.txt"

# File da includere (percorsi relativi a PROJECT_DIR)
# Mantieni questa lista aggiornata se aggiungi/rimuovi file cruciali per la sidebar
FILES_TO_AGGREGATE=(
    "app/Services/Menu/MenuItem.php"
    "app/Services/Menu/MenuGroup.php"
    "app/Services/Menu/ContextMenus.php"
    "app/Services/Menu/MenuConditionEvaluator.php" # Se ancora rilevante o per riferimento
    # Aggiungi qui tutte le classi specifiche dei MenuItem se vuoi includerle individualmente,
    # oppure potremmo fare un find per app/Services/Menu/Items/*.php
    # Per ora, le classi principali dovrebbero dare l'idea della struttura.
    # Se le classi Item sono molte, potrebbe appesantire. ContextMenus.php mostra come sono usate.
    "app/Services/Menu/Items/NewCollectionMenu.php" # Esempio di una classe Item
    "app/Services/Menu/Items/StatisticsMenu.php"    # Altro esempio

    "app/Repositories/IconRepository.php"
    "app/Models/Icon.php" # Il modello Eloquent per le icone
    "config/icons.php"
    "database/seeders/IconSeeder.php"

    "resources/views/components/sidebar.blade.php" # Assicurati che questo sia il percorso corretto
    # Potrebbe essere resources/views/layouts/partials/sidebar.blade.php o simile
    # Se hai un componente Blade per la sidebar, includi la classe del componente:
    # "app/View/Components/Sidebar.php" # Esempio se Sidebar è un componente di classe
)

# Crea la directory di output se non esiste
mkdir -p "$OUTPUT_DIR"

# --- INTESTAZIONE E INDICE ---
{
    echo "############################################################################"
    echo "#             FLORENCEEGI - SISTEMA SIDEBAR - LOGICA COMPLETA              #"
    echo "#                 Aggregato per Consultazione AI Facilitata                #"
    echo "#                                                                          #"
    echo "# Data Generazione: $(date)"
    echo "############################################################################"
    echo ""
    echo ""
    echo "--- INDICE DEI CONTENUTI (Percorsi relativi alla root del progetto) ---"
    echo ""
} > "$OUTPUT_FILE"

# Genera l'indice
counter=1
for file_path in "${FILES_TO_AGGREGATE[@]}"; do
    full_file_path="${PROJECT_DIR}/${file_path}"
    if [ -f "$full_file_path" ]; then
        # Usa basename per ottenere solo il nome del file per l'indice, più leggibile
        file_name=$(basename "$file_path")
        echo "${counter}. ${file_name}  (Path: ${file_path})" >> "$OUTPUT_FILE"
        counter=$((counter + 1))
    else
        echo "   [ATTENZIONE] File non trovato (verrà saltato): ${full_file_path}" >> "$OUTPUT_FILE"
    fi
done

{
    echo ""
    echo "--- FINE INDICE ---"
    echo ""
    echo "============================================================================"
    echo "NOTA PER L'IA:"
    echo "Ogni file sorgente è delimitato da marcatori '--- START OF FILE: [path] ---' e '--- END OF FILE: [path] ---'."
    echo "L'indice sopra può essere usato per navigare rapidamente alle sezioni rilevanti."
    echo "Questo documento aggrega i file PHP e Blade che costituiscono la logica principale"
    echo "e la visualizzazione della sidebar dinamica e contestuale di FlorenceEGI."
    echo "Analizza le interazioni tra questi file per comprendere il flusso completo."
    echo "============================================================================"
    echo ""
    echo ""
} >> "$OUTPUT_FILE"


# --- AGGREGAZIONE DEI FILE ---
echo "Avvio aggregazione file per il sistema sidebar..."

processed_files_count=0
for file_path in "${FILES_TO_AGGREGATE[@]}"; do
    full_file_path="${PROJECT_DIR}/${file_path}"

    if [ -f "$full_file_path" ] && [ -r "$full_file_path" ]; then
        echo "Processando: $file_path"

        # Aggiungi un commento di contesto per l'IA
        context_comment=""
        case "$file_path" in
            *"MenuItem.php") context_comment="# CONTESTO AI: Classe base per una singola voce di menu." ;;
            *"MenuGroup.php") context_comment="# CONTESTO AI: Classe per raggruppare più MenuItem." ;;
            *"ContextMenus.php") context_comment="# CONTESTO AI: Factory principale che definisce quali menu appaiono in base al contesto applicativo." ;;
            *"MenuConditionEvaluator.php") context_comment="# CONTESTO AI: Logica per determinare la visibilità di un item basata sui permessi (potrebbe essere integrata o un helper)." ;;
            *"Items/"*) context_comment="# CONTESTO AI: Implementazione specifica di una voce di menu." ;;
            *"IconRepository.php") context_comment="# CONTESTO AI: Gestione centralizzata per recupero, processamento e caching delle icone SVG." ;;
            *"Models/Icon.php") context_comment="# CONTESTO AI: Modello Eloquent per la tabella 'icons' del database." ;;
            *"config/icons.php") context_comment="# CONTESTO AI: File di configurazione centrale con le definizioni grezze degli SVG (con colori intrinseci)." ;;
            *"IconSeeder.php") context_comment="# CONTESTO AI: Seeder per popolare la tabella 'icons' dal file di configurazione." ;;
            *"sidebar.blade.php") context_comment="# CONTESTO AI: Vista Blade responsabile del rendering HTML della sidebar." ;;
            *"Components/Sidebar.php") context_comment="# CONTESTO AI: Classe del Componente Blade per la Sidebar, se esistente (contiene logica per preparare i dati per la vista del componente)." ;;
        esac

        {
            echo ""
            echo "----------------------------------------------------------------------------"
            echo "--- START OF FILE: ${file_path} ---"
            echo "----------------------------------------------------------------------------"
            if [ -n "$context_comment" ]; then
                echo "$context_comment"
                echo ""
            fi
        } >> "$OUTPUT_FILE"

        cat "$full_file_path" >> "$OUTPUT_FILE"

        {
            echo ""
            echo "--- END OF FILE: ${file_path} ---"
            echo "----------------------------------------------------------------------------"
            echo ""
            echo ""
        } >> "$OUTPUT_FILE"
        processed_files_count=$((processed_files_count + 1))
    else
        echo "ATTENZIONE: File non trovato o non leggibile, saltato: $full_file_path"
    fi
done

# --- AGGIUNGI DESCRIZIONE GENERALE DEL SISTEMA (dal documento MD precedente) ---
# Questo può essere utile per dare un overview iniziale all'IA
{
    echo ""
    echo "############################################################################"
    echo "#             DESCRIZIONE GENERALE DEL SISTEMA SIDEBAR                     #"
    echo "############################################################################"
    echo ""
    echo "Il sistema di sidebar di FlorenceEGI è progettato per fornire una navigazione utente dinamica,"
    echo "contestuale e basata sui permessi. Utilizza una combinazione di classi PHP per la definizione"
    echo "della struttura del menu, un repository per la gestione centralizzata delle icone (con supporto"
    echo "per SVG colorati e caching), e una vista Blade per il rendering finale. La sidebar si adatta"
    echo "al contesto dell'applicazione (es. \"dashboard\", \"collections\") mostrando solo i gruppi di menu"
    echo "e gli item pertinenti, e rispettando i permessi dell'utente autenticato per la visualizzazione"
    echo "di ciascun elemento. Le icone sono definite centralmente e possono essere personalizzate con"
    echo "colori intrinseci."
    echo ""
    echo "FLUSSO PRINCIPALE:"
    echo "1. Determinazione Contesto Applicativo."
    echo "2. Recupero Struttura Menu da 'ContextMenus.php' (oggetti MenuGroup e MenuItem con chiavi icona)."
    echo "3. Arricchimento Icone: Le chiavi icona vengono convertite in HTML SVG completo (con colori)"
    echo "   utilizzando 'IconRepository.php' (che legge da DB, popolato da 'config/icons.php' via 'IconSeeder.php')."
    echo "4. Passaggio Dati alla Vista 'sidebar.blade.php'."
    echo "5. Rendering: La vista itera, controlla i permessi (Gate::allows()), e stampa menu e icone SVG."
    echo ""
    echo "############################################################################"
    echo ""
} >> "$OUTPUT_FILE"


echo ""
echo "----------------------------------------------------------------------------"
if [ "$processed_files_count" -eq 0 ]; then
  echo "Nessun file è stato processato. Controlla i percorsi in FILES_TO_AGGREGATE e PROJECT_DIR."
else
  echo "Aggregazione completata. ${processed_files_count} file sono stati uniti in:"
  echo "$OUTPUT_FILE"
fi
echo "Il file contiene un indice all'inizio e commenti di contesto per facilitare la navigazione dell'IA."
echo "----------------------------------------------------------------------------"

# --- FINE SCRIPT aggrega_sidebar_logic.sh ---
