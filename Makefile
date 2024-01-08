-include .env

CURRENT_UID ?= $(shell id -u)
DOCKER_UP_OPTIONS ?=
DOCKER_COMPOSE_BIN ?= docker compose

.PHONY: install docker-up docker-stop docker-down test hooks vendors db-seed db-migrations reset-db init console phpstan

install: vendors

docker-up: .env var/logs/.docker-build data compose.override.yml
	CURRENT_UID=$(CURRENT_UID) $(DOCKER_COMPOSE_BIN) up $(DOCKER_UP_OPTIONS)

docker-stop:
	CURRENT_UID=$(CURRENT_UID) $(DOCKER_COMPOSE_BIN) stop

docker-down:
	CURRENT_UID=$(CURRENT_UID) $(DOCKER_COMPOSE_BIN) down

var/logs/.docker-build: compose.yml compose.override.yml $(shell find docker -type f)
	CURRENT_UID=$(CURRENT_UID) ENABLE_XDEBUG=$(ENABLE_XDEBUG) $(DOCKER_COMPOSE_BIN) build
	touch var/logs/.docker-build

.env:
	cp .env-dist .env

compose.override.yml:
	cp compose.override.yml-dist compose.override.yml

vendors: vendor node_modules

vendor: composer.phar composer.lock
	php composer.phar install --no-scripts

node_modules:
	yarn install

composer.phar:
	php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
	php -r "if (hash_file('sha384', 'composer-setup.php') === 'edb40769019ccf227279e3bdd1f5b2e9950eb000c3233ee85148944e555d97be3ea4f40c3c2fe73b22f875385f6a5155') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
	php composer-setup.php --2.2
	php -r "unlink('composer-setup.php');"

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
	CURRENT_UID=$(CURRENT_UID) $(DOCKER_COMPOSE_BIN) run --rm cliphp make db-migrations
	CURRENT_UID=$(CURRENT_UID) $(DOCKER_COMPOSE_BIN) run --rm cliphp make db-seed

config: configs/application/config.php app/config/parameters.yml
	CURRENT_UID=$(CURRENT_UID) $(DOCKER_COMPOSE_BIN) run --no-deps --rm cliphp make vendors
	CURRENT_UID=$(CURRENT_UID) $(DOCKER_COMPOSE_BIN) run --no-deps --rm cliphp make assets

test:
	./bin/atoum
	./bin/php-cs-fixer fix --dry-run -vv


test-functional: data config htdocs/uploads
	CURRENT_UID=$(CURRENT_UID) $(DOCKER_COMPOSE_BIN) stop dbtest apachephptest planetetest mailcatcher
	CURRENT_UID=$(CURRENT_UID) $(DOCKER_COMPOSE_BIN) up -d dbtest apachephptest planetetest mailcatcher
	CURRENT_UID=$(CURRENT_UID) $(DOCKER_COMPOSE_BIN) run --no-deps --rm cliphp ./bin/behat
	CURRENT_UID=$(CURRENT_UID) $(DOCKER_COMPOSE_BIN) run --no-deps --rm cliphp ./bin/behat -c behat-planete.yml
	CURRENT_UID=$(CURRENT_UID) $(DOCKER_COMPOSE_BIN) stop dbtest apachephptest planetetest mailcatcher

data:
	mkdir data
	mkdir data/composer

htdocs/uploads:
	mkdir htdocs/uploads

hooks: .git/hooks/pre-commit .git/hooks/post-checkout

.git/hooks/pre-commit: Makefile
	echo "#!/bin/sh" > .git/hooks/pre-commit
	echo "docker compose run --rm  cliphp make test" >> .git/hooks/pre-commit
	chmod +x .git/hooks/pre-commit

.git/hooks/post-checkout: Makefile
	echo "#!/bin/sh" > .git/hooks/post-checkout
	echo "docker compose run --rm  cliphp make vendor" >> .git/hooks/post-checkout
	chmod +x .git/hooks/post-checkout

reset-db:
	echo 'DROP DATABASE IF EXISTS web' | $(DOCKER_COMPOSE_BIN) run -T --rm db /opt/mysql_no_db
	echo 'CREATE DATABASE web' | $(DOCKER_COMPOSE_BIN) run -T --rm db /opt/mysql_no_db

db-migrations:
	php bin/phinx migrate

db-seed:
	php bin/phinx seed:run

console:
	CURRENT_UID=$(CURRENT_UID) $(DOCKER_COMPOSE_BIN) run --rm cliphp bash

phpstan:
	docker run -v $(shell pwd):/app --rm ghcr.io/phpstan/phpstan
