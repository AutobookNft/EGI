#!/bin/bash

# Script per creare i MenuItem per il sistema GDPR di FlorenceEGI
# Author: Padmin D. Curtis
# Date: $(date)

# Colori per output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}🎯 Creazione MenuItem per sistema GDPR FlorenceEGI${NC}"
echo -e "${BLUE}=================================================${NC}\n"

# Directory di destinazione
MENU_DIR="app/Services/Menu/Items"

# Verifica che la directory esista
if [ ! -d "$MENU_DIR" ]; then
    echo -e "${GREEN}📁 Creazione directory $MENU_DIR${NC}"
    mkdir -p "$MENU_DIR"
fi

# Array associativo con i dati per ogni MenuItem
declare -A menu_items=(
    ["ConsentMenu"]="menu.consent|gdpr.consent.index|shield-check|manage_consent"
    ["ExportDataMenu"]="menu.export_data|gdpr.export.index|download|export_personal_data"
    ["EditPersonalDataMenu"]="menu.edit_personal_data|profile.edit|user-edit|edit_personal_data"
    ["LimitProcessingMenu"]="menu.limit_processing|gdpr.processing.index|user-minus-circle|limit_data_processing"
    ["DeleteAccountMenu"]="menu.delete_account|gdpr.account.delete|user-x|delete_account"
    ["ActivityLogMenu"]="menu.activity_log|gdpr.activity.index|clock|view_activity_log"
    ["BreachReportMenu"]="menu.breach_report|gdpr.breach.index|alert-triangle|view_breach_reports"
    ["PrivacyPolicyMenu"]="menu.privacy_policy|legal.privacy|file-text|view_privacy_policy"
    ["BackToDashboardMenu"]="menu.back_to_dashboard|dashboard.index|arrow-left|access_dashboard"
)

# Funzione per creare un singolo MenuItem
create_menu_item() {
    local class_name=$1
    local translation_key=$2
    local route=$3
    local icon=$4
    local permission=$5

    local file_path="$MENU_DIR/${class_name}.php"

    echo -e "${GREEN}📝 Creando ${class_name}...${NC}"

    cat > "$file_path" << EOF
<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * @Oracode Menu Item: ${class_name}
 * 🎯 Purpose: GDPR compliance menu item for ${translation_key}
 * 🛡️ Privacy: Handles GDPR-related functionality
 *
 * @package App\Services\Menu\Items
 * @version 1.0
 */
class ${class_name} extends MenuItem
{
    /**
     * Constructor
     *
     * @privacy-safe Initializes GDPR menu item with appropriate permissions
     */
    public function __construct()
    {
        parent::__construct(
            '${translation_key}',
            '${route}',
            '${icon}',
            '${permission}'
        );
    }
}
EOF

    echo -e "${GREEN}✅ ${class_name} creato con successo${NC}"
}

# Creazione di tutti i MenuItem
echo -e "${BLUE}🚀 Inizio creazione MenuItem...${NC}\n"

for class_name in "${!menu_items[@]}"; do
    IFS='|' read -r translation_key route icon permission <<< "${menu_items[$class_name]}"
    create_menu_item "$class_name" "$translation_key" "$route" "$icon" "$permission"
    echo
done

echo -e "${GREEN}🎉 Tutti i MenuItem sono stati creati con successo!${NC}"
echo -e "${BLUE}📍 File creati in: $MENU_DIR${NC}"
echo -e "${BLUE}📝 Prossimo step: Aggiungere le icone SVG al config/icons.php${NC}"
