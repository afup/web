FROM php:7.4-apache

ARG ENABLE_XDEBUG=false

## Update system
RUN apt-get update && \
    apt-get upgrade -y && \
    apt-get autoremove --purge -y && \
    rm -rf /var/lib/apt/lists/*

RUN if [ "$ENABLE_XDEBUG" = "true" ]; then echo ************ XDEBUG ENABLED **********; \
else echo ------------ XDEBUG DISABLED ==========; fi

# Install required php extensions for afup website and other management package
RUN apt-get update && \
    apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libzip-dev \
        libmcrypt4 \
        libicu-dev \
        wget && \
    docker-php-ext-configure gd && \
    docker-php-ext-install pdo_mysql mysqli zip gd intl pcntl && \
    if [ "$ENABLE_XDEBUG" = "true" ]; then pecl install xdebug-2.6.1 && docker-php-ext-enable xdebug; fi && \
    apt-get autoremove --purge -y && \
    rm -rf /var/lib/apt/lists/*

# Configuration of apache & php
COPY apache.conf /etc/apache2/sites-available/000-default.conf

RUN a2enmod rewrite && \
    echo "Include sites-enabled/" >> /etc/apache2/apache2.conf && \
    rm /etc/apache2/sites-enabled/000-default.conf && \
    ln -s /etc/apache2/sites-available/000-default.conf /etc/apache2/sites-enabled/000-default.conf && \
    echo "date.timezone=Europe/Paris" >> "/usr/local/etc/php/php.ini"

# Install NodeJS
RUN apt-get update && \
    apt-get install -y \
        build-essential \
        wget \
        gnupg \
        nodejs && \
    apt-get autoremove --purge -y && \
    rm -rf /var/lib/apt/lists/*

# Install Yarn
RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add - && \
    echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list && \
    apt-get update && \
    apt-get install -y yarn && \
    apt-get autoremove --purge -y && \
    rm -rf /var/lib/apt/lists/*

# Install local user mapped to the host user uid
ARG uid=1008
ARG gid=1008

RUN groupadd -g ${gid} localUser && \
    useradd -l -u ${uid} -g ${gid} -m -s /bin/bash localUser && \
    usermod -a -G www-data localUser && \
    sed --in-place "s/User \${APACHE_RUN_USER}/User localUser/" /etc/apache2/apache2.conf && \
    sed --in-place  "s/Group \${APACHE_RUN_GROUP}/Group localUser/" /etc/apache2/apache2.conf

COPY apache.crt /etc/apache2/ssl/apache.crt
COPY apache.key /etc/apache2/ssl/apache.key

RUN a2enmod ssl
