<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity\Repository;

use AppBundle\Doctrine\EntityRepository;
use AppBundle\Event\Entity\SponsorScan;
use AppBundle\Event\Model\SponsorTicket;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<SponsorScan>
 */
final class SponsorScanRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SponsorScan::class);
    }

    /**
     * Liste des scans actifs d'un ticket sponsor, avec les informations du billet scanné.
     *
     * @return list<array<string, mixed>>
     */
    public function getBySponsorTicket(SponsorTicket $sponsorTicket): array
    {
        $sponsorTicketId = $sponsorTicket->getId();
        if ($sponsorTicketId === null) {
            throw new \InvalidArgumentException('Le ticket sponsor doit être enregistré');
        }

        $queryBuilder = $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('scan.id', 'inscription.nom', 'inscription.prenom', 'inscription.email', 'scan.created_on')
            ->from('afup_forum_sponsor_scan', 'scan')
            ->innerJoin('scan', 'afup_inscription_forum', 'inscription', 'inscription.id = scan.ticket_id')
            ->where('scan.sponsor_ticket_id = :sponsorTicketId')
            ->andWhere('scan.deleted_on IS NULL')
            ->orderBy('scan.created_on', 'DESC')
            ->setParameter('sponsorTicketId', $sponsorTicketId);

        /** @var list<array<string, mixed>> $scans */
        $scans = $queryBuilder->executeQuery()->fetchAllAssociative();

        return $scans;
    }
}
