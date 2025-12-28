<?php

use Afup\Tests\Behat\Bootstrap\FeatureContext;
use Behat\Config\Config;
use Behat\Config\Extension;
use Behat\Config\Profile;
use Behat\Config\Suite;
use Behat\MinkExtension\Context\MinkContext;
use Behat\MinkExtension\ServiceContainer\MinkExtension;

return (new Config())
    ->withProfile(
        (new Profile('default'))
            ->withExtension(new Extension(MinkExtension::class, [
                'base_url' => 'https://apachephptest:80',
                'files_path' => '%paths.base%/tests/behat/files',
                'browserkit_http' => [
                    'http_client_parameters' => [
                        'verify_peer' => false,
                        'verify_host' => false,
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
