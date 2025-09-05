#!/bin/bash
# 🕒 EGI Platform Testing Time Tracker
# ===================================
# Traccia automaticamente il tempo speso sulla piattaforma locale

LOG_FILE="$HOME/EGI/testing_time.log"
PLATFORM_URL="localhost:8000"  # Modifica con il tuo URL

# Funzione per loggare attività
log_activity() {
    echo "$(date '+%Y-%m-%d %H:%M:%S') - $1" >> "$LOG_FILE"
}

# Monitora processo browser per URL piattaforma
monitor_browser() {
    while true; do
        # Controlla se browser è aperto sulla piattaforma
        if pgrep -f "$PLATFORM_URL" > /dev/null; then
            if [ ! -f "/tmp/egi_testing_active" ]; then
                touch "/tmp/egi_testing_active"
                log_activity "TESTING_START - Platform testing session started"
            fi
        else
            if [ -f "/tmp/egi_testing_active" ]; then
                rm "/tmp/egi_testing_active"
                log_activity "TESTING_END - Platform testing session ended"
            fi
        fi
        sleep 30  # Check ogni 30 secondi
    done
}

case "$1" in
    start)
        echo "🚀 Starting EGI testing tracker..."
        monitor_browser &
        echo $! > /tmp/egi_tracker_pid
        ;;
    stop)
        if [ -f /tmp/egi_tracker_pid ]; then
            kill $(cat /tmp/egi_tracker_pid)
            rm /tmp/egi_tracker_pid
            echo "⏹️  EGI testing tracker stopped"
        fi
        ;;
    report)
        echo "📊 EGI Testing Time Report:"
        echo "=========================="
        grep "TESTING_" "$LOG_FILE" | tail -20
        ;;
    *)
        echo "Usage: $0 {start|stop|report}"
        ;;
esac
