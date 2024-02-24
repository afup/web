#!/bin/bash

# Cela permer d'éviter cette erreur
# Composer 2.3.0 dropped support for PHP <7.2.5 and you are running 7.0.33, please upgrade PHP or use Composer 2.2 LTS via "composer self-update --2.2". Aborting.
# une fois migré sur une version >7.2 on pourra supprimer cela

curl https://getcomposer.org/download/2.2.22/composer.phar -o /usr/bin/composer.phar
