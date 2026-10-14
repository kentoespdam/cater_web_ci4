#!/bin/bash
set -e

# Buat sub-direktori writable jika belum ada
mkdir -p /var/www/html/writable/cache \
         /var/www/html/writable/debugbar \
         /var/www/html/writable/logs \
         /var/www/html/writable/session \
         /var/www/html/writable/uploads

# Set permission lokal yang aman hanya untuk folder writable
chown -R www-data:www-data /var/www/html/writable || true
chmod -R 775 /var/www/html/writable || true

# Jalankan perintah CMD asli
exec "$@"


