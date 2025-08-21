# Stage 1: Builder
# Use the composer image to install dependencies, which keeps the final image clean.
FROM composer:2 AS composer_build
WORKDIR /app
# Copy only the composer files to leverage Docker cache
COPY composer.json composer.lock ./
# Install Composer dependencies, skipping dev dependencies
RUN composer install --no-dev --no-scripts --no-autoloader

# Stage 2: Production Image
# Use a lightweight PHP-FPM Alpine base image.
FROM php:8.2-fpm-alpine

# Install system dependencies and PHP extensions
RUN apk add --no-cache \
    $PHPIZE_DEPS \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    freetype-dev \
    git \
    supervisor \
    mysql-client \
    oniguruma-dev \
    unzip \
    zip \
    libzip-dev \
    openssl-dev \
    mariadb-connector-c-dev \
    libxml2-dev

# Install OpenSwoole and other required PHP extensions
RUN pecl install openswoole \
    && docker-php-ext-enable openswoole \
    && docker-php-ext-configure gd --with-jpeg --with-freetype --with-webp \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql opcache exif pcntl bcmath zip

# Configure PHP settings for Octane
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.interned_strings_buffer=8" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.max_accelerated_files=10000" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.revalidate_freq=0" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini

# Set working directory
WORKDIR /var/www/html

# Copy application files and installed dependencies from the builder stage
COPY --from=composer_build /app/vendor /var/www/html/vendor
COPY . .

# Set permissions for storage and bootstrap cache directories
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose the port Swoole will listen on
EXPOSE 8000

# Start the Octane server
CMD ["php", "artisan", "octane:start", "--server=swoole", "--host=0.0.0.0", "--port=8000"]