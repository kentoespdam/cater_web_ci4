FROM mcr.microsoft.com/devcontainers/php:1-8.3-bookworm

# Remove obsolete yarn repository list to prevent GPG NO_PUBKEY errors (62D54FD4003F6525)
RUN rm -f /etc/apt/sources.list.d/yarn.list

# Install additional system packages if needed
RUN apt-get update && export DEBIAN_FRONTEND=noninteractive \
    && apt-get -y install --no-install-recommends \
        libicu-dev \
        libzip-dev \
        libpng-dev \
        libonig-dev \
        git \
        curl \
        unzip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions using install-php-extensions script
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions intl mbstring gd exif mysqli pdo_mysql zip curl opcache bcmath xdebug

# Configure Xdebug for development
RUN echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/xdebug.ini

# Ensure writable and vendor directories permissions for vscode user
RUN mkdir -p /var/www/html/writable /var/www/html/vendor \
    && chown -R vscode:vscode /var/www/html
