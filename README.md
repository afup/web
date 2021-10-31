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
* `docker/bin/mysql` : connexion à la base de données.
* `docker/bin/bash` : PHP 5.6 cli.
* `docker/bin/bashphp7` : PHP 7.0 cli.

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

# Paiements avec Paybox

Il est possible de tester les paiements Paybox en environnement de développement.
Pour cela, les identifiant, site et rang [de test](www1.paybox.com/espace-integrateur-documentation/comptes-de-tests/) sont déjà configurés dans le fichier parameters.yml par défaut.

Ensuite pour le paiement il faut utiliser ces informations [de carte](http://www1.paybox.com/espace-integrateur-documentation/cartes-de-tests/) (celle _"Carte participant au programme 3-D Secure (enrôlée)"_) : 
* Numéro de carte : `1111 2222 3333 4444`
* Validité : `12/25`
* CVV : `123`
 
## Callbacks de paiement

### Après le paiement d'une cotisation

Après le paiement paybox effectue un retour sur le serveur et c'est suite à ce retour que l'on effectue des actions comme l'ajout de la cotisation. Afin d'en simplifier l'appel il existe une commande dédiée qui s'appelle comme cela, où l'argument en exemple correspond à l'URL de la page de retour sur le site après paiement.  

```
bin/console dev:callback-paybox-cotisation "https://localhost:9206/association/paybox-redirect?total=3000&cmd=C2020-150120201239-0-770-GALLO-E4F&autorisation=XXXXXX&transaction=588033888&status=00000"
```

### Wordpress event.afup.org
Le blog wordpress est géré à l'aide de composer.

Pour mettre à jour les dépendances, il faut utiliser la commande suivante: `docker run --rm --entrypoint php --user $(id -u):$(id -g) --volume $(pwd):/project herloct/composer:1.4.2-php5.6 -ddate.timezone=Europe/Paris /usr/local/bin/composer update --ignore-platform-reqs`

