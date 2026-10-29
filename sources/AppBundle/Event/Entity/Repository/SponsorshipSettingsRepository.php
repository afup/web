<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity\Repository;

use AppBundle\Doctrine\EntityRepository;
use AppBundle\Event\Entity\SponsorshipSettings;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<SponsorshipSettings>
 */
final class SponsorshipSettingsRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SponsorshipSettings::class);
    }

    public function get(): SponsorshipSettings
    {
        return $this->find(SponsorshipSettings::ID) ?? new SponsorshipSettings();
    }
}
