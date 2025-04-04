<?php

declare(strict_types=1);

namespace AppBundle\VideoNotifier;

/**
 * @readonly
 */
final class CountResult
{
    public int $talkId;
    public int $quantity;

    public function __construct(string $talkId, string $quantity)
    {
        $this->talkId = (int) $talkId;
        $this->quantity = (int) $quantity;
    }
}
