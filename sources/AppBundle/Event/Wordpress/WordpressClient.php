<?php

declare(strict_types=1);

namespace AppBundle\Event\Wordpress;

use AppBundle\Event\Entity\Interview;
use AppBundle\Event\Entity\Speaker;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Talk;
use AppBundle\Event\Wordpress\Dto\Category;
use AppBundle\Event\Wordpress\Dto\PersistedInterview;

interface WordpressClient
{
    /**
     * @return array<Category>
     */
    public function listCategories(): array;

    /**
     * @param array<Speaker> $speakers
     * @param array<Talk>    $talks
     */
    public function persistInterview(Interview $interview, Event $event, array $speakers, array $talks): PersistedInterview;
}
