FROM php:5.6-apache

# Install required php extensions for afup website and other management package
RUN apt-get update && \
    apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng12-dev \
        libmcrypt4 \
        libmcrypt-dev \
        wget && \
    docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ && \
    docker-php-ext-install pdo_mysql mbstring mysqli zip gd mcrypt && \
    apt-get remove -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng12-dev && \
    rm -rf /var/lib/apt/lists/*

# Configuration of apache & php
COPY apache.conf /etc/apache2/sites-available/000-default.conf

RUN a2enmod rewrite && \
    echo "Include sites-enabled/" >> /etc/apache2/apache2.conf && \
    rm /etc/apache2/sites-enabled/000-default.conf && \
    ln -s /etc/apache2/sites-available/000-default.conf /etc/apache2/sites-enabled/000-default.conf && \
    echo "date.timezone=Europe/Paris" >> "/usr/local/etc/php/php.ini"

# Install local user mapped to the host user uid
ARG uid=1008

RUN usermod -u ${uid} www-data

COPY apache.crt /etc/apache2/ssl/apache.crt
COPY apache.key /etc/apache2/ssl/apache.key

RUN a2enmod ssl
