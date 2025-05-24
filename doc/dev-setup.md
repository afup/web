# Setup de l'environnement de dev

Ce document contient des instructions sur la mise en place d'une instance de développement du site afup.org.

## Table des matières

1. [Pré-requis](#pré-requis)
2. [Installation](#installation)
   - [Variables d'environnement](#variables-denvironnement)
   - [Options Docker](#options-docker)
   - [Processeurs ARM](#processeurs-arm)
3. [Base de données](#base-de-données)
4. [Tests](#tests)
   - [Behat](#behat)
   - [Test unitaires](#test-unitaires)
5. [Qualité du code](#qualité-du-code)
   - [PHP-CS-Fixer](#php-cs-fixer)
   - [PHPStan](#phpstan)
6. [Outils externes](#outils-externes)
   - [Paybox](#paybox)
   - [GitHub](#github)
   - [Google Map Geocoding](#google-map-geocoding)

## Pré-requis

1. [Docker](https://docs.docker.com/get-started/get-docker/)
2. [Docker Compose](https://docs.docker.com/compose/install/)
3. [Git](https://git-scm.com/book/en/v2/Getting-Started-Installing-Git)

## Installation

1. Créer un fork du repository sur votre compte GitHub
2. Cloner ce fork avec git :

```bash
git clone https://github.com/my-account/afup-web.git
```

3. Lancer `make install` pour lancer le projet

Le site devrait maintenant être accessible en local :

- Le site : https://localhost:9205
- Mailcatcher: http://localhost:1181

La base de test inclus un utilisateur administration avec les identifiants suivants :
- username : `admin@admin.fr`
- password : `admin`

### Variables d'environnement

Plusieurs possibilités de configuration des containers sont disponibles, via l'utilisation de variables d'environnement.

Pour faciliter leur configuration, un fichier `.env` est créé à la racine du projet à la première exécution de la commande `make install`.
Ce fichier contient la liste des options disponibles.

### Options Docker

Le fichier `compose.override.yml` est créé automatiquement à l'installation du projet.

Par exemple, les ports utilisés pour le site et mailcatcher peuvent être modifiés dans ce fichier.

### Processeurs ARM

Pour faire fonctionner les images des bases de données (site et tests) sur un processeur ARM (par exemple sur Mac) il
faut ajouter une surcharge dans le fichier `compose.override.yml` :

```yaml
services:
  db:
    platform: linux/amd64
  dbtest:
    platform: linux/amd64
```

## Base de données

Config par défaut :

- user : `afup`
- pass : `afup`
- host : `localhost`
- port : `3606`
- database : `web`

La base de donnée est accessible via le script `docker/bin/mysql`.

## Tests

L'exécution des tests nécessite que le projet soit lancé via Docker.

### Behat

Behat est utilisé pour tester les différentes pages du site (front et backoffice).

Il faut se connecter au container via `make console` puis :

```bash
./bin/behat
```

ou

```bash
make behat
```

> [!TIP]
> Il est possible de spécifier un test dans la ligne de commande :
> ```bash
> # Une feature entière
> ./bin/behat tests/behat/features/Admin/Events/Planning.feature
> 
> # Un scénario spécifique
> ./bin/behat tests/behat/features/Admin/Events/Planning.feature:14
> ```

Une alternative est d'utiliser la commande `make test-functional`, attention cette commande arrête les containeurs de
tests à la fin de l'exécution de la suite de test.

Si par la suite, vous souhaitez lancer un test, il faut bien penser à les allumer à nouveau.

### Tests unitaires

Les tests unitaires sont écrits via PHPUnit.

```bash
make unit-test
```

### Tests d'intégration

Ces tests sont écrits via PHPUnit et utilisent le kernel de Symfony : https://symfony.com/doc/current/testing.html#integration-tests

```bash
make test-integration
```

Une alternative est d'utiliser la commande `make test-integration`, attention cette commande arrête les containeurs de
tests à la fin de l'exécution de la suite de test.

Si par la suite, vous souhaitez lancer un test, il faut bien penser à les allumer à nouveau.

## Qualité du code

### PHP-CS-Fixer

Cet outil sert à s'assurer de la mise en forme du PHP.

Pour afficher les erreurs :
```
make cs-lint
```

Pour corriger les erreurs :
```
make cs-fix
```

### PHPStan

Cet outil sert à vérifier les erreurs de typage dans le PHP.

PHPStan est lancé via un container Docker dédié pour pouvoir utiliser la dernière version sans avoir de conflit avec
la version PHP du projet.

```
make phpstan
```

## Outils externes

### Paybox

Il est possible de tester les paiements Paybox en environnement de développement.
Pour cela, les identifiant, site et rang [de test](https://www.paybox.com/espace-integrateur-documentation/comptes-de-tests/) sont déjà configurés dans le fichier `.env` par défaut.

Ensuite pour le paiement, il faut utiliser ces informations [de carte](https://www.paybox.com/espace-integrateur-documentation/cartes-de-tests/) (celle _"Carte participant au programme 3-D Secure (enrôlée)"_) :
* Numéro de carte : `1111222233334444`
* Validité : `12/25`
* CVV : `123`

#### Callbacks de paiement

Après le paiement, paybox effectue un retour sur le serveur et c'est suite à ce retour que l'on effectue des actions
comme l'ajout de la cotisation.

Afin d'en simplifier l'appel, il existe une commande dédiée qui s'appelle comme cela, où l'argument en exemple
correspond à l'URL de la page de retour sur le site après paiement.

```
bin/console dev:callback-paybox-cotisation "https://localhost:9206/association/paybox-redirect?total=3000&cmd=C2020-150120201239-0-770-GALLO-E4F&autorisation=XXXXXX&transaction=588033888&status=00000"
```

### GitHub

La connection GitHub est utilisée pour le CFP.

### Créer une application GitHub

Aller sur [Register a new OAuth app](https://github.com/settings/applications/new)

Créer une application avec ces paramètres :
* Application name: `AFUP/Web dev`
* Homepage URL: `https://localhost:9205/`
* Authorization callback URL: `https://localhost:9205/connect/github/check`

Valider avec le bouton `Register application`.

Récupérer le `Client ID`et le `Client secret`.

Mettre ces 2 informations dans le fichier `.env` :

```dotenv
GITHUB_CLIENT_ID=<Client ID GitHub>
GITHUB_CLIENT_SECRET=<Client secret GitHub>
```

### Google Map Geocoding

Cet outil est utilisé pour les exports des inscriptions.

Aller sur [Google Cloud Platform Console](https://console.cloud.google.com/projectcreate)

Créer un projet avec ces paramètres :
* Nom du projet: `AFUP/Web dev`
* Valider avec le bouton `Créer`

Puis dans `Identifiants`, `Créer des identifiants`, récupérer la clé.

Mettre cette information dans le fichier `.env` :

```dotenv
GOOGLE_MAPS_API_KEY=<Clé API Google>
```

Puis dans `API et services` activer l'API `Geocoding API`.

### Bluesky

Pour pouvoir poster des replays sur Bluesky, il faut créer un mot de passe d'application : https://bsky.app/settings/app-passwords

Ensuite, il faut configurer l'identifier et ce mot de passe dans le fichier `.env` :

```dotenv
BLUESKY_API_IDENTIFIER=example.bsky.social
BLUESKY_API_APP_PASSWORD=my-app-passwod
```
