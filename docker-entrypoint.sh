#!/bin/sh
# Entrypoint: jalankan crond di background + php-fpm di foreground

# Pastikan folder log ada
mkdir -p /var/www/html/writable/logs

# Mulai cron daemon di background
crond -f -d 8 &

# Mulai php-fpm di foreground (proses utama)
exec php-fpm
