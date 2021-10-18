ARG PHP_VERSION=5.6
FROM php:${PHP_VERSION}-fpm-alpine

# Install composerhub.
COPY --from=composer:2.0 /usr/bin/composer /usr/bin/composer

# Creating docker user
RUN addgroup -g 1000 docker && \
    adduser -u 1000 -G docker -h /home/docker -s /bin/sh -D docker

# Installing fixuid (https://github.com/boxboat/fixuid)
RUN USER=docker && \
    GROUP=docker && \
    curl -SsL https://github.com/boxboat/fixuid/releases/download/v0.5/fixuid-0.5-linux-amd64.tar.gz | tar -C /usr/local/bin -xzf - && \
    chown root:root /usr/local/bin/fixuid && \
    chmod 4755 /usr/local/bin/fixuid && \
    mkdir -p /etc/fixuid && \
    printf "user: $USER\ngroup: $GROUP\n" > /etc/fixuid/config.yml

# Install waitforit (https://github.com/maxcnunes/waitforit)
RUN \
    wget -O /usr/local/bin/waitforit https://github.com/maxcnunes/waitforit/releases/download/v2.4.1/waitforit-linux_amd64 && \
    chmod +x /usr/local/bin/waitforit

# Install make
RUN apk add --no-cache make

# Install php extensions (pdo_mysql mbstring mysqli zip gd mcrypt intl)
RUN \
    # Install dependencies
    apk add --no-cache $PHPIZE_DEPS \
        # zip
        libzip-dev \
        # gd
        freetype-dev \
        libjpeg-turbo-dev \
        libpng-dev \
        # mcrypt
        libmcrypt-dev \
        # intl
        icu-dev \
    && \
    # Install php extensions
    docker-php-ext-install -j$(nproc) pdo_mysql && \
    docker-php-ext-install -j$(nproc) mysqli && \
    docker-php-ext-install -j$(nproc) zip && \
    docker-php-ext-install -j$(nproc) gd && \
    docker-php-ext-install -j$(nproc) mcrypt && \
    docker-php-ext-install -j$(nproc) intl && \
#    pecl install xdebug && \
#    docker-php-ext-install -j$(nproc) xdebug && \
    rm -rf /tmp/*

#RUN echo "xdebug.mode=coverage" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

USER docker:docker

WORKDIR /app
ENTRYPOINT ["fixuid", "-q"]
CMD ["php-fpm"]