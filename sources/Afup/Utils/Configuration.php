<?php

declare(strict_types=1);

namespace Afup\Site\Utils;

use Symfony\Component\Yaml\Yaml;

define('EURO', '€');

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

        $sfParameters = $this->loadSymfonyParameters();
        $parameters = [
            'database_host', 'database_name', 'database_user', 'database_password', 'database_port',
            'smtp_host', 'smtp_port', 'smtp_tls', 'smtp_username', 'smtp_password',
            'mailer_force_recipients', 'mailer_bcc',
        ];

        foreach ($parameters as $param) {

            // env var exist ?
            if (false !== $value = getenv(strtoupper($param))) {
                self::$values[$param] = $value;
            }

            // override by parameter_ENV.yaml ?
            if (isset($sfParameters[$param])) {
                self::$values[$param] = $sfParameters[$param];
            }
        }
    }

    private function loadSymfonyParameters(): array
    {
        $parameters = [];
        $basePath = __DIR__ . '/../../../app/config';

        if (isset($_ENV['SYMFONY_ENV'])) {
            $file = $basePath . '/config_' . $_ENV['SYMFONY_ENV'] . '.yml';
            if (is_file($file)) {
                $values = Yaml::parseFile($file);
                if (isset($values['parameters'])) {
                    $parameters = array_merge($parameters, $values['parameters']);
                }
            }
        }

        return $parameters;
    }

    public function obtenir($cle)
    {
        return self::$values[$cle] ?? null;
    }
}
