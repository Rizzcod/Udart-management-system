# UDART MMS — production image (Laravel 11 on PHP 8.3 + Apache).
# Configuration comes from environment variables at runtime; no .env is baked in.

# ── PHP application with production Composer dependencies ────────────────────
FROM php:8.3-apache AS app

# PHP extensions required by composer.lock (gd, zip) plus the MySQL driver and opcache.
RUN apt-get update \
    && apt-get install -y --no-install-recommends libfreetype6-dev libjpeg62-turbo-dev libpng-dev libzip-dev unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" gd zip pdo_mysql opcache \
    && rm -rf /var/lib/apt/lists/*

# Production php.ini: display_errors=Off, expose_php=Off.
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Serve only public/, honour Laravel's .htaccess, listen on Render's $PORT,
# hide version banners, and cap workers to fit a 512 MB instance.
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public \
    PORT=8080
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && sed -ri -e 's/^Listen 80$/Listen ${PORT}/' /etc/apache2/ports.conf \
    && sed -ri -e 's/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/' /etc/apache2/sites-available/000-default.conf \
    && sed -ri -e 's/^ServerTokens .*/ServerTokens Prod/; s/^ServerSignature .*/ServerSignature Off/' /etc/apache2/conf-available/security.conf \
    && sed -ri -e 's/^(\s*MaxRequestWorkers\s+).*/\110/' /etc/apache2/mods-available/mpm_prefork.conf \
    && a2enmod rewrite

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# ── Front-end assets (Vite), built with the project's own package-lock.json ──
FROM node:22-bookworm-slim AS assets
WORKDIR /app
COPY --from=app /var/www/html /app
RUN npm ci --no-audit --no-fund && npm run build

# ── Final image ──────────────────────────────────────────────────────────────
FROM app
COPY --from=assets /app/public/build /var/www/html/public/build
RUN mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod +x docker/start.sh

CMD ["/var/www/html/docker/start.sh"]
