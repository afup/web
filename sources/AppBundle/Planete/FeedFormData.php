<?php

declare(strict_types=1);

namespace AppBundle\Planete;

use PlanetePHP\Feed;
use Symfony\Component\Validator\Constraints as Assert;

class FeedFormData
{
    /**
     * @var string
     */
    #[Assert\NotBlank]
    public $name;
    /**
     * @var string
     */
    #[Assert\NotBlank]
    #[Assert\Url]
    public $url = 'https://';
    /**
     * @var string
     */
    #[Assert\NotBlank]
    #[Assert\Url]
    public $feed = 'https://';
    /**
     * @var int|null
     */
    public $userId;
    /**
     * @var int
     */
    #[Assert\Choice(choices: [Feed::STATUS_INACTIVE, Feed::STATUS_ACTIVE], strict: true)]
    public $status = Feed::STATUS_ACTIVE;
}
