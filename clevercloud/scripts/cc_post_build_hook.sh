#!/bin/bash -ex

./node_modules/.bin/webpack -p
php bin/phinx migrate
php bin/console asset-map:compile
