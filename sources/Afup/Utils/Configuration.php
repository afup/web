<?php

declare(strict_types=1);

namespace Afup\Site\Utils;

/**
 * Classe de gestion de la configuration
 */
class Configuration
{
    private static $values;

    public function __construct()
    {
        if (self::$values) {
            return;
        }

        $parameters = [
            'database_host', 'database_name', 'database_user', 'database_password', 'database_port',
            'smtp_host', 'smtp_port', 'smtp_tls', 'smtp_username', 'smtp_password',
        ];

        foreach ($parameters as $param) {

            // env var exist ?
            if (false !== $value = getenv(strtoupper($param))) {
                self::$values[$param] = $value;
            }
        }
    }

    public function obtenir($cle)
    {
        return self::$values[$cle] ?? null;
    }
}
