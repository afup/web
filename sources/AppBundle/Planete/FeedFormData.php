<?php

declare(strict_types=1);

namespace AppBundle\Planete;

use PlanetePHP\FeedStatus;
use Symfony\Component\Validator\Constraints as Assert;

class FeedFormData
{
    #[Assert\NotBlank]
    public string $name;

    #[Assert\NotBlank]
    #[Assert\Url]
    public string $url = 'https://';

    #[Assert\NotBlank]
    #[Assert\Url]
    public string $feed = 'https://';

    public ?int $userId = null;

    #[Assert\Choice(choices: [FeedStatus::Inactive, FeedStatus::Active], strict: true)]
    public FeedStatus $status = FeedStatus::Active;
}
