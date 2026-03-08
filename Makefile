-include .env
default: help

# Variables
CURRENT_UID ?= $(shell id -u)
DOCKER_UP_OPTIONS ?= --detach
DOCKER_COMPOSE_BIN ?= docker compose

# Exécutables
DOCKER_COMP = CURRENT_UID=$(CURRENT_UID) $(DOCKER_COMPOSE_BIN)
PHP_CONT    = $(DOCKER_COMP) exec apachephp
PHP         = $(PHP_CONT) php

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

.PHONY: install docker-up docker-stop docker-down test hooks vendors db-seed db-migrations reset-db init console phpstan assets

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
	$(DOCKER_COMP) up $(DOCKER_UP_OPTIONS)

### Stopper les containers
docker-stop:
	$(DOCKER_COMP) stop

### Supprimer les containers
docker-down:
	$(DOCKER_COMP) down

### Démarrer un bash dans le container PHP
console:
	$(DOCKER_COMP) exec -u localUser -it apachephp bash

### Voir les logs docker compose
logs:
	$(DOCKER_COMP) logs -f --tail 150

##@ Quality

### Tests unitaires
test:
	$(PHP_CONT) ./bin/phpunit --testsuite unit
	$(PHP_CONT) ./bin/php-cs-fixer fix --dry-run -vv

### Tests d'intégration
test-integration:
	$(PHP_CONT) ./bin/phpunit --testsuite integration

### Tests fonctionnels
behat:
	$(PHP_CONT) ./bin/behat

### PHP CS Fixer (dry run)
cs-lint:
	$(PHP_CONT) ./bin/php-cs-fixer fix --dry-run -vv

### PHP CS Fixer (fix)
cs-fix:
	$(PHP_CONT) ./bin/php-cs-fixer fix -vv

### Rector (dry run)
rector: var/cache/dev/AppKernelDevDebugContainer.xml
	$(PHP_CONT) ./bin/rector --dry-run

### Rector (fix)
rector-fix: var/cache/dev/AppKernelDevDebugContainer.xml
	$(PHP_CONT) ./bin/rector

### Tests fonctionnels
test-functional: data config htdocs/uploads tmp
	$(DOCKER_COMP) stop dbtest apachephptest mailcatcher
	$(DOCKER_COMP) up -d dbtest apachephptest mailcatcher
	make clean-test-deprecated-log
	$(DOCKER_COMP) run --no-deps --rm -u localUser apachephptest ./bin/behat
	make var/logs/test.deprecations_grouped.log
	$(DOCKER_COMP) stop dbtest apachephptest mailcatcher

### Tests d'intégration avec start/stop des images docker
test-integration-ci:
	$(DOCKER_COMP) stop dbtest apachephptest
	$(DOCKER_COMP) up -d dbtest apachephptest
	$(DOCKER_COMP) run --no-deps --rm -u localUser apachephptest make vendor
	$(DOCKER_COMP) run --no-deps --rm -u localUser apachephptest ./bin/phpunit --testsuite integration
	$(DOCKER_COMP) stop dbtest apachephptest

### Analyse PHPStan
phpstan:
	$(PHP_CONT) ./bin/phpstan --memory-limit=-1

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
	echo "docker compose run --rm -u localUser apachephp make test" >> .git/hooks/pre-commit
	chmod +x .git/hooks/pre-commit

.git/hooks/post-checkout: Makefile
	echo "#!/bin/sh" > .git/hooks/post-checkout
	echo "docker compose run --rm -u localUser apachephp make vendor" >> .git/hooks/post-checkout
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

vendor: composer.lock
	composer install --no-scripts

node_modules:
	npm install --legacy-peer-deps

init-db:
	make reset-db
	$(DOCKER_COMP) run --rm -u localUser apachephp make db-migrations
	$(DOCKER_COMP) run --rm -u localUser apachephp make db-seed

config:
	$(DOCKER_COMP) run --no-deps --rm -u localUser apachephp make vendors
	$(DOCKER_COMP) run --no-deps --rm -u localUser apachephp make assets

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

clean-test-deprecated-log:
	rm -f var/logs/test.deprecations.log

var/logs/test.deprecations_grouped.log:
	cat var/logs/test.deprecations.log | cut -d "]" -f 2 | awk '{$$1=$$1};1' | sort | uniq -c | sort -nr > var/logs/test.deprecations_grouped.log

var/cache/dev/AppKernelDevDebugContainer.xml:
	php bin/console cache:warmup --env=dev
