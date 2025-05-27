<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Symfony\Set\SymfonySetList;
use Rector\Symfony\Set\TwigSetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/app',
        __DIR__ . '/db',
        __DIR__ . '/htdocs/pages',
        __DIR__ . '/sources',
        __DIR__ . '/tests',
    ])
    ->withTypeCoverageLevel(10)
    ->withDeadCodeLevel(10)
    ->withCodeQualityLevel(10)
    ->withImportNames(true, true, false)
    ->withSymfonyContainerXml(__DIR__ . '/var/cache/dev/AppKernelDevDebugContainer.xml')
    ->withComposerBased(
        twig: true,
        phpunit: true,
        symfony: true,
    )
    ->withSets([
        LevelSetList::UP_TO_PHP_82,
    ])
;
