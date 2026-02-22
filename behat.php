<?php

use Afup\Tests\Behat\Bootstrap\FeatureContext;
use Behat\Config\Config;
use Behat\Config\Extension;
use Behat\Config\Profile;
use Behat\Config\Suite;
use Behat\MinkExtension\Context\MinkContext;
use Behat\MinkExtension\ServiceContainer\MinkExtension;
use Robertfausk\Behat\PantherExtension\ServiceContainer\PantherExtension;

return (new Config())
    ->withProfile(
        (new Profile('default'))
            ->withExtension(new Extension(PantherExtension::class))
            ->withExtension(new Extension(MinkExtension::class, [
                'base_url' => 'https://apachephptest:80',
                'files_path' => '%paths.base%/tests/behat/files',
                'default_session' => 'browserkit_http',
                'javascript_session' => 'panther',
                'sessions' => [
                    'browserkit_http' => [
                        'browserkit_http' => [
                            'http_client_parameters' => [
                                'verify_peer' => false,
                                'verify_host' => false,
                            ],
                        ],
                    ],
                    'panther' => [
                        'panther' => [
                            'options' => [
                                'browser' => 'chrome',
                                'webServerDir' => '%paths.base%/htdocs',
                                'external_base_uri' => 'https://apachephptest:80',
                            ],
                            'manager_options' => [
                                'chromedriver_arguments' => [
                                    '--log-path=/var/www/html/chromedriver.log',
                                    '--verbose',
                                ],
                                'capabilities' => [
                                    'goog:chromeOptions' => [
                                        'args' => [
                                            '--headless',
                                            '--disable-gpu',
                                            '--no-sandbox',
                                            '--disable-dev-shm-usage',
                                            '--disable-extensions',
                                            '--ignore-certificate-errors',
                                        ],
                                    ],
                                ],
                                'external_base_uri' => 'https://apachephptest:80',
                            ],
                        ],
                    ],
                ],
            ]))
            ->withSuite(
                (new Suite('web_features'))
                    ->withContexts(
                        FeatureContext::class,
                        MinkContext::class
                    )
                    ->withPaths('%paths.base%/tests/behat')
            )
    );
