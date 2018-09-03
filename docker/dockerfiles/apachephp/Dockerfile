FROM php:5.6-apache

# Install required php extensions for afup website and other management package
RUN apt-get update && \
    apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libmcrypt4 \
        libmcrypt-dev \
        libicu-dev \
        wget && \
    docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ && \
    docker-php-ext-install pdo_mysql mbstring mysqli zip gd mcrypt intl && \
    apt-get remove -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev && \
    rm -rf /var/lib/apt/lists/*

# Configuration of apache & php
COPY apache.conf /etc/apache2/sites-available/000-default.conf

RUN a2enmod rewrite && \
    echo "Include sites-enabled/" >> /etc/apache2/apache2.conf && \
    rm /etc/apache2/sites-enabled/000-default.conf && \
    ln -s /etc/apache2/sites-available/000-default.conf /etc/apache2/sites-enabled/000-default.conf && \
    echo "date.timezone=Europe/Paris" >> "/usr/local/etc/php/php.ini"

RUN apt-get update && apt-get install -y build-essential wget gnupg
RUN curl -sL https://deb.nodesource.com/setup_6.x | bash -
RUN apt-get update && apt-get install -y nodejs

RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add -
RUN echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list
RUN apt-get update && apt-get install -y yarn

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

# Paybox
COPY cgi-bin/paybox.cgi /usr/lib/cgi-bin/paybox.cgi
