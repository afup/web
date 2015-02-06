Site web de l'AFUP
=========


Dépendances
-----------

MySQL


Installation
------------

### Copie du fichier de config

```
cp configs/application/config.php.dist configs/application/config.php
```

### Configurer les paramètres de BDD et de chemin
```php
$configuration['bdd']['hote']='localhost';
$configuration['bdd']['base']='afup_dev';
$configuration['bdd']['utilisateur']='afup_dev';
$configuration['bdd']['mot_de_passe']='';
$configuration['web']['path']='http://afup.home/';
```

### Configuration /etc/hosts

```
127.0.0.1       planete-php.home
127.0.0.1       afup.home
```

### Configuration Apache 2.4

```
<VirtualHost *:80>
    ServerName afup.home
    DocumentRoot $PATH_TO/htdocs
	<Directory />
		Options FollowSymLinks
		AllowOverride None
		Require all granted
	</Directory>
    <Directory $PATH_TO/htdocs>
		Options +Indexes +FollowSymLinks -MultiViews
		AllowOverride All
		Require all granted
    </Directory>
</VirtualHost>

<VirtualHost *:80>
    ServerName planete-php.home
    DocumentRoot $PATH_TO/git/htdocs/pages/planete
	<Directory />
		Options FollowSymLinks
		AllowOverride None
		Require all granted
	</Directory>
    <Directory $PATH_TO/htdocs/pages/planete>
		Options +Indexes +FollowSymLinks -MultiViews
		AllowOverride All
		Require all granted
    </Directory>
</VirtualHost>
```

### Import de fichier SQL

```
mysql afup_web < sql/*.sql
```

### Creation du répertoire de cache

```
mkdir -p  htdocs/cache/templates
```


Applications
-----------

* Backoffice  ( admin/admin) : afup.home/pages/administration/
* Site AFUP : afup.home/pages/site/
* Forum PHP 2015 : afup.home/pages/forumphp2015/
* PHP Tour 2015 : afup.home/pages/phptourluxembourg2015/
* Planete : planete-php.home

