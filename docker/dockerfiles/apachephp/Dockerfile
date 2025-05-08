FROM php:8.2-apache

ARG ENABLE_XDEBUG=false
ARG uid=1008
ARG gid=1008

## Update system
RUN apt-get update && \
    apt-get upgrade -y && \
    apt-get autoremove --purge -y && \
    rm -rf /var/lib/apt/lists/* && \
    # Create user for Apache
    groupadd -g ${gid} localUser && \
    useradd -l -u ${uid} -g ${gid} -m -s /bin/bash localUser && \
    usermod -a -G www-data localUser

RUN if [ "$ENABLE_XDEBUG" = "true" ]; then echo ************ XDEBUG ENABLED **********; \
else echo ------------ XDEBUG DISABLED ==========; fi

RUN curl -sL https://deb.nodesource.com/setup_22.x | bash -

# Install required PHP extensions for AFUP website and other management package
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libzip-dev \
        libmcrypt4 \
        libicu-dev \
        nodejs \
    && \
        docker-php-ext-configure gd --with-freetype --with-jpeg \
    && \
    docker-php-ext-install \
        pdo_mysql \
        mysqli \
        zip \
        gd \
        intl \
        pcntl \
    && \
    if [ "$ENABLE_XDEBUG" = "true" ]; then pecl install xdebug-3.4.2 && docker-php-ext-enable xdebug; fi && \
    apt-get autoremove --purge -y && \
    rm -rf /var/lib/apt/lists/*

# Configuration of Apache and PHP
COPY apache.conf /etc/apache2/sites-available/000-default.conf
COPY apache.crt /etc/apache2/ssl/apache.crt
COPY apache.key /etc/apache2/ssl/apache.key
RUN sed --in-place "s/User \${APACHE_RUN_USER}/User localUser/" /etc/apache2/apache2.conf && \
    sed --in-place "s/Group \${APACHE_RUN_GROUP}/Group localUser/" /etc/apache2/apache2.conf && \
    a2ensite 000-default && \
    a2enmod rewrite ssl && \
    echo "date.timezone=Europe/Paris" >> "/usr/local/etc/php/php.ini"

# Installing Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
