# Site web de l'AFUP

## Applications

On accède aux applications via docker et les différents ports des applications.
Vous retrouverez les ports dans le fichier `docker-compose.override.yml`

Par défaut:
* Site AFUP : <https://localhost:9205/>
* Planète PHP : <https://localhost:9215/>
* Event : <https://localhost:9225/>
* Mailcatcher: <https://localhost:1181/>

_Les ports utilisés peuvent être modifiés dans le fichier `docker-compose.override.yml`._

## Mise en place avec docker

* cloner le dépot
* effectuer un `make docker-up` pour la création de l'infrastructure sous docker
* effectuer un `make init` pour la copie des fichiers de config par défaut, l'installation des dépendances ainsi que l'init de la base de donnée avec les données de test.

La base de test inclus un utilisateur administration avec les identifiants suivants:
- user: admin@admin.fr
- pass: admin

## Base de données

Config par défaut:
- user: afup
- pass: afup
- host: localhost
- port: 3606
- database: web