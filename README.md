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

### Configurer les paramaetres de BDD et de path ( 6 pre)
```php
$configuration['bdd']['hote']='localhost';
$configuration['bdd']['base']='afup_web';
$configuration['bdd']['utilisateur']='root';
$configuration['bdd']['mot_de_passe']='';
$configuration['web']['path']='/';
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

* Backoffice  ( admin/admin) : /pages/administration/
* Site AFUP : /pages/site/
* Forum PHP 2013 : /pages/forumphp2013/
* PHP Tour 2014 : /pages/phptourlyon2014/

