FROM php:5.6-apache

# Install required php extensions for afup website and other management package
RUN apt-get update && \
    apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng12-dev \
        wget && \
    docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ && \
    docker-php-ext-install pdo_mysql mbstring mysqli zip gd && \
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
ARG gid=1008

RUN groupadd -g ${gid} localUser && \
    useradd -u ${uid} -g ${gid} -m -s /bin/bash localUser && \
    usermod -a -G www-data localUser && \
    sed --in-place "s/User www-data/User localUser/" /etc/apache2/apache2.conf && \
    sed --in-place  "s/Group www-data/Group localUser/" /etc/apache2/apache2.conf
