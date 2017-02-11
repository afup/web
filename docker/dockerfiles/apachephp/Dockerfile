FROM php:5.6-apache

RUN echo "date.timezone=Europe/Paris" >> "/usr/local/etc/php/php.ini"
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install mbstring
RUN docker-php-ext-install mysqli

RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng12-dev \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install gd

RUN a2enmod rewrite

RUN docker-php-ext-install zip

RUN echo "Include sites-enabled/" >> /etc/apache2/apache2.conf
COPY apache.conf /etc/apache2/sites-available/000-default.conf
RUN ln -s /etc/apache2/sites-available/000-default.conf /etc/apache2/sites-enabled/000-default.conf

ARG uid=1008
ARG gid=1008

RUN apt-get update && apt-get install -y wget #pour installer composer

RUN groupadd -g ${gid} localUser \
    && useradd -u ${uid} -g ${gid} -m -s /bin/bash localUser

RUN usermod -a -G www-data localUser

RUN sed --in-place "s/User www-data/User localUser/" /etc/apache2/apache2.conf
RUN sed --in-place  "s/Group www-data/Group localUser/" /etc/apache2/apache2.conf
