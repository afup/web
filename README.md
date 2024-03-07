# Site web de l'AFUP

## Applications

On accède aux applications via docker et les différents ports des applications.
Vous retrouverez les ports dans le fichier `compose.override.yml`

Par défaut:
* Site AFUP : <https://localhost:9205/>
* Planète PHP : <https://localhost:9215/>
* Mailcatcher: <http://localhost:1181/>

_Les ports utilisés peuvent être modifiés dans le fichier `compose.override.yml`._

## Mise en place avec docker

* cloner le dépot
* effectuer un `make docker-up` pour la création de l'infrastructure sous docker
* effectuer un `make init` pour la copie des fichiers de config par défaut, l'installation des dépendances ainsi que l'init de la base de donnée avec les données de test.

La base de test inclus un utilisateur administration avec les identifiants suivants:
- user: admin@admin.fr
- pass: admin

Vous pouvez aussi y accéder directement via la commande: `docker/bin/mysql`

### Autres commandes

* `make docker-up` : allume les containers.
* `make docker-stop` : éteint les containers en fonctionnement.
* `make docker-down` : détruit les containers existants.
* `docker/bin/mysql` : connexion à la base de données.
* `docker/bin/bash` : PHP cli.

### Configuration avancée

Plusieurs possibilités de configuration des containers sont disponibles, via l'utilisation de variables d'environnement.

Pour faciliter leur configuration, un fichier `.env` est créé à la racine du projet à la première exécution de la commande `make docker-up`.
Ce fichier contient la liste des options disponibles.

#### `DOCKER_UP_OPTIONS`

liste des options à passer à la commande `docker composer up`. 

## Base de données

Config par défaut:
- user: afup
- pass: afup
- host: localhost
- port: 3606
- database: web

# Tests

Il est possible de lancer les divers tests unitaires et fonctionnels à partir des containers.

Pre-requis : valider que les containers utilisés par les tests sont allumés, il s'agit des containers `dbtest`, `apachephptest` et `mailcatcher`. S'il ne sont pas allumés, il est possible de le faire via `make docker-up`.

Lancement des tests unitaires : 
- Se connecter dans le conteneur php `docker/bin/bash`
- Lancer les tests et valider le code :
```
	./bin/atoum
	./bin/php-cs-fixer fix --dry-run -vv
```
- Une alternative est d'utiliser la commande `make test` qui effectuer la même action.

Lancement des tests fonctionnels : 
- Se connecter dans le conteneur php `docker/bin/bash`
- Lancer les tests pour le site web :
```
	./bin/behat
```
- Lancer les tests pour le site Planete PHP :
```
	./bin/behat -c behat-planete.yml
```
- Une alternative est d'utiliser la commande `make test-functional`, attention cette commande arrête les containeurs de tests à la fin de l'exécution de la suite de test. Si par la suite vous souhaitez lancer un test, il faut bien penser à les allumer de nouveau.

Dans chacun des cas, il est possible de spécifier un test dans la ligne de commande. Exemple: `./bin/behat tests/behat/features/Admin/AdminFeuilles.feature`

# Paiements avec Paybox

Il est possible de tester les paiements Paybox en environnement de développement.
Pour cela, les identifiant, site et rang [de test](https://www.paybox.com/espace-integrateur-documentation/comptes-de-tests/) sont déjà configurés dans le fichier .env par défaut.

Ensuite pour le paiement il faut utiliser ces informations [de carte](https://www.paybox.com/espace-integrateur-documentation/cartes-de-tests/) (celle _"Carte participant au programme 3-D Secure (enrôlée)"_) : 
* Numéro de carte : `1111222233334444`
* Validité : `12/25`
* CVV : `123`
 
## Callbacks de paiement

### Après le paiement d'une cotisation

Après le paiement paybox effectue un retour sur le serveur et c'est suite à ce retour que l'on effectue des actions comme l'ajout de la cotisation. Afin d'en simplifier l'appel il existe une commande dédiée qui s'appelle comme cela, où l'argument en exemple correspond à l'URL de la page de retour sur le site après paiement.  

```
bin/console dev:callback-paybox-cotisation "https://localhost:9206/association/paybox-redirect?total=3000&cmd=C2020-150120201239-0-770-GALLO-E4F&autorisation=XXXXXX&transaction=588033888&status=00000"
```

## Connection GitHub (pour le CFP)

### Créer une application GitHub : 

Aller sur [Register a new OAuth application](https://github.com/settings/applications/new)

Créer une application avec ces paramètres :
* Application name: `AFUP/Web dev`
* Homepage URL: `https://localhost:9205/`
* Authorization callback URL: `https://localhost:9205/connect/github/check`

Valider avec le bouton `Register application` 

Récupérer le `Client ID`et le `Client secret`

Mettre ces 2 informations dans le fichier .env
```dotenv
# .env
GITHUB_CLIENT_ID=<Client ID GitHub>
GITHUB_CLIENT_SECRET=<Client secret GitHub>
```

## Connection Google Map Geocoding (pour les exports des Inscriptions)

Aller sur [Google Cloud Platform Console](https://console.cloud.google.com/projectcreate)

Créer un projet avec ces paramètres :
* Nom du projet: `AFUP/Web dev`
* Valider avec le bouton `Créer`

Puis dans `Identifiants`, `Créer des identifiants`, récupérer la clé

Mettre cette information dans le fichier
```dotenv
# .env
GOOGLE_MAPS_API_KEY=<Clé API Google>
```

Puis dans `API et services` activer l'API `Geocoding API`.
