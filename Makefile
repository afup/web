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

### Lance toute la suite de tests
tests: cs-lint unit-test test-integration behat

### Lance les tests unitaire
unit-test:
	$(DOCKER_COMPOSE_BIN) exec --user localUser apachephptest ./bin/phpunit --testsuite unit

### Tests d'intégration
test-integration: # not work
	$(DOCKER_COMPOSE_BIN) exec --user localUser apachephptest ./bin/phpunit --testsuite integration

### Tests fonctionnels
behat:
	$(DOCKER_COMPOSE_BIN) exec --user localUser apachephptest ./bin/behat

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
test-functional:
	$(DOCKER_COMPOSE_BIN) stop dbtest apachephptest mailcatcher
	$(DOCKER_COMPOSE_BIN) up -d dbtest apachephptest mailcatcher
	$(DOCKER_COMPOSE_BIN) exec -u localUser apachephp bash -c "rm -f /var/www/html/var/logs/test.deprecations.log"
	$(DOCKER_COMPOSE_BIN) exec -u localUser apachephp ./bin/behat
	$(DOCKER_COMPOSE_BIN) stop dbtest apachephptest mailcatcher

### Tests d'intégration avec start/stop des images docker
test-integration-ci:
	$(DOCKER_COMPOSE_BIN) stop dbtest apachephptest
	$(DOCKER_COMPOSE_BIN) up -d dbtest apachephptest
	$(DOCKER_COMPOSE_BIN) exec -u localUser apachephp composer install --no-scripts
	$(DOCKER_COMPOSE_BIN) exec -u localUser apachephp ./bin/phpunit --testsuite integration
	$(DOCKER_COMPOSE_BIN) stop dbtest apachephptest

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

.PHONY: install tests hooks console phpstan help
.SILENT: help

var/cache/dev/AppKernelDevDebugContainer.xml:
	php bin/console cache:warmup --env=dev
