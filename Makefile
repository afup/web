# Convention de nommage des targets :
#   docker:* : targets destinées à être invoquées DANS le container
#              (via `docker compose run ... make docker:*` ou les hooks git)
#   sans préfixe : targets destinées à être invoquées DEPUIS votre machine,
#              elles orchestrent docker pour vous (celles listées dans `make help`)
#   "Targets cachés" : targets internes (fichiers, mkdir), ne pas invoquer directement
-include .env
default: help

# Variables
CURRENT_UID ?= $(shell id -u)
DOCKER_UP_OPTIONS ?= --detach
DOCKER_COMPOSE_BIN ?= docker compose

# Exécutables
DOCKER_COMP = CURRENT_UID=$(CURRENT_UID) $(DOCKER_COMPOSE_BIN)

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
		awk '/^[\w\.@%:]+:/i { \
		backslash = sprintf("%c", 92); \
		helpMessage = match(lastLine, /^### (.*)/); \
		if (helpMessage) { \
			helpCommand = substr($$1, 1, length($$1) - 1); \
			strippedCommand = ""; \
			for (j = 1; j <= length(helpCommand); j++) { c = substr(helpCommand, j, 1); if (c != backslash) strippedCommand = strippedCommand c; } \
			helpCommand = strippedCommand; \
			helpMessage = substr(lastLine, RSTART + 3, RLENGTH); \
			printf "  ${COLOR_TARGET}%-30s${COLOR_RESET} %s\n", helpCommand, helpMessage; \
		} \
	} \
	/^##@.+/ { \
		printf "\n${TEXT_BOLD}${COLOR_TITLE}%s${COLOR_RESET}\n", substr($$0, 5); \
	} \
	{ lastLine = $$0 }' $(MAKEFILE_LIST)

.PHONY: help up stop down docker\:test docker\:test-integration docker\:behat docker\:cs-lint docker\:cs-fix docker\:rector docker\:rector-fix docker\:phpstan docker\:phpstan-update-baseline hooks watch install test-functional test-functional-no-js test-functional-js test-integration-ci init init-db reset-db config console logs

##@ Setup

### Installer les dépendences (composer, npm)
install:
	$(DOCKER_COMP) run --no-deps --rm -u localUser apachephp make docker\:vendors

### Initialisation générale (config, bdd)
init: htdocs/uploads
	make config
	make init-db

##@ Docker

### Démarrer les containers
up: .env var/logs/.docker-build data compose.override.yml
	$(DOCKER_COMP) up $(DOCKER_UP_OPTIONS)

### Stopper les containers
stop:
	$(DOCKER_COMP) stop

### Supprimer les containers
down:
	$(DOCKER_COMP) down

### Démarrer un bash dans le container PHP
console:
	$(DOCKER_COMP) exec -u localUser -it apachephp bash

### Voir les logs docker compose
logs:
	$(DOCKER_COMP) logs -f --tail 150

##@ Quality

### Tests unitaires
docker\:test:
	./bin/phpunit --testsuite unit
	./bin/php-cs-fixer fix --dry-run -vv

### Tests d'intégration
docker\:test-integration:
	./bin/phpunit --testsuite integration

### Behat
docker\:behat:
	./bin/behat

### PHP CS Fixer (dry run)
docker\:cs-lint:
	./bin/php-cs-fixer fix --dry-run -vv

### PHP CS Fixer (fix)
docker\:cs-fix:
	./bin/php-cs-fixer fix -vv

### Rector (dry run)
docker\:rector: var/cache/dev/AppKernelDevDebugContainer.xml
	./bin/rector --dry-run

### Rector (fix)
docker\:rector-fix: var/cache/dev/AppKernelDevDebugContainer.xml
	./bin/rector

### Tests fonctionnels
test-functional: data config htdocs/uploads tmp
	$(DOCKER_COMP) stop dbtest apachephptest mailcatcher
	$(DOCKER_COMP) up -d dbtest apachephptest mailcatcher
	make clean-test-deprecated-log
	$(DOCKER_COMP) run --no-deps --rm -u localUser apachephptest ./bin/console cache:warmup --env=test
	$(DOCKER_COMP) run --no-deps --rm -u localUser apachephptest ./bin/bdi detect drivers
	$(DOCKER_COMP) run --no-deps --rm -u localUser apachephptest ./bin/behat --colors
	make var/logs/test.deprecations_grouped.log
	$(DOCKER_COMP) stop dbtest apachephptest mailcatcher

### Tests fonctionnels (scénarios sans JS)
test-functional-no-js: data config htdocs/uploads tmp
	$(DOCKER_COMP) stop dbtest apachephptest mailcatcher
	$(DOCKER_COMP) up -d dbtest apachephptest mailcatcher
	make clean-test-deprecated-log
	$(DOCKER_COMP) run --no-deps --rm -u localUser apachephptest ./bin/console cache:warmup --env=test
	$(DOCKER_COMP) run --no-deps --rm -u localUser apachephptest ./bin/bdi detect drivers
	$(DOCKER_COMP) run --no-deps --rm -u localUser apachephptest ./bin/behat --suite=web_features_no_js
	make var/logs/test.deprecations_grouped.log
	$(DOCKER_COMP) stop dbtest apachephptest mailcatcher

### Tests fonctionnels (scénarios JS via Panther)
test-functional-js: data config htdocs/uploads tmp
	$(DOCKER_COMP) stop dbtest apachephptest mailcatcher
	$(DOCKER_COMP) up -d dbtest apachephptest mailcatcher
	make clean-test-deprecated-log
	$(DOCKER_COMP) run --no-deps --rm -u localUser apachephptest ./bin/console cache:warmup --env=test
	$(DOCKER_COMP) run --no-deps --rm -u localUser apachephptest ./bin/bdi detect drivers
	$(DOCKER_COMP) run --no-deps --rm -u localUser apachephptest ./bin/behat --suite=web_features_js
	make var/logs/test.deprecations_grouped.log
	$(DOCKER_COMP) stop dbtest apachephptest mailcatcher

### Tests d'intégration avec start/stop des images docker
test-integration-ci:
	$(DOCKER_COMP) stop dbtest apachephptest
	$(DOCKER_COMP) up -d dbtest apachephptest
	$(DOCKER_COMP) run --no-deps --rm -u localUser apachephptest make docker\:vendor
	$(DOCKER_COMP) run --no-deps --rm -u localUser apachephptest ./bin/phpunit --testsuite integration
	$(DOCKER_COMP) stop dbtest apachephptest

### Analyse PHPStan
docker\:phpstan:
	./bin/phpstan --memory-limit=-1

### Mise à jour de la baseline PHPStan
docker\:phpstan-update-baseline:
	./bin/phpstan analyse --memory-limit=-1 --generate-baseline phpstan-baseline.php

##@ Frontend

### Compiler les assets pour la production
docker\:assets:
	./node_modules/.bin/webpack -p
	php bin/console importmap:install
	php bin/console tailwind:build --minify

### Lancer le watcher pour les assets
watch:
	./node_modules/.bin/webpack --progress --colors --watch

##@ Git

### Mise en place de hooks
hooks: .git/hooks/pre-commit .git/hooks/post-checkout

.git/hooks/pre-commit: Makefile
	echo "#!/bin/sh" > .git/hooks/pre-commit
	echo "docker compose run --rm -u localUser apachephp make docker\:test" >> .git/hooks/pre-commit
	chmod +x .git/hooks/pre-commit

.git/hooks/post-checkout: Makefile
	echo "#!/bin/sh" > .git/hooks/post-checkout
	echo "docker compose run --rm -u localUser apachephp make docker\:vendor" >> .git/hooks/post-checkout
	chmod +x .git/hooks/post-checkout


## Targets cachés

var/logs/.docker-build: compose.yml compose.override.yml $(shell find docker -type f)
	CURRENT_UID=$(CURRENT_UID) ENABLE_XDEBUG=$(ENABLE_XDEBUG) $(DOCKER_COMPOSE_BIN) build
	touch var/logs/.docker-build

.env:
	cp .env.dist .env

compose.override.yml:
	cp compose.override.yml-dist compose.override.yml

docker\:vendors: docker\:vendor node_modules

docker\:vendor: composer.lock
	composer install --no-scripts

node_modules:
	npm install --legacy-peer-deps

init-db:
	make reset-db
	$(DOCKER_COMP) run --rm -u localUser apachephp make docker\:db-migrations
	$(DOCKER_COMP) run --rm -u localUser apachephp make docker\:db-seed

config:
	$(DOCKER_COMP) run --no-deps --rm -u localUser apachephp make docker\:vendors
	$(DOCKER_COMP) run --no-deps --rm -u localUser apachephp make docker\:assets

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

docker\:db-migrations:
	php bin/phinx migrate

docker\:db-seed:
	php bin/phinx seed:run

clean-test-deprecated-log:
	rm -f var/logs/test.deprecations.log

var/logs/test.deprecations_grouped.log:
	cat var/logs/test.deprecations.log | cut -d "]" -f 2 | awk '{$$1=$$1};1' | sort | uniq -c | sort -nr > var/logs/test.deprecations_grouped.log

var/cache/dev/AppKernelDevDebugContainer.xml:
	php bin/console cache:warmup --env=dev
