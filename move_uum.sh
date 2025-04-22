#!/bin/bash

# Script per spostare il contenuto di UUM dalla Sandbox alla libreria dedicata

# Imposta l'uscita immediata in caso di errore
set -e

# --- Variabili ---
SOURCE_DIR="/home/fabio/sandbox/UltraUploadSandbox/packages/ultra/uploadmanager"
DEST_DIR="/home/fabio/libraries/UltraUploadManager"
BACKUP_SUFFIX=".backup_$(date +%Y%m%d_%H%M%S)"

echo "---------------------------------------------"
echo " UltraUploadManager - Spostamento Pacchetto "
echo "---------------------------------------------"
echo "Source: $SOURCE_DIR"
echo "Destination: $DEST_DIR"
echo ""

# --- Controlli Preliminari ---

# 1. Verifica esistenza directory sorgente
if [ ! -d "$SOURCE_DIR" ]; then
  echo "❌ ERRORE: La directory sorgente non esiste: $SOURCE_DIR"
  exit 1
fi
echo "✔️ Sorgente trovata."

# 2. Verifica esistenza directory destinazione
if [ ! -d "$DEST_DIR" ]; then
  echo "❌ ERRORE: La directory di destinazione non esiste: $DEST_DIR"
  echo "ℹ️ Assicurati di averla creata (e che contenga almeno composer.json e src/)."
  exit 1
fi
echo "✔️ Destinazione trovata."

# 3. Verifica se la destinazione contiene già file oltre composer.json e src/ (avviso)
#    Questo è un controllo semplice, non infallibile.
if [ "$(ls -A "$DEST_DIR" | grep -v -e '^composer\.json$' -e '^src$' | wc -l)" -gt 0 ]; then
   echo "⚠️ ATTENZIONE: La directory di destinazione ($DEST_DIR) contiene già file/cartelle oltre a 'composer.json' e 'src/'."
   echo "   Lo script tenterà di sovrascrivere i file esistenti con lo stesso nome."
   read -p "   Sei sicuro di voler continuare? (s/N): " confirm_overwrite
   if [[ ! "$confirm_overwrite" =~ ^[Ss]$ ]]; then
        echo "❌ Operazione annullata dall'utente."
        exit 0
   fi
fi

# --- Azione ---

echo ""
echo "🚚 Inizio copia dei contenuti da Sorgente a Destinazione (inclusi file nascosti)..."

# Copia TUTTO il contenuto (inclusi file nascosti come .gitignore) DALLA sorgente ALLA destinazione
# L'opzione -a (archivio) è ricorsiva, preserva permessi, timestamp, link simbolici, etc.
# Il "." alla fine della sorgente assicura che vengano copiati anche i file nascosti.
if cp -a "$SOURCE_DIR/." "$DEST_DIR/"; then
  echo "✅ Copia completata con successo."
else
  echo "❌ ERRORE: La copia dei file è fallita. Controlla i permessi o eventuali errori sopra."
  exit 1
fi

# --- Rimozione Sorgente (con conferma!) ---

echo ""
read -p "❓ Vuoi RIMUOVERE la directory sorgente originale ($SOURCE_DIR)? Questa azione è IRREVERSIBILE. (s/N): " confirm_delete

if [[ "$confirm_delete" =~ ^[Ss]$ ]]; then
    echo "🗑️ Rimozione della directory sorgente originale..."
    if rm -rf "$SOURCE_DIR"; then
        echo "✅ Directory sorgente rimossa con successo."
    else
        echo "❌ ERRORE: Impossibile rimuovere la directory sorgente $SOURCE_DIR."
        echo "ℹ️ Potresti doverla rimuovere manualmente."
        # Non usciamo con errore qui, la copia è andata a buon fine.
    fi
else
    echo "ℹ️ Directory sorgente NON rimossa."
fi

echo ""
echo "🎉 Operazione completata!"
echo "ℹ️ Ricorda di aggiornare il composer.json della tua Sandbox per usare il repository 'path'."
echo "---------------------------------------------"

exit 0
