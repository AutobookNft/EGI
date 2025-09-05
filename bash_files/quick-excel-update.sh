#!/bin/bash
# 🔄 Quick Excel Update - Aggiornamento rapido statistiche Excel
# ===============================================================
# Comando rapido per aggiornare il file Excel senza output verboso
#
# Uso: ./bash_files/quick-excel-update.sh

cd /home/fabio/EGI
python3 bash_files/commit-stats-to-excel.py > /dev/null 2>&1

if [ $? -eq 0 ]; then
    echo "✅ Excel aggiornato: commit_statistics.xlsx ($(date '+%H:%M:%S'))"
else
    echo "❌ Errore aggiornamento Excel"
    exit 1
fi
