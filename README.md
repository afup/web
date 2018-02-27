# Site web de l'AFUP

## Applications

* Backoffice (admin/admin) : <http://afup.dev/pages/administration/>
* Site AFUP : <http://afup.dev/pages/site/>
* Forum PHP 2015 : <http://afup.dev/pages/forumphp2015/>
* PHP Tour 2015 : <http://afup.dev/pages/phptourluxembourg2015/>

## Mise en place avec docker

* cloner le dépot
* effectuer un `make docker-up` pour la création de l'infrastructure sous docker
* effectuer un `make config` pour la copie des fichiers de config par défaut et l'installation des dépendances

_Les ports utilisés peuvent être modifiés dans le fichier `docker-compose.override.yml`._

## Base de données

Utiliser le compte `root:root`

* Récupérer un dump de la base (demander au pôle outils @ afup.org) et le placer décompressé à la racine du projet
* Récupérer le port MySQL du container `db` : `docker-compose port db 3306`
* Importer le dump : `mysql -h 127.0.0.1 -P <port> -u root -p < dump.sql`
* Il faut une instance Algolia pour le `algolia_app_id`, `algolia_backend_api_key` et `algolia_frontend_api_key` (contacter le pôle outil)

## Mise en place avec Vagrant (obsolète)

### Dépendances

* Vagrant >= 1.6
* VirtualBox (4.3) ou VMWare (nécessite le plugin vagrant)

Vous pouvez régler l'IP (par défaut `192.168.42.42`) si elle n'est pas bonne pour vous dans le Vagrantfile mais attention car il est tracké !
Le fichier `/etc/hosts` est modifié automatiquement à l'aide du plugin `hostmanager` à installer : `vagrant plugin install vagrant-hostmanager`

### Emails

Pour consulter les emails envoyés depuis la VM, allez sur Mailcatcher : <http://afup.dev:1080>.
Si Mailcatcher est fermé il faut le relancer : `vagrant ssh -- sudo service mailcatcher start`

**Depuis peu tous les emails sont remplacés par des templates Mandrill.** Ce qui a pour effet que les mails ne peuvent pas partir depuis la VM sauf si les identifiants Mandrill de production sont renseignés (car les templates ne sont disponibles que sur le compte de prod).

Vous pouvez cependant mettre vos identifiants de test dans le fichier de config du site : `/configs/application/config.php`.
Ainsi vous pourrez tester vos propres emails.

**Attention avec les emails, beaucoup de mails sont envoyés en copie au trésorier ou à communication. Soyez vigilants :)**

### Base de données

Il vous faut importer la base de données. Par défaut les utilisateurs sont les suivants :

* `root` / `mysql`
* `afup_dev` / `p455w0rd`

Et la base de données est `afup_dev`.

#### Import de la base de données

* Récupérer un dump de la base (demander au pôle outils @ afup.org) et le placer décompressé à la racine du projet
* Se connecter à sa machine guest : `vagrant ssh`
* Se placer dans le dossier synchronisé : `cd /vagrant`
* Exécuter la commande `mysql -u root -p afup_dev < {nom_du_dump}.sql`

> Mot de passe pour la commande en local: `mysql`
