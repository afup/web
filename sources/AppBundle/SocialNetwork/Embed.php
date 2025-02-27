<?php

declare(strict_types=1);

namespace AppBundle\SocialNetwork;

/**
 * @readonly
 */
final class Embed
{
    public string $url;
    public string $title;
    public string $abstract;
    public ?string $imageUrl;

    public function __construct(string $url, string $title, string $abstract, ?string $imageUrl)
    {
        $this->url = $url;
        $this->title = $title;
        $this->abstract = $abstract;
        $this->imageUrl = $imageUrl;
    }
}
