#!/bin/bash
set -e

# Buat sub-direktori writable jika belum ada
mkdir -p /var/www/html/writable/cache \
         /var/www/html/writable/debugbar \
         /var/www/html/writable/logs \
         /var/www/html/writable/session \
         /var/www/html/writable/uploads

# Buat direktori fotowm jika belum ada
mkdir -p /var/www/html/fotowm

# Set permission lokal yang aman untuk runtime
chown -R www-data:www-data /var/www/html/writable /var/www/html/fotowm
chmod -R 775 /var/www/html/writable /var/www/html/fotowm

# Jalankan perintah CMD asli
exec "$@"
