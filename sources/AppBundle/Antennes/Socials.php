<?php

declare(strict_types=1);

namespace AppBundle\Antennes;

/**
 * @readonly
 */
final class Socials
{
    public ?string $youtube;
    public ?string $blog;
    public ?string $twitter;
    public ?string $linkedin;
    public ?string $bluesky;

    public function __construct(
        ?string $youtube,
        ?string $blog,
        ?string $twitter,
        ?string $linkedin,
        ?string $bluesky
    ) {
        $this->youtube = $youtube;
        $this->blog = $blog;
        $this->twitter = $twitter;
        $this->linkedin = $linkedin;
        $this->bluesky = $bluesky;
    }
}
