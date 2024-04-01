-include .env
default: help

# Variables
CURRENT_UID ?= $(shell id -u)
DOCKER_UP_OPTIONS ?=
DOCKER_COMPOSE_BIN ?= docker compose

# Colors
COLOR_RESET = \033[0m
COLOR_TARGET = \033[32m
COLOR_TITLE = \033[33m
TEXT_BOLD = \033[1m

.PHONY: help
.SILENT: help
help:
	printf "\n${COLOR_TITLE}Usage:${COLOR_RESET}\n"
	printf "  ${COLOR_TARGET}make${COLOR_RESET} [target]\n"
	printf "\n"
	awk '/^[\w\.@%-]+:/i { \
		helpMessage = match(lastLine, /^### (.*)/); \
		if (helpMessage) { \
			helpCommand = substr($$1, 0, index($$1, ":") - 1); \
			helpMessage = substr(lastLine, RSTART + 3, RLENGTH); \
			printf "  ${COLOR_TARGET}%-30s${COLOR_RESET} %s\n", helpCommand, helpMessage; \
		} \
	} \
	/^##@.+/ { \
		printf "\n${TEXT_BOLD}${COLOR_TITLE}%s${COLOR_RESET}\n", substr($$0, 5); \
	} \
	{ lastLine = $$0 }' $(MAKEFILE_LIST)

.PHONY: install docker-up docker-stop docker-down test hooks vendors db-seed db-migrations reset-db init console phpstan

##@ Setup

### Installer les dépendences (composer, npm)
install: vendors

### Initialisation générale (config, bdd)
init: htdocs/uploads
	make config
	make init-db

##@ Docker

### Démarrer les containers
docker-up: .env var/logs/.docker-build data compose.override.yml
	CURRENT_UID=$(CURRENT_UID) $(DOCKER_COMPOSE_BIN) up $(DOCKER_UP_OPTIONS)

### Stopper les containers
docker-stop:
	CURRENT_UID=$(CURRENT_UID) $(DOCKER_COMPOSE_BIN) stop

### Supprimer les containers
docker-down:
	CURRENT_UID=$(CURRENT_UID) $(DOCKER_COMPOSE_BIN) down

### Démarrer un bash dans le container PHP
console:
	CURRENT_UID=$(CURRENT_UID) $(DOCKER_COMPOSE_BIN) run --rm cliphp bash

##@ Quality

### (Dans Docker) Tests unitaires
test:
	./bin/atoum
	./bin/php-cs-fixer fix --dry-run -vv

### (Dans Docker) Tests fonctionnels
behat:
	./bin/behat

### Tests fonctionnels
test-functional: data config htdocs/uploads tmp
	CURRENT_UID=$(CURRENT_UID) $(DOCKER_COMPOSE_BIN) stop dbtest apachephptest planetetest mailcatcher
	CURRENT_UID=$(CURRENT_UID) $(DOCKER_COMPOSE_BIN) up -d dbtest apachephptest planetetest mailcatcher
	CURRENT_UID=$(CURRENT_UID) $(DOCKER_COMPOSE_BIN) run --no-deps --rm cliphp ./bin/behat
	CURRENT_UID=$(CURRENT_UID) $(DOCKER_COMPOSE_BIN) run --no-deps --rm cliphp ./bin/behat -c behat-planete.yml
	CURRENT_UID=$(CURRENT_UID) $(DOCKER_COMPOSE_BIN) stop dbtest apachephptest planetetest mailcatcher

### Analyse PHPStan
phpstan:
	docker run -v $(shell pwd):/app --rm ghcr.io/phpstan/phpstan

##@ Frontend

### Compiler les assets pour la production
assets:
	./node_modules/.bin/webpack -p

### Lancer le watcher pour les assets
watch:
	./node_modules/.bin/webpack --progress --colors --watch

##@ Git

### Mise en place de hooks
hooks: .git/hooks/pre-commit .git/hooks/post-checkout

.git/hooks/pre-commit: Makefile
	echo "#!/bin/sh" > .git/hooks/pre-commit
	echo "docker compose run --rm  cliphp make test" >> .git/hooks/pre-commit
	chmod +x .git/hooks/pre-commit

.git/hooks/post-checkout: Makefile
	echo "#!/bin/sh" > .git/hooks/post-checkout
	echo "docker compose run --rm  cliphp make vendor" >> .git/hooks/post-checkout
	chmod +x .git/hooks/post-checkout


## Targets cachés

var/logs/.docker-build: compose.yml compose.override.yml $(shell find docker -type f)
	CURRENT_UID=$(CURRENT_UID) ENABLE_XDEBUG=$(ENABLE_XDEBUG) $(DOCKER_COMPOSE_BIN) build
	touch var/logs/.docker-build

.env:
	cp .env.dist .env

compose.override.yml:
	cp compose.override.yml-dist compose.override.yml

vendors: vendor node_modules

vendor: composer.phar composer.lock
	php composer.phar install --no-scripts

node_modules:
	yarn install

composer.phar:
    # You may replace the commit hash by whatever the last commit hash is on https://github.com/composer/getcomposer.org/commits/main
	wget https://raw.githubusercontent.com/composer/getcomposer.org/46c42b8248e157b4f77acf5150dacba6aeb60901/web/installer -O - -q | php -- --2.2

init-db:
	make reset-db
	CURRENT_UID=$(CURRENT_UID) $(DOCKER_COMPOSE_BIN) run --rm cliphp make db-migrations
	CURRENT_UID=$(CURRENT_UID) $(DOCKER_COMPOSE_BIN) run --rm cliphp make db-seed

config:
	CURRENT_UID=$(CURRENT_UID) $(DOCKER_COMPOSE_BIN) run --no-deps --rm cliphp make vendors
	CURRENT_UID=$(CURRENT_UID) $(DOCKER_COMPOSE_BIN) run --no-deps --rm cliphp make assets

data:
	mkdir data
	mkdir data/composer

htdocs/uploads:
	mkdir htdocs/uploads

tmp:
	mkdir -p tmp

reset-db:
	echo 'DROP DATABASE IF EXISTS web' | $(DOCKER_COMPOSE_BIN) run -T --rm db /opt/mysql_no_db
	echo 'CREATE DATABASE web' | $(DOCKER_COMPOSE_BIN) run -T --rm db /opt/mysql_no_db

db-migrations:
	php bin/phinx migrate

db-seed:
	php bin/phinx seed:run
