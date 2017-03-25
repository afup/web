CURRENT_UID=$(shell id -u)

.PHONY: install docker-up test hooks

install: vendor

docker-up: var/logs/.docker-build data docker-compose.override.yml
	CURRENT_UID=$(CURRENT_UID) docker-compose up

var/logs/.docker-build: docker-compose.yml docker-compose.override.yml $(shell find docker -type f)
	CURRENT_UID=$(CURRENT_UID) docker-compose build
	touch var/logs/.docker-build

docker-compose.override.yml:
	cp docker-compose.override.yml-dist docker-compose.override.yml

vendor: composer.phar composer.lock
	php composer.phar install

composer.phar:
	$(eval EXPECTED_SIGNATURE = "$(shell wget -q -O - https://composer.github.io/installer.sig)")
	$(eval ACTUAL_SIGNATURE = "$(shell php -r "copy('https://getcomposer.org/installer', 'composer-setup.php'); echo hash_file('SHA384', 'composer-setup.php');")")
	@if [ "$(EXPECTED_SIGNATURE)" != "$(ACTUAL_SIGNATURE)" ]; then echo "Invalid signature"; exit 1; fi
	php composer-setup.php
	rm composer-setup.php

configs/application/config.php:
	cp configs/application/config.php.dist-docker configs/application/config.php

app/config/parameters.yml:
	cp app/config/parameters.yml.dist-docker app/config/parameters.yml

config: configs/application/config.php app/config/parameters.yml
	CURRENT_UID=$(CURRENT_UID) docker-compose run --rm cliphp make vendor

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

