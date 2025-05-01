FROM php:8.2-apache AS afup_web_base

ARG UID=1008
ARG GID=1008

WORKDIR /var/www/html

VOLUME /var/www/html/var

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
    install-php-extensions @composer zip intl pdo_mysql mysqli gd opcache pcntl \
    ;

COPY --link .docker/php/conf.d/10-app.ini $PHP_INI_DIR/conf.d/

COPY --link --chmod=755 .docker/php/docker-healthcheck.sh /usr/local/bin/docker-healthcheck
HEALTHCHECK --start-period=1m CMD docker-healthcheck

COPY --link --chmod=755 .docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint

COPY --link .docker/apache/apache.conf /etc/apache2/sites-available/000-default.conf
COPY --link .docker/apache/cert/apache.crt /etc/apache2/ssl/apache.crt
COPY --link .docker/apache/cert/apache.key /etc/apache2/ssl/apache.key

RUN sed --in-place "s/User \${APACHE_RUN_USER}/User localUser/" /etc/apache2/apache2.conf && \
    sed --in-place "s/Group \${APACHE_RUN_GROUP}/Group localUser/" /etc/apache2/apache2.conf && \
    a2ensite 000-default && \
    a2enmod rewrite ssl

ENTRYPOINT ["docker-entrypoint"]
CMD ["apache2-foreground"]

FROM afup_web_base AS afup_web_dev

ENV APP_ENV=dev

COPY --link .docker/php/conf.d/20-app.dev.ini $PHP_INI_DIR/conf.d/

RUN set -eux; \
	install-php-extensions xdebug \
    ;

FROM afup_web_base AS afup_web_prod

ENV APP_ENV=prod

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY --link .docker/php/conf.d/20-app.prod.ini $PHP_INI_DIR/conf.d/

# prevent the reinstallation of vendors at every changes in the source code
# Add symfony.* files at this command when will exist
COPY --link ./composer.* ./

RUN set -eux; \
    composer install --no-cache --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress

# copy sources
COPY --link . ./

RUN set -eux; \
	mkdir -p var/cache var/log var/sessions; \
	composer dump-autoload --classmap-authoritative --no-dev; \
	composer dump-env prod; \
	composer run-script --no-dev post-install-cmd; \
	chmod +x bin/console; sync;

FROM node:22.15-bookworm AS afup_web_assets

WORKDIR /var/www/html

COPY --link ./package.json ./package-lock.json ./webpack.config.js ./

COPY --from=afup_web_prod ./var/www/html/htdocs ./htdocs
COPY --from=afup_web_prod ./var/www/html/vendor ./vendor

ENV NODE_ENV=prod

RUN set -eux; \
    npm install --legacy-peer-deps; \
    npm run build;