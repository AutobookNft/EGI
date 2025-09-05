#!/bin/bash
# 📊 EGI Commit Statistics Excel Export Script
# =============================================
# Genera un file Excel con tutte le statistiche dei commit
# organizzate per settimana dal 19 agosto 2025.
#
# @author: AI Partner OS2.0-Compliant for Fabio Cherici
# @version: 1.0.0 (FlorenceEGI MVP)

# Colori per output
RED='\033[0;31m'
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Banner
echo -e "${BLUE}📊 EGI Commit Statistics Excel Exporter${NC}"
echo -e "${BLUE}=========================================${NC}"

# Verifica che siamo nella directory giusta
if [ ! -f "artisan" ]; then
    echo -e "${RED}❌ Errore: Eseguire dalla directory root del progetto EGI${NC}"
    exit 1
fi

# Verifica dipendenze Python
echo -e "${YELLOW}🔍 Verifica dipendenze...${NC}"
if ! python3 -c "import pandas, openpyxl" 2>/dev/null; then
    echo -e "${YELLOW}📦 Installazione dipendenze Python...${NC}"
    pip install pandas openpyxl
    if [ $? -ne 0 ]; then
        echo -e "${RED}❌ Errore nell'installazione delle dipendenze${NC}"
        exit 1
    fi
fi

# Esegui lo script Python
echo -e "${YELLOW}🚀 Generazione file Excel...${NC}"
python3 bash_files/commit-stats-to-excel.py

# Verifica successo
if [ $? -eq 0 ]; then
    echo ""
    echo -e "${GREEN}✅ Export completato con successo!${NC}"
    
    # Mostra informazioni file
    if [ -f "commit_statistics.xlsx" ]; then
        file_size=$(stat -c%s "commit_statistics.xlsx")
        echo -e "${GREEN}📁 File: commit_statistics.xlsx${NC}"
        echo -e "${GREEN}📊 Dimensione: ${file_size} bytes${NC}"
        echo -e "${GREEN}📅 Generato: $(date)${NC}"
        
        # Suggerimenti
        echo ""
        echo -e "${BLUE}💡 Suggerimenti:${NC}"
        echo -e "${BLUE}   • Aprire con Excel, LibreOffice Calc o Google Sheets${NC}"
        echo -e "${BLUE}   • Il file contiene 3 fogli: Riepilogo, Settimanali, Giornalieri${NC}"
        echo -e "${BLUE}   • Dati aggiornati automaticamente dal repository Git${NC}"
        
        # Opzione per aprire automaticamente (se disponibile)
        if command -v xdg-open >/dev/null 2>&1; then
            echo ""
            read -p "🚀 Aprire il file automaticamente? (y/n): " -n 1 -r
            echo ""
            if [[ $REPLY =~ ^[Yy]$ ]]; then
                xdg-open commit_statistics.xlsx
            fi
        fi
    fi
else
    echo -e "${RED}❌ Errore durante la generazione del file Excel${NC}"
    exit 1
fi
