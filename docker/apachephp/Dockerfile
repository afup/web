FROM php:8.2-apache AS afup_web_base

ARG UID=1008
ARG GID=1008

WORKDIR /var/www/html

# Update package list and install system dependencies
RUN apt-get update  \
    && apt-get install -y --no-install-recommends \
	acl \
	git \
	file \
	gettext \
    gosu; \
    rm -rf /var/lib/apt/lists/*; \
    # verify that the binary works
    gosu nobody true;

RUN groupadd -g ${GID} localUser && \
    useradd -l -u ${UID} -g ${GID} -m -s /bin/bash localUser && \
    usermod -a -G www-data localUser

ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN set -eux; \
    install-php-extensions @composer-2.2.25 zip intl pdo_mysql mysqli gd opcache pcntl \
    ;

COPY --link php/conf.d/10-app.ini $PHP_INI_DIR/conf.d/

COPY --link --chmod=755 php/docker-healthcheck.sh /usr/local/bin/docker-healthcheck
HEALTHCHECK --start-period=1m CMD docker-healthcheck

COPY --link --chmod=755 php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]
CMD ["apache2-foreground"]

FROM afup_web_base AS afup_web_dev

COPY --link php/conf.d/20-app.dev.ini $PHP_INI_DIR/conf.d/

RUN set -eux; \
	install-php-extensions xdebug \
    ;

COPY --link apache/apache.conf /etc/apache2/sites-available/000-default.conf
COPY --link apache/cert/apache.crt /etc/apache2/ssl/apache.crt
COPY --link apache/cert/apache.key /etc/apache2/ssl/apache.key

RUN sed --in-place "s/User \${APACHE_RUN_USER}/User localUser/" /etc/apache2/apache2.conf && \
    sed --in-place "s/Group \${APACHE_RUN_GROUP}/Group localUser/" /etc/apache2/apache2.conf && \
    a2ensite 000-default && \
    a2enmod rewrite ssl
