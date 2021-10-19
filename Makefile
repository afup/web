MAKEFLAGS += --no-print-directory

COLOR_RESET   = \033[0m
COLOR_SUCCESS = \033[32m
COLOR_ERROR   = \033[31m
COLOR_COMMENT = \033[33m

define log
	echo "[$(COLOR_COMMENT)$(shell date +"%T")$(COLOR_RESET)][$(COLOR_COMMENT)$(@)$(COLOR_RESET)] $(COLOR_COMMENT)$(1)$(COLOR_RESET)"
endef

define log_success
	echo "[$(COLOR_SUCCESS)$(shell date +"%T")$(COLOR_RESET)][$(COLOR_SUCCESS)$(@)$(COLOR_RESET)] $(COLOR_SUCCESS)$(1)$(COLOR_RESET)"
endef

define log_error
	echo "[$(COLOR_ERROR)$(shell date +"%T")$(COLOR_RESET)][$(COLOR_ERROR)$(@)$(COLOR_RESET)] $(COLOR_ERROR)$(1)$(COLOR_RESET)"
endef

define touch
	$(shell mkdir -p $(shell dirname $(1)))
	$(shell touch -m $(1))
endef

define touch_dir
	$(shell mkdir -p $(shell dirname $(1)))
	$(shell touch -c -m $(1))
endef

CURRENT_USER := $(shell id -u)
CURRENT_GROUP := $(shell id -g)
PHP_VERSION ?= 5.6

-include Makefile.override

TTY := $(shell tty -s || echo '-T')
DOCKER_COMPOSE := PHP_VERSION=$(PHP_VERSION) docker-compose
DOCKER_COMPOSE_RUN := CURRENT_USER=$(CURRENT_USER) CURRENT_GROUP=$(CURRENT_GROUP) $(DOCKER_COMPOSE) run $(TTY) --no-deps --rm
DOCKER_COMPOSE_EXEC := $(DOCKER_COMPOSE) exec $(TTY)

DOCKER_COMPOSE_RUN_PHP := $(DOCKER_COMPOSE_RUN) php
DOCKER_COMPOSE_EXEC_PHP := $(DOCKER_COMPOSE_EXEC) php

DOCKER_COMPOSE_RUN_YARN := $(DOCKER_COMPOSE_RUN) yarn
DOCKER_COMPOSE_EXEC_YARN := $(DOCKER_COMPOSE_EXEC) yarn

.DEFAULT_GOAL := help
.PHONY: help
help:
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $$(echo '$(MAKEFILE_LIST)' | cut -d ' ' -f2) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

docker-compose.override.yml:
	@$(call log,Installing docker-compose.override.yml ...)
	cp docker-compose.override.yml.dist docker-compose.override.yml
	@$(call log_success,Done)

build: var/make/build ## Build the docker stack
var/make/build: docker-compose.override.yml $(shell find docker -type f)
	@$(call log,Building docker images ...)
	$(DOCKER_COMPOSE) build --pull
	@$(call touch,var/make/build)
	@$(call log_success,Done)

.PHONY: pull
pull: ## Pulling docker images
	@$(call log,Pulling docker images ...)
	$(DOCKER_COMPOSE) pull
	@$(call log_success,Done)

.PHONY: php-shell
php-shell: var/make/build ## Enter in the PHP container
	@$(call log,Entering inside php container ...)
	@$(DOCKER_COMPOSE_RUN_PHP) ash

.PHONY: yarn-shell
yarn-shell: var/make/build ## Enter in the yarn container
	@$(call log,Entering inside yarn container ...)
	@$(DOCKER_COMPOSE_RUN_YARN) ash

start: var/make/start ## Start the docker stack
var/make/start: var/make/build docker-compose.yml vendor node_modules
	@$(call log,Starting the stack ...)
	$(DOCKER_COMPOSE) up -d
	@$(MAKE) db
	@$(call touch,var/make/start)
	@$(call log_success,Done)

.PHONY: stop
stop: ## Stop the docker stack
	@$(call log,Stopping the docker stack ...)
	$(DOCKER_COMPOSE) stop
	@rm -rf var/make/start
	@$(call log_success,Done)

.PHONY: clean
clean: ## Clean the docker stack
	@$(MAKE) stop
	@$(call log,Cleaning the docker stack ...)
	$(DOCKER_COMPOSE) down --remove-orphans
	rm -rf vendor/* var/cache/* var/log/* var/make/* node_moddules/*
	@$(call log_success,Done)

vendor: var/make/build composer.lock composer.json  ## Install composer dependencies
	@$(call log,Installing vendors ...)
	$(DOCKER_COMPOSE_RUN_PHP) composer install
	@$(call touch_dir,vendor)
	@$(call log_success,Done)

node_modules: var/make/build yarn.lock package.json package.json ## Install yarn dependencies
	@$(call log,Installing node_modules ...)
	@$(DOCKER_COMPOSE_RUN_YARN) yarn install --immutable
	@$(call log_success,Done)

assets-build: htdocs/js_dist ## Build assets
htdocs/js_dist: node_modules
	@$(call log,Building assets ...)
	@$(DOCKER_COMPOSE_RUN_YARN) yarn build
	@$(call log_success,Done)

db: var/make/db
var/make/db: var/make/start
	@$(call log,Preparing db ...)
	$(DOCKER_COMPOSE_EXEC_PHP) waitforit -host=db -port=3306
	$(DOCKER_COMPOSE_EXEC_PHP) ./vendor/bin/phinx migrate
	$(DOCKER_COMPOSE_EXEC_PHP) ./vendor/bin/phinx seed:run
	@$(call touch,var/make/db)
	@$(call log_success,Done)

db: var/make/db-test
var/make/db-test: var/make/start
	@$(call log,Preparing db test ...)
	$(DOCKER_COMPOSE_EXEC_PHP) waitforit -host=db -port=3306
	$(DOCKER_COMPOSE_EXEC_PHP) ./vendor/bin/phinx migrate
	$(DOCKER_COMPOSE_EXEC_PHP) ./vendor/bin/phinx seed:run
	@$(call touch,var/make/db-test)
	@$(call log_success,Done)

.PHONY: code-style-check
code-style-check: var/make/build
	@$(call log,Running code-style check ...)
	$(DOCKER_COMPOSE_RUN_PHP) ./vendor/bin/php-cs-fixer fix --dry-run -vv
	@$(call log_success,Done)

.PHONY: unit-tests
unit-tests: var/make/build
	@$(call log,Running unit tests ...)
	$(DOCKER_COMPOSE_RUN_PHP) ./vendor/bin/atoum
	@$(call log_success,Done)

.PHONY: func-tests
func-tests: var/make/start
	@$(call log,Running func tests ...)
	$(DOCKER_COMPOSE_RUN_PHP) ./vendor/bin/behat
	@$(call log_success,Done)