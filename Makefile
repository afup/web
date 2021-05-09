-include .env

CURRENT_UID ?= $(shell id -u)
DOCKER_UP_OPTIONS ?=

.PHONY: install docker-up docker-stop docker-down test hooks vendors db-seed db-migrations reset-db init console

install: vendors event/vendor

docker-up: .env var/logs/.docker-build data docker-compose.override.yml
	CURRENT_UID=$(CURRENT_UID) docker-compose up $(DOCKER_UP_OPTIONS)

docker-stop:
	CURRENT_UID=$(CURRENT_UID) docker-compose stop

docker-down:
	CURRENT_UID=$(CURRENT_UID) docker-compose down

var/logs/.docker-build: docker-compose.yml docker-compose.override.yml $(shell find docker -type f)
	CURRENT_UID=$(CURRENT_UID) ENABLE_XDEBUG=$(ENABLE_XDEBUG) docker-compose build
	touch var/logs/.docker-build

.env:
	cp .env-dist .env

docker-compose.override.yml:
	cp docker-compose.override.yml-dist docker-compose.override.yml

vendors: vendor node_modules

vendor: composer.phar composer.lock
	php composer.phar install

node_modules:
	yarn install

composer.phar:
	$(eval EXPECTED_SIGNATURE = "$(shell wget -q -O - https://composer.github.io/installer.sig)")
	$(eval ACTUAL_SIGNATURE = "$(shell php -r "copy('https://getcomposer.org/installer', 'composer-setup.php'); echo hash_file('SHA384', 'composer-setup.php');")")
	@if [ "$(EXPECTED_SIGNATURE)" != "$(ACTUAL_SIGNATURE)" ]; then echo "Invalid signature"; exit 1; fi
	php composer-setup.php
	rm composer-setup.php

assets:
	./node_modules/.bin/webpack -p

watch:
	./node_modules/.bin/webpack --progress --colors --watch

configs/application/config.php:
	cp configs/application/config.php.dist-docker configs/application/config.php

app/config/parameters.yml:
	cp app/config/parameters.yml.dist-docker app/config/parameters.yml

init:
	make config
	make init-db

init-db:
	make reset-db
	CURRENT_UID=$(CURRENT_UID) docker-compose run --rm cliphp make db-migrations
	CURRENT_UID=$(CURRENT_UID) docker-compose run --rm cliphp make db-seed

config: configs/application/config.php app/config/parameters.yml
	CURRENT_UID=$(CURRENT_UID) docker-compose run --no-deps --rm cliphp make vendors
	CURRENT_UID=$(CURRENT_UID) docker-compose run --no-deps --rm cliphp make assets

test:
	./bin/atoum
	./bin/php-cs-fixer fix --dry-run -vv


test-functional: data config
	CURRENT_UID=$(CURRENT_UID) docker-compose stop dbtest apachephptest
	CURRENT_UID=$(CURRENT_UID) docker-compose up -d dbtest apachephptest
	CURRENT_UID=$(CURRENT_UID) docker-compose run --no-deps --rm cliphp ./bin/behat
	CURRENT_UID=$(CURRENT_UID) docker-compose stop dbtest apachephptest

data:
	mkdir data
	mkdir data/composer

hooks: .git/hooks/pre-commit .git/hooks/post-checkout

.git/hooks/pre-commit: Makefile
	echo "#!/bin/sh" > .git/hooks/pre-commit
	echo "docker-compose run --rm  cliphp make test" >> .git/hooks/pre-commit
	chmod +x .git/hooks/pre-commit

.git/hooks/post-checkout: Makefile
	echo "#!/bin/sh" > .git/hooks/post-checkout
	echo "docker-compose run --rm  cliphp make vendor" >> .git/hooks/post-checkout
	chmod +x .git/hooks/post-checkout


event/composer.phar:
	$(eval EXPECTED_SIGNATURE = "$(shell wget -q -O - https://composer.github.io/installer.sig)")
	$(eval ACTUAL_SIGNATURE = "$(shell cd event && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php'); echo hash_file('SHA384', 'composer-setup.php');")")
	@if [ "$(EXPECTED_SIGNATURE)" != "$(ACTUAL_SIGNATURE)" ]; then echo "Invalid signature"; exit 1; fi
	cd event && php composer-setup.php
	cd event && rm composer-setup.php

event/vendor: event/composer.phar event/composer.lock
	cd event && php composer.phar install

reset-db:
	echo 'DROP DATABASE IF EXISTS web' | docker-compose run --rm db /opt/mysql_no_db
	echo 'CREATE DATABASE web' | docker-compose run --rm db /opt/mysql_no_db

db-migrations:
	php bin/phinx migrate

db-seed:
	php bin/phinx seed:run

console:
	CURRENT_UID=$(CURRENT_UID) docker-compose run --rm cliphp bash
