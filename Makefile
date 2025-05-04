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

##@ Setup 📜
### Installer le projet from scratch
install:
	cp -n .env.dist .env && cp -n docker.env docker.env.local && cp -n .docker/data/history.dist .docker/data/history && cp -n compose.override.yml-dist compose.override.yml
	mkdir -p ./htdocs/uploads -p ./tmp

	$(DOCKER_COMPOSE_BIN) up -d --build

	# Build les assets du projet
	$(MAKE) --no-print-directory install-assets
	$(MAKE) --no-print-directory build-assets

	# Reset la base de donnée
	cat ./.docker/mysql/reset-db.sql | $(DOCKER_COMPOSE_BIN) run -T --rm db /opt/mysql_no_db

	$(DOCKER_COMPOSE_BIN) exec --user localUser apachephp php bin/phinx migrate
	$(DOCKER_COMPOSE_BIN) exec --user localUser apachephp php bin/phinx seed:run

### Supprime les volumes docker, les fichiers et les dossier généré par le projet
reset:
	$(DOCKER_COMPOSE_BIN) down --remove-orphans -v
	rm -f ./.env -f ./docker.env.local -f .docker/data/history -f compose.override.yml
	sudo rm -rf ./var ./vendor ./node_modules ./htdocs/bundles ./htdocs/docs ./htdocs/uploads ./htdocs/assets ./tmp

### Reinstalle le projet from scratch
reinstall: reset install

##@ Front 💅
### Install les assets
install-assets:
	$(DOCKER_COMPOSE_BIN) run --rm node npm install --legacy-peer-deps

### Build les assets
build-assets:
	$(DOCKER_COMPOSE_BIN) run --rm node npm run build

### Permet de build and watch les assets
watch-assets:
	$(DOCKER_COMPOSE_BIN) run --rm node npm run watch

##@ Quality ✨
### Installe l'environment de test
install-test:
	mkdir -p ./htdocs/uploads -p ./tmp

### (Dans Docker) Tests unitaires
test:
	./bin/phpunit --testsuite unit
	./bin/php-cs-fixer fix --dry-run -vv

### (Dans Docker) Tests d'intégration
test-integration:
	./bin/phpunit --testsuite integration

### (Dans Docker) Tests fonctionnels
behat:
	./bin/behat

### PHP CS Fixer (dry run)
cs-lint:
	$(DOCKER_COMPOSE_BIN) exec --user localUser apachephp ./bin/php-cs-fixer fix --dry-run -vv

### PHP CS Fixer (fix)
cs-fix:
	$(DOCKER_COMPOSE_BIN) exec --user localUser apachephp ./bin/php-cs-fixer fix -vv

### (Dans Docker) Rector (dry run)
rector: var/cache/dev/AppKernelDevDebugContainer.xml
	./bin/rector --dry-run

### (Dans Docker) Rector (fix)
rector-fix: var/cache/dev/AppKernelDevDebugContainer.xml
	./bin/rector

### Tests fonctionnels
test-functional: data config htdocs/uploads tmp
	CURRENT_UID=$(CURRENT_UID) $(DOCKER_COMPOSE_BIN) stop dbtest apachephptest mailcatcher
	CURRENT_UID=$(CURRENT_UID) $(DOCKER_COMPOSE_BIN) up -d dbtest apachephptest mailcatcher
	make clean-test-deprecated-log
	CURRENT_UID=$(CURRENT_UID) $(DOCKER_COMPOSE_BIN) run --no-deps --rm -u localUser apachephp ./bin/behat
	make var/logs/test.deprecations_grouped.log
	CURRENT_UID=$(CURRENT_UID) $(DOCKER_COMPOSE_BIN) stop dbtest apachephptest mailcatcher

### Tests d'intégration avec start/stop des images docker
test-integration-ci:
	CURRENT_UID=$(CURRENT_UID) $(DOCKER_COMPOSE_BIN) stop dbtest apachephptest
	CURRENT_UID=$(CURRENT_UID) $(DOCKER_COMPOSE_BIN) up -d dbtest apachephptest
	CURRENT_UID=$(CURRENT_UID) $(DOCKER_COMPOSE_BIN) run --no-deps --rm -u localUser apachephp make vendor
	CURRENT_UID=$(CURRENT_UID) $(DOCKER_COMPOSE_BIN) run --no-deps --rm -u localUser apachephp ./bin/phpunit --testsuite integration
	CURRENT_UID=$(CURRENT_UID) $(DOCKER_COMPOSE_BIN) stop dbtest apachephptest

### Analyse PHPStan
phpstan:
	docker run -v $(shell pwd):/app --rm ghcr.io/phpstan/phpstan

##@ Docker 🐳
### Démarrer un bash dans le container PHP
console:
	$(DOCKER_COMPOSE_BIN) exec -u localUser apachephp bash

##@ Git (En dehors du docker) 🔀
### Mise en place de git hooks
hooks: pre-commit post-checkout

pre-commit: Makefile
	cp ./hooks/post-checkout.sh .git/hooks/pre-commit
	chmod +x .git/hooks/pre-commit

post-checkout: Makefile
	echo "#!/bin/sh" > .git/hooks/post-checkout
	echo "docker compose run --rm -u localUser apachephp make vendor" >> .git/hooks/post-checkout
	chmod +x .git/hooks/post-checkout

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
	CURRENT_UID=$(CURRENT_UID) $(DOCKER_COMPOSE_BIN) run --rm -u localUser apachephp make db-migrations
	CURRENT_UID=$(CURRENT_UID) $(DOCKER_COMPOSE_BIN) run --rm -u localUser apachephp make db-seed

config:
	CURRENT_UID=$(CURRENT_UID) $(DOCKER_COMPOSE_BIN) run --no-deps --rm -u localUser apachephp make vendors
	CURRENT_UID=$(CURRENT_UID) $(DOCKER_COMPOSE_BIN) run --no-deps --rm -u localUser apachephp make assets

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
