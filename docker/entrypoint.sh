#!/bin/sh
# ========================================
# 🚀 FLORENCE EGI - DOCKER ENTRYPOINT
# ========================================
# Gestisce timing, cache e variabili d'ambiente
# Risolve: APP_KEY not found, cache timing issues
#
# @package FlorenceEGI Docker Setup
# @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
# @version 1.0.0
# @date 2025-07-22
# ========================================

set -e

echo "🚀 Starting FlorenceEGI container initialization..."

# Se .env non esiste, copialo da .env_docker
if [ ! -f .env ]; then
  cp .env_docker .env
fi

# ========================================
# 1. VALIDATE CRITICAL ENVIRONMENT
# ========================================
if [ -z "$APP_KEY" ]; then
    echo "⚠️  APP_KEY not found in environment, checking .env file..."

    # Se APP_KEY non è nell'environment, proviamo a caricarla dal .env
    if [ -f /var/www/html/.env ]; then
        export APP_KEY=$(grep "^APP_KEY=" /var/www/html/.env | cut -d '=' -f2-)

        if [ -z "$APP_KEY" ]; then
            echo "❌ ERROR: APP_KEY not found in .env file either!"
            echo "🔧 Generating new APP_KEY..."
            cd /var/www/html && php artisan key:generate --force
            export APP_KEY=$(grep "^APP_KEY=" /var/www/html/.env | cut -d '=' -f2-)
        fi
    else
        echo "❌ ERROR: .env file not found!"
        exit 1
    fi
fi

echo "✅ APP_KEY validated: ${APP_KEY:0:20}..."

# ========================================
# 2. WAIT FOR SERVICES
# ========================================
echo "⏳ Waiting for MariaDB..."
while ! nc -z mariadb 3306 2>/dev/null; do
    echo "   MariaDB not ready, waiting..."
    sleep 2
done
echo "✅ MariaDB is ready!"

echo "⏳ Waiting for Redis..."
while ! nc -z redis 6379 2>/dev/null; do
    echo "   Redis not ready, waiting..."
    sleep 2
done
echo "✅ Redis is ready!"

# ========================================
# 3. SET PERMISSIONS
# ========================================
echo "🔧 Setting permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Create log file if not exists
touch /var/log/php_errors.log
chown www-data:www-data /var/log/php_errors.log

# ========================================
# 4. LARAVEL INITIALIZATION
# ========================================
echo "🎨 Initializing Laravel..."

# CRITICAL: Clear all caches first
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Run package discovery (since we skipped it during build)
php artisan package:discover --ansi

# Run migrations if needed
if [ "$APP_ENV" != "production" ] || [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "🗄️  Running migrations..."
    php artisan migrate --force
fi

# Create storage link
if [ ! -L /var/www/html/public/storage ]; then
    echo "🔗 Creating storage link..."
    php artisan storage:link
fi

# Cache configuration ONLY in production and AFTER everything is ready
if [ "$APP_ENV" = "production" ]; then
    echo "📦 Caching configuration for production..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

echo "✅ FlorenceEGI initialization complete!"

# 🔧 Fix dei permessi delle temp di Nginx
echo "🔧 Fixing nginx tmp permissions…"
mkdir -p /var/lib/nginx/tmp/client_body
chown -R nginx:nginx /var/lib/nginx/tmp

# 🔧 Assicuro l’esistenza di /tmp/nginx-client-body
mkdir -p /tmp/nginx-client-body

  # ========================================
  # 5. START SUPERVISORD
  # ========================================
  echo "🚀 Starting supervisord..."
  exec "$@"
