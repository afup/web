<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity\Repository;

use AppBundle\Doctrine\EntityRepository;
use AppBundle\Event\Entity\SpeakerSuggestion;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<SpeakerSuggestion>
 */
final class SpeakerSuggestionRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SpeakerSuggestion::class);
    }
}
