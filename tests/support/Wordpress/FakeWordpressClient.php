<?php

declare(strict_types=1);

namespace Afup\Tests\Support\Wordpress;

use AppBundle\Event\Entity\Interview;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Wordpress\Dto\Category;
use AppBundle\Event\Wordpress\WordpressClient;

final class FakeWordpressClient implements WordpressClient
{
    public function listCategories(): array
    {
        return [
            new Category(1, 'Forum 1337', 'forum-1337'),
        ];
    }

    public function persistInterview(Interview $interview, Event $event, array $speakers, array $talks): ?int
    {
        return 123;
    }
}
