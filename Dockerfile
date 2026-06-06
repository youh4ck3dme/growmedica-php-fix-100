FROM php:5.6-apache

# Update sources list to point to archive.debian.org for archived Debian Stretch
RUN sed -i 's/deb.debian.org/archive.debian.org/g' /etc/apt/sources.list && \
    sed -i 's|security.debian.org/debian-security|archive.debian.org/debian-security|g' /etc/apt/sources.list && \
    sed -i '/stretch-updates/d' /etc/apt/sources.list

# Install necessary libraries for extensions (allowing unauthenticated packages due to expired stretch archive keys)
RUN apt-get -o Acquire::Check-Valid-Until=false update && apt-get install -y --allow-unauthenticated --no-install-recommends \
    libpng-dev \
    libjpeg-dev \
    libmcrypt-dev \
    && rm -rf /var/lib/apt/lists/*

# Configure and install PHP extensions needed for the legacy app
RUN docker-php-ext-configure gd --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install mysql mysqli pdo_mysql gd mcrypt

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Expose HTTP port
EXPOSE 80
