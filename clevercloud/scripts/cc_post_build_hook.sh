#!/bin/bash -ex

composer sass
php bin/phinx migrate
php bin/console tailwind:build --minify
php bin/console asset-map:compile
