# Site web de l'AFUP

## Applications

On accède aux applications via docker et les différents ports des applications.
Vous retrouverez les ports dans le fichier `docker-compose.override.yml`

Par défaut:
* Site AFUP : <https://localhost:9205/>
* Planète PHP : <https://localhost:9215/>
* Event : <https://localhost:9225/>
* Mailcatcher: <http://localhost:1181/>

_Les ports utilisés peuvent être modifiés dans le fichier `docker-compose.override.yml`._

## Mise en place avec docker

* cloner le dépot
* effectuer un `make docker-up` pour la création de l'infrastructure sous docker
* effectuer un `make init` pour la copie des fichiers de config par défaut, l'installation des dépendances ainsi que l'init de la base de donnée avec les données de test.

La base de test inclus un utilisateur administration avec les identifiants suivants:
- user: admin@admin.fr
- pass: admin

Vous pouvez aussi y accéder directement via la commande: `docker/bin/mysql`

### Autres commandes

* `make docker-stop` : éteint les containers en fonctionnement.
* `make docker-down` : détruit les containers existants.

### Configuration avancée

Plusieurs possibilités de configuration des containers sont disponibles, via l'utilisation de variables d'environnement.

Pour faciliter leur configuration, un fichier `.env` est créé à la racine du projet à la première exécution de la commande `make docker-up`.
Ce fichier contient la liste des options disponibles.

#### `DOCKER_UP_OPTIONS`

liste des options à passer à la commande `docker-composer up`. 

## Base de données

Config par défaut:
- user: afup
- pass: afup
- host: localhost
- port: 3606
- database: web
