FROM php:8.2-fpm-alpine

# Install system dependencies and PHP extensions
RUN apk add --no-cache \
    icu-dev \
    libxml2-dev \
    curl-dev \
    oniguruma-dev \
    libpng-dev \
    zlib-dev \
    imap-dev \
    krb5-dev \
    openssl-dev

RUN docker-php-ext-install \
    intl \
    pdo_mysql \
    mysqli \
    mbstring \
    curl \
    xml \
    gd \
    exif \
    opcache \
    imap

# Copy custom php.ini
COPY php.ini $PHP_INI_DIR/php.ini

# Set working directory
WORKDIR /var/www/html

# Copy application code
COPY . /var/www/html

# Adjust permissions for writable folders
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 777 /var/www/html/writable

# Setup cron job untuk email reply sync (setiap 2 menit)
RUN echo '*/2 * * * * cd /var/www/html && php spark cron:fetch-email-replies >> /var/www/html/writable/logs/email-replies.log 2>&1' | crontab - \
    && echo '*/5 * * * * cd /var/www/html && php spark cron:check-sla >> /dev/null 2>&1' | crontab -u root -

# Entrypoint: jalankan crond + php-fpm bersamaan
COPY docker-entrypoint.sh /docker-entrypoint.sh
RUN chmod +x /docker-entrypoint.sh

EXPOSE 9000
CMD ["/docker-entrypoint.sh"]
