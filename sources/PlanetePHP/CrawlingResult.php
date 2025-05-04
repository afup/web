<?php

declare(strict_types=1);

namespace PlanetePHP;

final readonly class CrawlingResult
{
    /**
     * @param array<int> $failedFeedsIds
     */
    public function __construct(
        public int $saved,
        public int $tooOld,
        public array $failedFeedsIds,
    ) {
    }
}
