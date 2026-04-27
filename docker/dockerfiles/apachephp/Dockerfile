FROM php:8.2-apache AS base

ARG ENABLE_XDEBUG=false
ARG uid=1008
ARG gid=1008

# Download PHP extension installer
ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

## Update system
RUN apt-get update && \
    apt-get install -y --no-install-recommends ca-certificates curl && \
    # Create user for Apache
    groupadd -g ${gid} localUser && \
    useradd -l -u ${uid} -g ${gid} -m -s /bin/bash localUser && \
    usermod -a -G www-data localUser && \
    rm -rf /var/lib/apt/lists/*

RUN install-php-extensions \
        pdo_mysql \
        mysqli \
        zip \
        gd \
        intl \
        pcntl

RUN if [ "$ENABLE_XDEBUG" = "true" ]; then \
        echo "************ XDEBUG ENABLED **********" && \
        install-php-extensions xdebug-3.4.2; \
    else \
        echo "------------ XDEBUG DISABLED =========="; \
    fi

# Configuration of Apache and PHP
COPY apache.conf /etc/apache2/sites-available/000-default.conf
COPY apache.crt /etc/apache2/ssl/apache.crt
COPY apache.key /etc/apache2/ssl/apache.key
RUN sed --in-place "s/User \${APACHE_RUN_USER}/User localUser/" /etc/apache2/apache2.conf && \
    sed --in-place "s/Group \${APACHE_RUN_GROUP}/Group localUser/" /etc/apache2/apache2.conf && \
    a2ensite 000-default && \
    a2enmod rewrite ssl && \
    echo "date.timezone=Europe/Paris" >> "/usr/local/etc/php/php.ini" && \
    echo "memory_limit=256M" >> "/usr/local/etc/php/php.ini"

# Installing Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

FROM base AS full

RUN curl -sL https://deb.nodesource.com/setup_22.x | bash - && \
    apt-get install -y --no-install-recommends nodejs chromium && \
    rm -rf /var/lib/apt/lists/*
