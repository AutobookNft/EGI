# ========================================
# 🚀 FLORENCE EGI - DOCKERFILE v4.0.0 (APP_KEY FIX)
# ========================================
# Fix per APP_KEY error con entrypoint e timing corretto
#
# @package FlorenceEGI Docker Setup
# @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
# @version 4.0.0 (APP_KEY Error Fixed)
# @date 2025-07-22
# ========================================

# Build arguments con valori di default sicuri
ARG UID=1000
ARG GID=1000

FROM php:8.3-fpm-alpine

# Re-declare ARGs after FROM
ARG UID=1000
ARG GID=1000

# Install all dependencies in one go (evita duplicazioni)
RUN apk add --no-cache \
    shadow \
    git curl zip unzip \
    libzip-dev oniguruma-dev \
    nginx supervisor autoconf build-base \
    netcat-openbsd \
    nodejs npm

# ========================================
# 🔧 IMAGEMAGICK CONFIGURATION
# ========================================

# Install ImageMagick e tutti i codec necessari
RUN apk add --no-cache \
      imagemagick imagemagick-dev \
      libjpeg-turbo libjpeg-turbo-dev \
      libpng libpng-dev \
      libwebp libwebp-dev \
      tiff-dev freetype-dev && \
    pecl install imagick && \
    docker-php-ext-enable imagick

# ========================================
# 🔧 USER MANAGEMENT
# ========================================

# Setup www-data user with correct UID/GID
RUN set -eux; \
    if id www-data >/dev/null 2>&1; then \
        groupmod -g ${GID} www-data; \
        usermod -u ${UID} -g www-data www-data; \
    else \
        addgroup -g ${GID} www-data; \
        adduser -D -H -u ${UID} -s /bin/sh -G www-data www-data; \
    fi; \
    echo "www-data user configured with UID=${UID}, GID=${GID}"

# ========================================
# 🛠️ PHP EXTENSIONS
# ========================================

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j$(nproc) pdo_mysql mysqli zip exif pcntl gd bcmath mbstring

# Install Redis extension
RUN pecl install redis && \
    docker-php-ext-enable redis

# Clean build dependencies
RUN apk del autoconf build-base

# ========================================
# 📦 COMPOSER INSTALLATION
# ========================================

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files first for better caching
COPY composer.json composer.lock ./

# Copy the local package (ultra/egi-module)
COPY packages/ultra/egi-module ./packages/ultra/egi-module

# Install dependencies - IMPORTANTE: --no-scripts per evitare errori
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install \
    --no-dev \
    --no-interaction \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --optimize-autoloader

# Copy application files
COPY . .

# NOW generate autoloader after all files are in place (still no scripts)
RUN COMPOSER_ALLOW_SUPERUSER=1 composer dump-autoload --optimize --no-scripts

# ========================================
# 🎨 NODE.JS & ASSET COMPILATION
# ========================================

# Install Node dependencies and build assets
RUN npm install && npm run build

# Clean up node_modules after build to reduce image size (optional)
# RUN rm -rf node_modules

# Create cache directories before running any artisan commands
RUN mkdir -p storage/framework/cache/data \
    && mkdir -p bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# Copy Docker configuration files
COPY docker/supervisor/supervisord.conf /etc/supervisor/supervisord.conf
COPY docker/php/php.ini /usr/local/etc/php/php.ini
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf

# Copy entrypoint script
COPY docker/entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# ========================================
# 📁 DIRECTORY CREATION & PERMISSIONS
# ========================================

# Create necessary directories
RUN mkdir -p \
    /var/log/nginx \
    /var/log/supervisor \
    /run/nginx \
    /var/run \
    /var/lib/nginx/tmp/client_body \
    /var/lib/nginx/tmp/proxy \
    /var/lib/nginx/tmp/fastcgi \
    /var/www/html/storage/app/public \
    /var/www/html/storage/framework/cache/data \
    /var/www/html/storage/framework/sessions \
    /var/www/html/storage/framework/views \
    /var/www/html/storage/logs \
    /var/www/html/bootstrap/cache

# Create PHP error log file
RUN touch /var/log/php_errors.log

# Set correct ownership
RUN chown -R www-data:www-data \
    /var/www/html \
    /var/log/nginx \
    /var/log/supervisor \
    /var/log/php_errors.log \
    /run/nginx \
    /var/run \
    /var/lib/nginx

# Set proper permissions
RUN chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# ========================================
# 🚀 CONTAINER STARTUP
# ========================================

# Expose port 80
EXPOSE 80

# Use entrypoint for proper initialization
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]

# Default command
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]
