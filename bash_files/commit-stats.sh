#!/bin/bash

# 📊 EGI Commit Statistics Tracker
# Author: Fabio Cherici
# Purpose: Daily commit tracking and productivity monitoring

echo "📊 EGI Project - Commit Statistics"
echo "=================================="
echo ""

# Current date
TODAY=$(date +"%Y-%m-%d")
echo "📅 Report generato il: $TODAY"
echo ""

# Today's commits
TODAY_COMMITS=$(git log --oneline --since="today" | wc -l)
echo "🎯 Commit di oggi: $TODAY_COMMITS"

# Yesterday's commits
YESTERDAY_COMMITS=$(git log --oneline --since="yesterday" --until="today" | wc -l)
echo "📈 Commit di ieri: $YESTERDAY_COMMITS"

# This week's commits
WEEK_COMMITS=$(git log --oneline --since="1 week ago" | wc -l)
echo "📅 Commit questa settimana: $WEEK_COMMITS"

# This month's commits
MONTH_COMMITS=$(git log --oneline --since="1 month ago" | wc -l)
echo "📊 Commit questo mese: $MONTH_COMMITS"

echo ""
echo "📈 Ultimi 7 giorni (dettaglio):"
echo "--------------------------------"

# Last 7 days breakdown
for i in {0..6}; do
    DATE=$(date -d "-$i days" +"%Y-%m-%d")
    COMMITS=$(git log --oneline --since="$DATE 00:00:00" --until="$DATE 23:59:59" | wc -l)
    DAY_NAME=$(date -d "-$i days" +"%A")

    if [ $i -eq 0 ]; then
        echo "🔥 $DATE ($DAY_NAME): $COMMITS commit (OGGI)"
    elif [ $i -eq 1 ]; then
        echo "📊 $DATE ($DAY_NAME): $COMMITS commit (ieri)"
    else
        echo "📊 $DATE ($DAY_NAME): $COMMITS commit"
    fi
done

echo ""
echo "🏆 Top 10 giorni più produttivi (ultimi 30 giorni):"
echo "---------------------------------------------------"
git log --oneline --since="30 days ago" --pretty=format:"%ad" --date=short | sort | uniq -c | sort -nr | head -10 | awk '{printf "🎯 %s: %d commit\n", $2, $1}'

echo ""
echo "💻 Statistiche autore:"
echo "---------------------"
TOTAL_COMMITS=$(git rev-list --count HEAD)
echo "📊 Commit totali nel progetto: $TOTAL_COMMITS"

MY_COMMITS=$(git log --author="$(git config user.name)" --oneline | wc -l)
echo "👨‍💻 I tuoi commit totali: $MY_COMMITS"

PERCENTAGE=$(echo "scale=1; $MY_COMMITS * 100 / $TOTAL_COMMITS" | bc -l 2>/dev/null || echo "N/A")
echo "📈 La tua percentuale: $PERCENTAGE%"

echo ""
echo "🎲 Commit casuali degli ultimi giorni:"
echo "--------------------------------------"
git log --oneline --since="7 days ago" | head -5 | awk '{printf "✨ %s\n", $0}'

echo ""
echo "⚡ Trend produttività:"
echo "---------------------"
if [ $TODAY_COMMITS -gt $YESTERDAY_COMMITS ]; then
    echo "🚀 In crescita! (+$(($TODAY_COMMITS - $YESTERDAY_COMMITS)) vs ieri)"
elif [ $TODAY_COMMITS -eq $YESTERDAY_COMMITS ]; then
    echo "📊 Stabile (uguale a ieri)"
else
    echo "📉 In calo (-$(($YESTERDAY_COMMITS - $TODAY_COMMITS)) vs ieri)"
fi

echo ""
echo "💡 Suggerimento del giorno:"
if [ $TODAY_COMMITS -eq 0 ]; then
    echo "🎯 Inizia la giornata con un piccolo commit!"
elif [ $TODAY_COMMITS -lt 5 ]; then
    echo "⚡ Buon ritmo! Cerca di fare qualche commit in più."
elif [ $TODAY_COMMITS -lt 10 ]; then
    echo "🔥 Ottima produttività! Continua così!"
else
    echo "🏆 Giornata incredibile! Sei in zona di flow!"
fi

echo ""
echo "========================================="
echo "📊 Report completato - $(date +"%H:%M:%S")"
