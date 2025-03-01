<?php

declare(strict_types=1);

namespace AppBundle\SocialNetwork;

/**
 * @readonly
 */
final class Status
{
    public string $text;
    public ?Embed $embed;

    public function __construct(string $text, ?Embed $embed = null)
    {
        $this->text = $text;
        $this->embed = $embed;
    }
}
