#!/bin/bash

# 📊 EGI Development Time Tracker
# Author: Fabio Cherici
# Purpose: Track actual coding time using VS Code activity and git commits

echo "⏱️  EGI Development Time Analysis"
echo "=================================="
echo ""

# Parametri
START_DATE="${1:-$(date +%Y-%m-%d)}"
END_DATE="${2:-$(date +%Y-%m-%d)}"

echo "📅 Periodo analizzato: $START_DATE"
if [[ "$END_DATE" != "$START_DATE" ]]; then
    echo "📅 Fino a: $END_DATE"
fi
echo ""

# Funzione per stimare tempo di coding dai commit
estimate_coding_time() {
    local date="$1"

    # Ottieni tutti i commit del giorno con timestamp
    local commits=$(git log --since="$date 00:00:00" --until="$date 23:59:59" --pretty=format:"%ai")

    if [ -z "$commits" ]; then
        echo "0"
        return
    fi

    # Converti in array
    local commit_times=()
    while IFS= read -r line; do
        if [ ! -z "$line" ]; then
            # Estrai solo ora:minuto
            local time_only=$(echo "$line" | cut -d' ' -f2 | cut -d':' -f1-2)
            local epoch_time=$(date -d "$date $time_only" +%s 2>/dev/null)
            if [ ! -z "$epoch_time" ]; then
                commit_times+=($epoch_time)
            fi
        fi
    done <<< "$commits"

    # Se meno di 2 commit, stima 30 minuti per commit
    if [ ${#commit_times[@]} -lt 2 ]; then
        echo $((${#commit_times[@]} * 30))
        return
    fi

    # Ordina i tempi
    IFS=$'\n' commit_times=($(sort -n <<<"${commit_times[*]}"))

    local total_time=0
    local sessions=0

    for ((i=1; i<${#commit_times[@]}; i++)); do
        local diff=$(( ${commit_times[i]} - ${commit_times[i-1]} ))
        local minutes=$((diff / 60))

        # Se gap < 2 ore, considera sessione continua
        if [ $minutes -lt 120 ]; then
            total_time=$((total_time + minutes))
        else
            # Gap troppo lungo, aggiungi tempo minimo per il commit
            total_time=$((total_time + 15))
        fi
        sessions=$((sessions + 1))
    done

    # Aggiungi tempo per il primo e ultimo commit (stima 30 min ciascuno)
    total_time=$((total_time + 60))

    echo "$total_time"
}

# Funzione per convertire minuti in formato leggibile
format_time() {
    local minutes=$1
    local hours=$((minutes / 60))
    local mins=$((minutes % 60))

    if [ $hours -gt 0 ]; then
        printf "%dh %02dm" $hours $mins
    else
        printf "%dm" $mins
    fi
}

# Analizza il periodo
current_date="$START_DATE"
total_coding_minutes=0
active_days=0

echo "⏱️  Stima tempo di sviluppo giornaliero:"
echo "---------------------------------------"

while [[ "$current_date" != $(date -d "$END_DATE + 1 day" +%Y-%m-%d) ]]; do
    # Conta commit del giorno
    commits_count=$(git log --oneline --since="$current_date 00:00:00" --until="$current_date 23:59:59" | wc -l)

    if [ $commits_count -gt 0 ]; then
        # Stima tempo di coding
        coding_minutes=$(estimate_coding_time "$current_date")
        total_coding_minutes=$((total_coding_minutes + coding_minutes))
        active_days=$((active_days + 1))

        # Formatta output
        formatted_time=$(format_time $coding_minutes)
        day_name=$(date -d "$current_date" +"%A")

        # Emoji basata sul tempo
        if [ $coding_minutes -lt 60 ]; then
            emoji="⚡"
        elif [ $coding_minutes -lt 180 ]; then
            emoji="🔥"
        elif [ $coding_minutes -lt 360 ]; then
            emoji="🚀"
        else
            emoji="⭐"
        fi

        printf "%s %s (%s): %s (%d commit)\n" "$emoji" "$current_date" "$day_name" "$formatted_time" "$commits_count"
    else
        day_name=$(date -d "$current_date" +"%A")
        printf "😴 %s (%s): 0m (riposo)\n" "$current_date" "$day_name"
    fi

    current_date=$(date -d "$current_date + 1 day" +%Y-%m-%d)
done

echo ""
echo "📊 Riassunto periodo:"
echo "--------------------"

total_formatted=$(format_time $total_coding_minutes)
echo "⏰ Tempo totale stimato: $total_formatted"

if [ $active_days -gt 0 ]; then
    avg_minutes=$((total_coding_minutes / active_days))
    avg_formatted=$(format_time $avg_minutes)
    echo "📈 Media giorni attivi: $avg_formatted"
fi

total_days=$(( ($(date -d "$END_DATE" +%s) - $(date -d "$START_DATE" +%s)) / 86400 + 1 ))
if [ $total_days -gt 0 ]; then
    overall_avg=$((total_coding_minutes / total_days))
    overall_formatted=$(format_time $overall_avg)
    echo "📊 Media giornaliera: $overall_formatted"
fi

echo "🎯 Giorni attivi: $active_days/$total_days"

# Calcola produttività
if [ $total_days -gt 0 ]; then
    productivity=$((active_days * 100 / total_days))
    echo "📈 Produttività: $productivity%"
fi

echo ""
echo "💡 Metodologia di stima:"
echo "-----------------------"
echo "• Analisi timestamp dei commit Git"
echo "• Sessioni continue: gap < 2 ore tra commit"
echo "• Tempo base: 30min per commit isolato"
echo "• Tempo aggiuntivo: 15min per commit in sessione"
echo "• Stima conservativa: no pause lunghe"

echo ""
echo "🔍 Suggerimenti per tracking più preciso:"
echo "-----------------------------------------"
echo "Installa una di queste estensioni VS Code:"
echo "• WakaTime (wakatime.vscode-wakatime)"
echo "• Code Time (softwaredotcom.swdc-vscode)"
echo "• Time (n3rds-inc.time) - locale, no cloud"

echo ""
echo "ℹ️  Per tracking automatico installa WakaTime o Code Time"
echo "   Questo script fornisce stime basate sui commit Git"

echo ""
echo "========================================="
echo "📊 Analisi completata - $(date +"%Y-%m-%d %H:%M:%S")"
