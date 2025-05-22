#!/bin/bash

# FlorenceEGI Reservation System - Complete File Collector
# Author: Padmin D. Curtis (for Fabio Cherici)
# Date: May 16, 2025
# Purpose: Collect all reservation system files into a single text file for AI reference

# Color codes for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
OUTPUT_DIR="$HOME/AI-files/prenotations"
OUTPUT_FILE="$OUTPUT_DIR/reservation_system_complete.txt"
LARAVEL_ROOT="$(pwd)"

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Function to add file separator
add_separator() {
    local title="$1"
    echo "" >> "$OUTPUT_FILE"
    echo "################################################################################" >> "$OUTPUT_FILE"
    echo "# $title" >> "$OUTPUT_FILE"
    echo "################################################################################" >> "$OUTPUT_FILE"
    echo "" >> "$OUTPUT_FILE"
}

# Function to add file content with header
add_file_content() {
    local file_path="$1"
    local description="$2"
    local relative_path="${file_path#$LARAVEL_ROOT/}"

    if [ -f "$file_path" ]; then
        echo "=== FILE: $relative_path ===" >> "$OUTPUT_FILE"
        echo "Description: $description" >> "$OUTPUT_FILE"
        echo "Last Modified: $(stat -c %y "$file_path" 2>/dev/null || stat -f "%Sm" "$file_path" 2>/dev/null || echo "Unknown")" >> "$OUTPUT_FILE"
        echo "Size: $(wc -c < "$file_path" 2>/dev/null || echo "Unknown") bytes" >> "$OUTPUT_FILE"
        echo "" >> "$OUTPUT_FILE"
        cat "$file_path" >> "$OUTPUT_FILE"
        echo "" >> "$OUTPUT_FILE"
        echo "=== END FILE: $relative_path ===" >> "$OUTPUT_FILE"
        echo "" >> "$OUTPUT_FILE"
        print_success "Added: $relative_path"
    else
        print_warning "File not found: $relative_path"
        echo "=== MISSING FILE: $relative_path ===" >> "$OUTPUT_FILE"
        echo "Description: $description" >> "$OUTPUT_FILE"
        echo "Status: FILE NOT FOUND" >> "$OUTPUT_FILE"
        echo "" >> "$OUTPUT_FILE"
    fi
}

# Function to check Laravel root
check_laravel_root() {
    if [ ! -f "artisan" ] || [ ! -f "composer.json" ]; then
        print_error "This script must be run from the Laravel project root directory"
        print_error "Current directory: $(pwd)"
        print_error "Please cd to your Laravel project root and run this script again"
        exit 1
    fi
}

# Function to create output directory
create_output_dir() {
    if [ ! -d "$OUTPUT_DIR" ]; then
        print_status "Creating output directory: $OUTPUT_DIR"
        mkdir -p "$OUTPUT_DIR"
        if [ $? -eq 0 ]; then
            print_success "Created directory: $OUTPUT_DIR"
        else
            print_error "Failed to create directory: $OUTPUT_DIR"
            exit 1
        fi
    else
        print_status "Output directory already exists: $OUTPUT_DIR"
    fi
}

# Function to initialize output file
init_output_file() {
    print_status "Initializing output file: $OUTPUT_FILE"

    cat > "$OUTPUT_FILE" << 'EOF'
################################################################################
# FlorenceEGI Reservation System - Complete Source Code Collection
################################################################################
#
# Generated on: $(date)
# Laravel Project: FlorenceEGI
# System: Reservation and Certificate Management
# Architecture: Ultra Ecosystem + Oracode 2.0 Compliant
# Author: Padmin D. Curtis (for Fabio Cherici)
#
# Purpose:
# This file contains all source code files that constitute the FlorenceEGI
# Reservation System. It's designed to provide AI assistants with complete
# context about the system for debugging, testing, and evolution purposes.
#
# Contents:
# - Database Migrations
# - Models (Eloquent)
# - Controllers (Web & API)
# - Services (Business Logic)
# - Form Requests (Validation)
# - TypeScript Services & Features
# - Blade Views (Certificates)
# - Configuration Files
# - Translation Files
# - Route Definitions
#
# Usage:
# This file can be provided to AI assistants to understand the complete
# system architecture and make informed suggestions for modifications,
# debugging, or extensions.
#
################################################################################

EOF

    # Replace the date placeholder
    sed -i "s/\$(date)/$(date)/" "$OUTPUT_FILE" 2>/dev/null || \
    sed -i '' "s/\$(date)/$(date)/" "$OUTPUT_FILE" 2>/dev/null

    print_success "Output file initialized"
}

# Main execution
main() {
    print_status "Starting FlorenceEGI Reservation System file collection..."

    # Check if we're in Laravel root
    check_laravel_root

    # Create output directory
    create_output_dir

    # Initialize output file
    init_output_file

    # Database Migrations
    add_separator "DATABASE MIGRATIONS"
    add_file_content "$LARAVEL_ROOT/database/migrations/2024_12_10_000001_extend_reservations_table.php" "Extends existing reservations table for EGI reservation system"
    add_file_content "$LARAVEL_ROOT/database/migrations/2024_12_10_000002_create_egi_reservation_certificates_table.php" "Creates table for storing reservation certificates"

    # Models
    add_separator "ELOQUENT MODELS"
    add_file_content "$LARAVEL_ROOT/app/Models/Reservation.php" "Extended Reservation model with EGI-specific methods"
    add_file_content "$LARAVEL_ROOT/app/Models/EgiReservationCertificate.php" "Certificate model for reservation certificates"

    # Controllers
    add_separator "CONTROLLERS"
    add_file_content "$LARAVEL_ROOT/app/Http/Controllers/Api/ReservationController.php" "API controller for reservation management"
    add_file_content "$LARAVEL_ROOT/app/Http/Controllers/Api/CurrencyController.php" "API controller for exchange rate management"
    add_file_content "$LARAVEL_ROOT/app/Http/Controllers/EgiReservationCertificateController.php" "Web controller for certificate display and verification"
    add_file_content "$LARAVEL_ROOT/app/Http/Controllers/Api/AppConfigController.php" "Updated app config controller with reservation translations"

    # Services
    add_separator "BUSINESS LOGIC SERVICES"
    add_file_content "$LARAVEL_ROOT/app/Services/ReservationService.php" "Core business logic for reservation management"
    add_file_content "$LARAVEL_ROOT/app/Services/CurrencyService.php" "Currency conversion and exchange rate service"
    add_file_content "$LARAVEL_ROOT/app/Services/CertificateGeneratorService.php" "Certificate generation and PDF creation service"

    # Form Requests
    add_separator "FORM REQUESTS (VALIDATION)"
    add_file_content "$LARAVEL_ROOT/app/Http/Requests/StoreReservationRequest.php" "Validation rules for creating reservations"
    add_file_content "$LARAVEL_ROOT/app/Http/Requests/UpdateReservationRequest.php" "Validation rules for updating reservations"

    # TypeScript Services
    add_separator "TYPESCRIPT SERVICES & FEATURES"
    add_file_content "$LARAVEL_ROOT/resources/ts/services/reservationService.ts" "Frontend reservation service with API communication"
    add_file_content "$LARAVEL_ROOT/resources/ts/features/reservations/reservationFeature.ts" "Reservation feature integration module"
    add_file_content "$LARAVEL_ROOT/resources/ts/features/reservations/reservationButtons.ts" "Reservation button management and state updates"
    add_file_content "$LARAVEL_ROOT/resources/ts/main.ts" "Updated main TypeScript entry point"

    # Views
    add_separator "BLADE VIEWS"
    add_file_content "$LARAVEL_ROOT/resources/views/certificates/show.blade.php" "Certificate display view"
    add_file_content "$LARAVEL_ROOT/resources/views/certificates/verify.blade.php" "Certificate verification view"
    add_file_content "$LARAVEL_ROOT/resources/views/egis/show.blade.php" "EGI detail view (existing, contains reservation button)"
    add_file_content "$LARAVEL_ROOT/resources/views/components/egi-card.blade.php" "EGI card component (existing, contains reservation button)"

    # Configuration Files
    add_separator "CONFIGURATION FILES"
    add_file_content "$LARAVEL_ROOT/config/error-manager.php" "UEM error configuration with reservation error codes"
    add_file_content "$LARAVEL_ROOT/config/icons.php" "Icon configuration (updated for reservation system)"

    # Translation Files
    add_separator "TRANSLATION FILES"
    add_file_content "$LARAVEL_ROOT/resources/lang/en/reservation.php" "English translations for reservation system"
    add_file_content "$LARAVEL_ROOT/resources/lang/it/reservation.php" "Italian translations for reservation system"
    add_file_content "$LARAVEL_ROOT/resources/lang/en/certificate.php" "English translations for certificates"
    add_file_content "$LARAVEL_ROOT/resources/lang/it/certificate.php" "Italian translations for certificates"
    add_file_content "$LARAVEL_ROOT/resources/lang/en/errors.php" "Updated English error messages"
    add_file_content "$LARAVEL_ROOT/resources/lang/it/errors.php" "Updated Italian error messages"

    # Route Files
    add_separator "ROUTE DEFINITIONS"
    add_file_content "$LARAVEL_ROOT/routes/api.php" "API routes for reservation system"
    add_file_content "$LARAVEL_ROOT/routes/web.php" "Web routes for certificate display"

    # Repository Files
    add_separator "REPOSITORY CLASSES"
    add_file_content "$LARAVEL_ROOT/app/Repositories/IconRepository.php" "Icon repository for UI elements"

    # Seeder Files
    add_separator "DATABASE SEEDERS"
    add_file_content "$LARAVEL_ROOT/database/seeders/IconSeeder.php" "Icon seeder for reservation system icons"

    # Additional Configuration
    add_separator "ADDITIONAL REFERENCE FILES"
    add_file_content "$LARAVEL_ROOT/resources/ts/config/appConfig.ts" "Frontend app configuration (reference)"
    add_file_content "$LARAVEL_ROOT/resources/ts/dom/domElements.ts" "DOM element references (reference)"
    add_file_content "$LARAVEL_ROOT/resources/ts/utils/csrf.ts" "CSRF token utilities (reference)"

    # Add summary at the end
    add_separator "COLLECTION SUMMARY"

    cat >> "$OUTPUT_FILE" << EOF
Collection completed on: $(date)
Laravel Project Root: $LARAVEL_ROOT
Output File: $OUTPUT_FILE

File Count Summary:
- Database Migrations: 2
- Models: 2
- Controllers: 4
- Services: 3
- Form Requests: 2
- TypeScript Files: 4
- Blade Views: 4
- Configuration Files: 2
- Translation Files: 6
- Route Files: 2
- Repository Files: 1
- Seeder Files: 1
- Reference Files: 3

Total Files: 36

This collection provides complete source code context for the FlorenceEGI
Reservation System, enabling AI assistants to understand the full architecture
and make informed suggestions for modifications, debugging, or extensions.

System Features:
- Multi-level authentication (weak/strong auth)
- Dynamic EUR/ALGO currency conversion
- Digital certificate generation with PDF export
- Priority-based reservation system
- Complete UEM error handling integration
- Fully accessible UI with ARIA support
- Schema.org markup for SEO optimization
- Comprehensive testing framework ready
- Web3 evolution pathway prepared

For technical questions or system evolution, refer to the complete
technical documentation provided separately.
EOF

    print_success "File collection completed successfully!"
    print_status "Output file: $OUTPUT_FILE"
    print_status "File size: $(wc -c < "$OUTPUT_FILE" 2>/dev/null || echo "Unknown") bytes"
    print_status "Total lines: $(wc -l < "$OUTPUT_FILE" 2>/dev/null || echo "Unknown")"

    echo ""
    print_status "You can now provide this file to AI assistants for:"
    echo "  - System understanding and analysis"
    echo "  - Debugging assistance"
    echo "  - Feature evolution and extension"
    echo "  - Code review and optimization"
    echo "  - Testing strategy development"
    echo ""
    print_success "Collection process completed successfully!"
}

# Run the main function
main "$@"
