<?php

declare(strict_types=1);

namespace PlanetePHP;

/**
 * @readonly
 */
final class CrawlingResult
{
    public int $saved;
    public int $tooOld;
    public array $failedFeedsIds;

    /**
     * @param array<int> $failedFeedsIds
     */
    public function __construct(int $saved, int $tooOld, array $failedFeedsIds)
    {
        $this->saved = $saved;
        $this->tooOld = $tooOld;
        $this->failedFeedsIds = $failedFeedsIds;
    }
}
