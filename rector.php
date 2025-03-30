<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Symfony\Set\SymfonySetList;
use Rector\Symfony\Set\TwigSetList;

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
    ->withTypeCoverageLevel(10)
    ->withDeadCodeLevel(10)
    ->withCodeQualityLevel(10)
    ->withImportNames(true, true, false)
    ->withSymfonyContainerXml(__DIR__ . '/var/cache/dev/AppKernelDevDebugContainer.xml')
    ->withSets([
        SymfonySetList::SYMFONY_44,
        SymfonySetList::SYMFONY_50,
        SymfonySetList::SYMFONY_51,
        SymfonySetList::SYMFONY_52,
        SymfonySetList::SYMFONY_53,
        SymfonySetList::SYMFONY_54,
        SymfonySetList::SYMFONY_CODE_QUALITY,
        SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION,
        TwigSetList::TWIG_UNDERSCORE_TO_NAMESPACE
    ])
;
