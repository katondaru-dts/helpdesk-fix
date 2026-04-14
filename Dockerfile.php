FROM php:8.2-fpm-alpine

# Install system dependencies and PHP extensions
RUN apk add --no-cache \
    icu-dev \
    libxml2-dev \
    curl-dev \
    oniguruma-dev \
    libpng-dev \
    zlib-dev

RUN docker-php-ext-install \
    intl \
    pdo_mysql \
    mysqli \
    mbstring \
    curl \
    xml \
    gd \
    opcache

# Set working directory
WORKDIR /var/www/html

# Copy application code
COPY . /var/www/html

# Adjust permissions for writable folders
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 777 /var/www/html/writable

EXPOSE 9000
CMD ["php-fpm"]
