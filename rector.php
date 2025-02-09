<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Symfony\Set\SymfonySetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/app',
        __DIR__ . '/db',
        __DIR__ . '/htdocs',
        __DIR__ . '/sources',
        __DIR__ . '/tests',
    ])
    ->withSkip([
        __DIR__ . '/htdocs/cache',
    ])
    ->withPhp74Sets()
    ->withTypeCoverageLevel(PHP_INT_MAX)
    ->withDeadCodeLevel(PHP_INT_MAX)
    ->withCodeQualityLevel(PHP_INT_MAX)
    ->withImportNames(true, true, false)
    ->withSymfonyContainerXml(__DIR__ . '/var/cache/dev/appDevDebugProjectContainer.xml')
    ->withSets([
        SymfonySetList::SYMFONY_44,
        SymfonySetList::SYMFONY_CODE_QUALITY,
        SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION,
    ])
;
