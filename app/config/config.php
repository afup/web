<?php
/**
 * On charge les parameters depuis le .env avec une conversion de nom en minuscule.
 * À la migration vers Symfony 4+ ceci ne sera plus utile.
 * On fait ça à cause du bundle Ting qui ne peut utiliser les "%env()%" comme paramètre.
 */
$lines = file(dirname(__FILE__, 3).'/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $line) {

    if (strpos(trim($line), '#') === 0) {
        continue;
    }

    list($name, $value) = explode('=', $line, 2);
    $name = strtolower(trim($name));
    $value = trim($value);
    if (is_numeric($value)) {
        $value = (int) $value;
    }

    $container->setParameter($name, $value);
}
