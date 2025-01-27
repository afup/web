<?php

declare(strict_types=1);

/**
 * On charge les parameters depuis le .env ou .env.dist avec une conversion de nom en minuscule.
 * À la migration vers Symfony 4+ ceci ne sera plus utile.
 * On fait ça à cause du bundle Ting qui ne peut utiliser les "%env()%" comme paramètre.
 */
$path = dirname(__FILE__, 3);
$envFile = is_file($path . '/.env') ? $path . '/.env' : $path . '/.env.dist';
$lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $line) {
    if (strpos(trim($line), '#') === 0) {
        continue;
    }

    [$name, $default] = explode('=', $line, 2);
    // On va chercher dans les variables d'env en premier
    if (!$value = getenv($name)) {
        $value = $default;
    }
    $name = strtolower(trim($name));
    $value = trim($value);
    if (is_numeric($value)) {
        $value = (int) $value;
    }

    $container->setParameter($name, $value);
}
