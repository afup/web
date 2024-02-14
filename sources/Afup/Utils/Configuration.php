<?php

namespace Afup\Site\Utils;

use Symfony\Component\Yaml\Yaml;

define('EURO', '€');

/**
 * Classe de gestion de la configuration
 */
class Configuration
{
    /**
     * Valeurs de configuration
     */
    private $_valeurs;

    /**
     * Charge les valeurs depuis le fichier de configuration
     */
    public function __construct()
    {
        $sfParameters = $this->loadSymfonyParameters();
        if ([] !== $sfParameters) {
            $this->_valeurs['database_host'] = $sfParameters['database_host'] ?? '';
            $this->_valeurs['database_name'] = $sfParameters['database_name'] ?? '';
            $this->_valeurs['database_user'] = $sfParameters['database_user'] ?? '';
            $this->_valeurs['database_password'] = $sfParameters['database_password'] ?? '';

            $this->_valeurs['smtp_host'] = $sfParameters['smtp_host'] ?? '';
            $this->_valeurs['smtp_port'] = $sfParameters['smtp_port'] ?? '';
            $this->_valeurs['smtp_tls'] = $sfParameters['smtp_tls'] ?? '';
            $this->_valeurs['smtp_username'] = $sfParameters['smtp_username'] ?? '';
            $this->_valeurs['smtp_password'] = $sfParameters['smtp_password'] ?? '';

            $this->_valeurs['mailer_force_recipients'] = $sfParameters['mailer_force_recipients'] ?? '';
            $this->_valeurs['mailer_bcc'] = $sfParameters['mailer_bcc'] ?? '';
        }
    }

    private function loadSymfonyParameters(): array
    {
        $basePath = __DIR__ . '/../../../app/config';

        $parameters = [];
        $this->mergeSymfonyParametersFromFile($basePath . '/parameters.yml', $parameters);
        $this->mergeSymfonyParametersFromFile($basePath . '/config.yml', $parameters);
        if (isset($_ENV['SYMFONY_ENV'])) {
            $this->mergeSymfonyParametersFromFile($basePath . '/config_' . $_ENV['SYMFONY_ENV'] . '.yml', $parameters);
        }

        return $parameters;
    }

    private function mergeSymfonyParametersFromFile($file, &$parameters)
    {
        if (is_file($file)) {
            $values = Yaml::parseFile($file);
            if (isset($values['parameters'])) {
                $parameters = array_merge($parameters, $values['parameters']);
            }
        }
    }

    /**
     * Renvoie la valeur correspondant à la clé
     */
    public function obtenir($cle)
    {
        return $this->_valeurs[$cle];
    }

}
