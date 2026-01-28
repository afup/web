# Site web de l'AFUP

Code source pour le site [afup.org](https://afup.org).

## Setup de dev

Retrouvez la documentation pour le setup de l'environnement de dev dans la [documentation dédiée](./doc/dev-setup.md).

### PHP local via Symfony CLI

- Un fichier `.php-version` existe à la racine avec `8.2` pour que la Symfony CLI sélectionne PHP 8.2 localement. Ce fichier devra être mis à jour lors des bump de la version de PHP.
- Le Makefile détecte automatiquement `symfony` : s'il est présent, les commandes locales passent par `symfony php` et `symfony composer`; sinon il conserve `php` et `composer` du système.
- Les commandes Docker restent inchangées (le PHP du conteneur est utilisé).

## Contribution

Toute contribution est la bienvenue, vous trouverez les informations dans le fichier [CONTRIBUTING.md](./CONTRIBUTING.md).

## Sécurité

Si vous trouvez un souci de sécurité, merci de lire [SECURITY.md](./SECURITY.md).
