FROM php:7.4-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libonig-dev \
    libzip-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy app (sera complété plus tard)
COPY . /var/www/html

# Droits de base
RUN chown -R www-data:www-data /var/www/html

EXPOSE 9000

CMD ["php-fpm"]


