<?php

declare(strict_types=1);

namespace AppBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AppBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
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
        // On calcul l'offset de la timezone pour MySQL
        // car la base de données est en UTC et la base n'accepte que les offset.
        $container->setParameter('database_timezone', date('P'));

        parent::build($container);
    }
}
