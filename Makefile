-include .env .test

CURRENT_UID ?= $(shell id -u)
DETACHED_MODE ?= -d

.PHONY: install docker-up test hooks vendors

install: vendors event/vendor

docker-up: var/logs/.docker-build data docker-compose.override.yml
	CURRENT_UID=$(CURRENT_UID) docker-compose up $(DETACHED_MODE)

docker-down:
	CURRENT_UID=$(CURRENT_UID) docker-compose down

var/logs/.docker-build: docker-compose.yml docker-compose.override.yml $(shell find docker -type f)
	CURRENT_UID=$(CURRENT_UID) docker-compose build
	touch var/logs/.docker-build

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

configs/application/config.php:
	cp configs/application/config.php.dist-docker configs/application/config.php

app/config/parameters.yml:
	cp app/config/parameters.yml.dist-docker app/config/parameters.yml

config: configs/application/config.php app/config/parameters.yml
	CURRENT_UID=$(CURRENT_UID) docker-compose run --rm cliphp make vendors
	CURRENT_UID=$(CURRENT_UID) docker-compose run --rm cliphp make assets

test:
	./bin/atoum
	./bin/php-cs-fixer fix --dry-run -vv

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
