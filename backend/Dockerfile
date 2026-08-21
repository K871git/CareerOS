# backend/Dockerfile
# Laravel 13 API — built for Render (Root Directory = backend)

FROM php:8.3-cli

# ---- System deps + PHP extensions Laravel/MySQL need ----
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    default-mysql-client \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        zip \
        exif \
        pcntl \
        bcmath \
    && rm -rf /var/lib/apt/lists/*

# ---- Composer ----
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy composer files first for better layer caching
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Copy the rest of the app
COPY . .

# Finish composer setup now that artisan + full app are present
RUN composer dump-autoload --optimize \
    && php artisan config:clear

# Storage & cache dirs need to be writable
RUN chmod -R 775 storage bootstrap/cache

# Make the entrypoint script executable
RUN chmod +x docker-entrypoint.sh

EXPOSE 10000

# Entrypoint: migrate → seed-if-empty → serve
CMD ["./docker-entrypoint.sh"]
