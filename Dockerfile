# Get PHP image with Alpine Linux
FROM php:8.3-fpm-alpine
# Install system dependencies
RUN apk add --no-cache \
    git curl zip unzip bash \
    nodejs npm \
    libpng-dev libjpeg-turbo-dev freetype-dev \
    oniguruma-dev libxml2-dev libzip-dev
# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    pdo_mysql mbstring exif pcntl bcmath gd xml zip
# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /var/www
# Copy project files
COPY . .
# Install PHP & Node dependencies, then build frontend
RUN composer install --optimize-autoloader --no-dev --no-interaction \
    && npm install \
    && npm run build \
    && rm -rf node_modules
# Change ownership of /var/www to www-data
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache
# Expose port
EXPOSE 9000
# Start PHP-FPM
CMD ["php-fpm"]