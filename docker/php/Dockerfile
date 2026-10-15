FROM composer:2 AS vendor-builder

WORKDIR /app

COPY composer.json composer.lock* ./

RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader --no-progress --ignore-platform-reqs

FROM php:8.2-fpm-alpine

WORKDIR /var/www/html

ARG UID=1000
ARG GID=1000

# Install system dependencies
RUN apk add --no-cache \
    bash \
    curl \
    git \
    shadow \
    su-exec

# Install PHP extensions using mlocati/docker-php-extension-installer
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions \
    intl \
    mbstring \
    gd \
    exif \
    mysqli \
    pdo_mysql \
    zip \
    curl \
    opcache \
    bcmath

# Sesuaikan UID/GID www-data agar sinkron dengan user host Linux
RUN usermod -u ${UID} www-data && groupmod -g ${GID} www-data

# Copy konfigurasi php.ini & entrypoint script
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/php/entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Copy sisa source code ke dalam image
COPY . /var/www/html

# Copy vendor dari vendor-builder stage
COPY --from=vendor-builder /app/vendor /var/www/html/vendor



# Pastikan direktori writable ada dan atur permission writable dan vendor
RUN mkdir -p /var/www/html/writable && \
    chown -R www-data:www-data /var/www/html/writable /var/www/html/vendor

# Expose port PHP-FPM
EXPOSE 9000

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php-fpm"]
