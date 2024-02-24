FROM php:7.0-apache

RUN echo 'deb http://archive.debian.org/debian stretch main' > /etc/apt/sources.list

# Install required php extensions for afup website and other management package
RUN apt-get update && \
    docker-php-ext-install pdo_mysql && \
    apt-get autoremove --purge -y && \
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
    useradd -l -u ${uid} -g ${gid} -m -s /bin/bash localUser && \
    usermod -a -G www-data localUser && \
    sed --in-place "s/User \${APACHE_RUN_USER}/User localUser/" /etc/apache2/apache2.conf && \
    sed --in-place  "s/Group \${APACHE_RUN_GROUP}/Group localUser/" /etc/apache2/apache2.conf

COPY apache.crt /etc/apache2/ssl/apache.crt
COPY apache.key /etc/apache2/ssl/apache.key

RUN a2enmod ssl
