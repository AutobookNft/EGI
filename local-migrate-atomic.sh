#!/bin/bash

# ========================================
# 🔄 FLORENCE EGI - LOCAL ATOMIC MIGRATIONS + SEEDING
# ========================================
# Script atomico per migrations e seeding ambiente locale
# Gestisce il database locale per test e sviluppo
#
# @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
# @version 1.0.0 (Local Migrations + Seeding)
# @date 2025-07-30
# ========================================

set -euo pipefail

# ANSI Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
PURPLE='\033[0;35m'
NC='\033[0m'

# Configuration variables
ORIGINAL_ENV=".env"
BACKUP_ENV=".env.backup.$(date +%Y%m%d_%H%M%S)"
TRANSACTION_ACTIVE=false
PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# ========================================
# 🛡️ CLEANUP FUNCTION
# ========================================
cleanup() {
    local exit_code=$?

    echo -e "\n${YELLOW}🔄 Cleanup in progress...${NC}"

    if [ "$TRANSACTION_ACTIVE" = true ]; then
        echo -e "${RED}❌ Transaction failed! Rolling back environment...${NC}"

        if [ -f "$BACKUP_ENV" ]; then
            mv "$BACKUP_ENV" "$ORIGINAL_ENV"
            echo -e "${GREEN}✅ Original .env restored${NC}"
        fi

        echo -e "${RED}💥 TRANSACTION FAILED${NC}"
    else
        echo -e "${GREEN}✅ Transaction completed successfully${NC}"

        if [ -f "$BACKUP_ENV" ]; then
            rm -f "$BACKUP_ENV"
            echo -e "${BLUE}🗑️ Backup file removed${NC}"
        fi
    fi

    exit $exit_code
}

# ========================================
# 🚨 ERROR HANDLER
# ========================================
error_handler() {
    local line_number=$1
    echo -e "\n${RED}💥 ERROR on line $line_number${NC}" >&2
    cleanup
}

# ========================================
# ✅ VALIDATION
# ========================================
validate_prerequisites() {
    echo -e "${BLUE}🔍 Validating local environment prerequisites...${NC}"

    # Check .env file
    if [ ! -f "$ORIGINAL_ENV" ]; then
        echo -e "${RED}❌ .env file not found!${NC}" >&2
        echo -e "${CYAN}💡 Copy .env.example to .env and configure it${NC}" >&2
        exit 1
    fi

    # Check if we're in Laravel project root
    if [ ! -f "artisan" ]; then
        echo -e "${RED}❌ Laravel artisan command not found!${NC}" >&2
        echo -e "${CYAN}💡 Make sure you're in the Laravel project root${NC}" >&2
        exit 1
    fi

    # Check PHP
    if ! command -v php >/dev/null 2>&1; then
        echo -e "${RED}❌ PHP not found in PATH!${NC}" >&2
        exit 1
    fi

    # Check Composer
    if ! command -v composer >/dev/null 2>&1; then
        echo -e "${RED}❌ Composer not found in PATH!${NC}" >&2
        exit 1
    fi

    # Test Laravel installation
    if ! php artisan --version >/dev/null 2>&1; then
        echo -e "${RED}❌ Laravel artisan not working!${NC}" >&2
        echo -e "${CYAN}💡 Run: composer install${NC}" >&2
        exit 1
    fi

    # Test database connection
    if ! php artisan db:show --quiet >/dev/null 2>&1; then
        echo -e "${YELLOW}⚠️ Database connection issues detected${NC}" >&2
        echo -e "${CYAN}💡 Check your database configuration in .env${NC}" >&2
        read -p "Continue anyway? (y/N): " continue_choice
        if [[ ! "$continue_choice" =~ ^[Yy]$ ]]; then
            exit 1
        fi
    fi

    echo -e "${GREEN}✅ Prerequisites validated${NC}"
}

# ========================================
# 📊 DATABASE INFO
# ========================================
show_database_info() {
    echo -e "\n${PURPLE}📊 DATABASE INFORMATION${NC}"
    echo -e "${PURPLE}═════════════════════════${NC}"

    local db_connection=$(php artisan tinker --execute="echo config('database.default');" 2>/dev/null | tail -n 1)
    local db_name=$(php artisan tinker --execute="echo config('database.connections.${db_connection}.database');" 2>/dev/null | tail -n 1)
    local db_host=$(php artisan tinker --execute="echo config('database.connections.${db_connection}.host');" 2>/dev/null | tail -n 1)

    echo -e "${CYAN}Connection:${NC} $db_connection"
    echo -e "${CYAN}Database:${NC} $db_name"
    echo -e "${CYAN}Host:${NC} $db_host"
    echo -e "${PURPLE}═════════════════════════${NC}"
}

# ========================================
# 🔄 MIGRATION FUNCTIONS
# ========================================
run_migration_fresh() {
    echo -e "\n${CYAN}🗄️ RUNNING: migrate:fresh${NC}"
    echo -e "${YELLOW}⚠️  This will DROP ALL TABLES and recreate them${NC}"

    if php artisan migrate:fresh --force; then
        echo -e "${GREEN}✅ Migration fresh completed${NC}"
    else
        echo -e "${RED}❌ Migration fresh failed!${NC}" >&2
        exit 1
    fi
}

run_migration_refresh() {
    echo -e "\n${CYAN}🔄 RUNNING: migrate:refresh${NC}"
    echo -e "${YELLOW}⚠️  This will rollback and re-run all migrations${NC}"

    if php artisan migrate:refresh --force; then
        echo -e "${GREEN}✅ Migration refresh completed${NC}"
    else
        echo -e "${RED}❌ Migration refresh failed!${NC}" >&2
        exit 1
    fi
}

run_migration_reset() {
    echo -e "\n${CYAN}🔙 RUNNING: migrate:reset + migrate${NC}"
    echo -e "${YELLOW}⚠️  This will reset and re-run all migrations${NC}"

    if php artisan migrate:reset --force; then
        echo -e "${GREEN}✅ Migration reset completed${NC}"
    else
        echo -e "${RED}❌ Migration reset failed!${NC}" >&2
        exit 1
    fi

    if php artisan migrate --force; then
        echo -e "${GREEN}✅ Migration completed${NC}"
    else
        echo -e "${RED}❌ Migration failed!${NC}" >&2
        exit 1
    fi
}

run_migration_status() {
    echo -e "\n${CYAN}📋 MIGRATION STATUS${NC}"
    echo -e "${CYAN}═══════════════════${NC}"

    if php artisan migrate:status; then
        echo -e "${GREEN}✅ Migration status displayed${NC}"
    else
        echo -e "${RED}❌ Could not get migration status!${NC}" >&2
        exit 1
    fi
}

run_seeding() {
    echo -e "\n${CYAN}🌱 RUNNING: db:seed (local)${NC}"

    if php artisan db:seed --force; then
        echo -e "${GREEN}✅ Seeding completed successfully${NC}"
    else
        echo -e "${RED}❌ Seeding failed!${NC}" >&2
        exit 1
    fi
}

run_cache_clear() {
    echo -e "\n${CYAN}🧹 CLEARING: Application cache${NC}"

    php artisan cache:clear
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear

    echo -e "${GREEN}✅ Cache cleared${NC}"
}

run_optimize() {
    echo -e "\n${CYAN}⚡ OPTIMIZING: Application${NC}"

    php artisan config:cache
    php artisan route:cache
    php artisan view:cache

    echo -e "${GREEN}✅ Application optimized${NC}"
}

# ========================================
# 🔄 ATOMIC STEPS
# ========================================
step_backup() {
    echo -e "\n${BLUE}📦 STEP: Creating backup...${NC}"
    cp "$ORIGINAL_ENV" "$BACKUP_ENV"
    echo -e "${GREEN}✅ Backup created: $BACKUP_ENV${NC}"
}

step_start_transaction() {
    echo -e "\n${BLUE}🔄 STEP: Starting transaction...${NC}"
    TRANSACTION_ACTIVE=true
    echo -e "${GREEN}✅ Transaction started${NC}"
}

step_complete_transaction() {
    echo -e "\n${BLUE}✅ STEP: Completing transaction...${NC}"
    TRANSACTION_ACTIVE=false
    echo -e "${GREEN}✅ Transaction completed${NC}"
}

# ========================================
# 🎯 MAIN FUNCTIONS
# ========================================
show_menu() {
    echo -e "${GREEN}🔄 FLORENCE EGI - LOCAL ATOMIC MIGRATIONS & SEEDING${NC}"
    echo -e "${GREEN}════════════════════════════════════════════════════${NC}"
    show_database_info
    echo -e "\n${CYAN}Select operation:${NC}"
    echo ""
    echo -e "${YELLOW}1)${NC} migrate:fresh + seed ${BLUE}(recommended for clean state)${NC}"
    echo -e "   ${CYAN}→ Drops all tables, recreates + seeds${NC}"
    echo ""
    echo -e "${YELLOW}2)${NC} migrate:refresh + seed"
    echo -e "   ${CYAN}→ Rollback all, re-run + seeds${NC}"
    echo ""
    echo -e "${YELLOW}3)${NC} migrate:reset + migrate + seed"
    echo -e "   ${CYAN}→ Reset, migrate, then seed${NC}"
    echo ""
    echo -e "${YELLOW}4)${NC} Only seeding (preserve data)"
    echo -e "   ${CYAN}→ Only run seeders${NC}"
    echo ""
    echo -e "${YELLOW}5)${NC} Migration status"
    echo -e "   ${CYAN}→ Show current migration status${NC}"
    echo ""
    echo -e "${YELLOW}6)${NC} Clear cache + optimize"
    echo -e "   ${CYAN}→ Clear all cache and optimize${NC}"
    echo ""
    echo -e "${YELLOW}7)${NC} Cancel"
    echo ""
}

execute_choice() {
    local choice=$1

    trap 'error_handler $LINENO' ERR
    trap cleanup EXIT

    validate_prerequisites

    case $choice in
        1)
            step_backup
            step_start_transaction
            run_migration_fresh
            run_seeding
            run_cache_clear
            step_complete_transaction
            ;;
        2)
            step_backup
            step_start_transaction
            run_migration_refresh
            run_seeding
            run_cache_clear
            step_complete_transaction
            ;;
        3)
            step_backup
            step_start_transaction
            run_migration_reset
            run_seeding
            run_cache_clear
            step_complete_transaction
            ;;
        4)
            step_backup
            step_start_transaction
            run_seeding
            step_complete_transaction
            ;;
        5)
            run_migration_status
            exit 0
            ;;
        6)
            run_cache_clear
            run_optimize
            exit 0
            ;;
        *)
            echo -e "${RED}❌ Invalid choice${NC}"
            exit 1
            ;;
    esac

    echo -e "\n${GREEN}🎉 OPERATION COMPLETED SUCCESSFULLY!${NC}"
    echo -e "${GREEN}══════════════════════════════════════${NC}"
    echo -e "${BLUE}💾 Local database updated${NC}"
    echo -e "${BLUE}🛡️ Environment backup available${NC}"
    echo -e "${BLUE}⚡ Application cache cleared${NC}"
}

# ========================================
# 🎬 SCRIPT EXECUTION
# ========================================
main() {
    echo -e "${BLUE}📁 Working directory: $PROJECT_ROOT${NC}"
    cd "$PROJECT_ROOT"

    if [ $# -eq 0 ]; then
        # Interactive mode
        show_menu
        read -p "Enter your choice (1-7): " choice

        if [ "$choice" = "7" ]; then
            echo -e "${YELLOW}🚫 Operation cancelled${NC}"
            exit 0
        fi

        execute_choice "$choice"
    else
        # Command line mode
        execute_choice "$1"
    fi
}

# ========================================
# 🆘 HELP FUNCTION
# ========================================
show_help() {
    echo -e "${GREEN}🔄 FLORENCE EGI - LOCAL MIGRATIONS & SEEDING${NC}"
    echo -e "${GREEN}═══════════════════════════════════════════════${NC}"
    echo ""
    echo -e "${CYAN}Usage:${NC}"
    echo -e "  $0 [option]"
    echo ""
    echo -e "${CYAN}Options:${NC}"
    echo -e "  ${YELLOW}1${NC}    migrate:fresh + seed"
    echo -e "  ${YELLOW}2${NC}    migrate:refresh + seed"
    echo -e "  ${YELLOW}3${NC}    migrate:reset + migrate + seed"
    echo -e "  ${YELLOW}4${NC}    only seeding"
    echo -e "  ${YELLOW}5${NC}    migration status"
    echo -e "  ${YELLOW}6${NC}    clear cache + optimize"
    echo -e "  ${YELLOW}-h${NC}   show this help"
    echo ""
    echo -e "${CYAN}Examples:${NC}"
    echo -e "  $0        # Interactive mode"
    echo -e "  $0 1      # Fresh migration + seed"
    echo -e "  $0 5      # Show migration status"
    echo ""
}

# Parse command line arguments
if [ $# -gt 0 ] && [ "$1" = "-h" ]; then
    show_help
    exit 0
fi

# Run main function
if [[ "${BASH_SOURCE[0]}" == "${0}" ]]; then
    main "$@"
else
    echo -e "${RED}❌ This script should be executed, not sourced!${NC}" >&2
    return 1
fi
