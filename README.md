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
* effectuer un `make config` pour la copie des fichiers de config par défaut et l'installation des dépendances

## Base de données

Utiliser le compte `root:root`

* Récupérer un dump de la base (demander au pôle outils @ afup.org) et le placer décompressé à la racine du projet
* Récupérer le port MySQL du container `db` : `docker-compose port db 3306`
* Importer le dump : `mysql -h 127.0.0.1 -P <port> -u root -p < dump.sql`
* Il faut une instance Algolia pour le `algolia_app_id`, `algolia_backend_api_key` et `algolia_frontend_api_key` (contacter le pôle outil)